<script setup lang="ts">
import { ref, onMounted, h } from "vue"
import axios from "@/axios/axios"
import { toTypedSchema } from "@vee-validate/zod"
import { useForm } from "vee-validate"
import * as z from "zod"

// shadcn-ui
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { ChevronRight } from "lucide-vue-next"
import BarangaySelector from "@/components/ui/select/barangay_selection/BarangaySelector.vue"
import { toast } from "vue-sonner"
import SelectContent from '@/components/ui/select/SelectContent.vue'
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue'
import Select from '@/components/ui/select/Select.vue'
import SelectValue from '@/components/ui/select/SelectValue.vue'
import SelectItem from '@/components/ui/select/SelectItem.vue'


interface PkpSite {
  site_id?: number
  barangay_id: number
  latitude: number
  longitude: number
  site_status: number
  target_purok: number
  target_sition: number
  no_household?: number | null
  population?: number | null

  barangay: {
    barangay_id: number
    barangay_name: string
    municipality: {
      municipality_id: number
      municipality_name: string
      province: {
        province_id: number
        province_name: string
      }
    }
  }
}


const siteStatusLabels: Record<number, string> = {
  1: "Preparation Phase",
  2: "Action Planning Done",
  3: "With organized PKT",
  4: "Implementing PK",
  5: "Monitored PK Implementation",
}

const formSchema = toTypedSchema(z.object({
  barangay_id: z.coerce.number().min(1, "Barangay is required"),
  latitude: z.coerce.number().min(-90).max(90, "Latitude must be between -90 and 90"),
  longitude: z.coerce.number().min(-180).max(180, "Longitude must be between -180 and 180"),
  site_status: z.coerce.number().int(),
  target_sition: z.coerce.number().int(),
  target_purok: z.coerce.number().int(),
  no_household: z.coerce.number().nullable().optional(),
  population: z.coerce.number().nullable().optional(),
}))

// State
const sites = ref<PkpSite[]>([])
const isDialogOpen = ref(false)
const isEditing = ref(false)
const editingId = ref<number | null>(null)



async function loadSites() {
  const res = await axios.get("/site/list")
  sites.value = res.data.data

}

onMounted(() => {
  loadSites()
})

const { handleSubmit, resetForm } = useForm<PkpSite>({
  validationSchema: formSchema,
  initialValues: {
    barangay_id: 0,
    latitude: 0,
    longitude: 0,
    site_status: 1,
    target_sition: 0,
    target_purok: 0,
    no_household: null,
    population: null,
  },
})




function openCreate() {
  isEditing.value = false
  editingId.value = null
  resetForm()
  isDialogOpen.value = true
}

function editSite(site: PkpSite) {
  isEditing.value = true
  editingId.value = site.site_id ?? null

  resetForm({
    values: {
      ...site,
      barangay_id: site.barangay_id,
    },
  })

  isDialogOpen.value = true
}

const saveSite = async (values: PkpSite) => {
  try {
    if (isEditing.value && editingId.value) {
      await axios.put(`/site/update`, values)
    } else {
      await axios.post("/site/create", values)
    }

    await loadSites()
    isDialogOpen.value = false
    toast.success(isEditing.value ? "Site updated." : "New site created.")
  } catch (e) {
    console.error("Save failed", e)
    toast.error("Could not save site.")
  }
}
const onSubmit = handleSubmit(saveSite)

async function removeSite(id?: number) {
  if (!id) return
  if (!confirm("Delete this site?")) return
  await axios.delete(`/site/delete`)
  await loadSites()
}

</script>

<template>
  <div class="p-4 space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold">PKP Sites</h2>
      <Button @click="openCreate">New Site</Button>
    </div>

    <!-- Sites Table -->
    <Card>
      <CardContent>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>ID</TableHead>
              <TableHead>Province</TableHead>
              <TableHead>Municipality</TableHead>
              <TableHead>Barangay</TableHead>
              <TableHead>Lat</TableHead>
              <TableHead>Lng</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Purok</TableHead>
              <TableHead>Sition</TableHead>
              <TableHead>Households</TableHead>
              <TableHead>Population</TableHead>
              <TableHead class="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="site in sites" :key="site.site_id">
              <TableCell>{{ site.site_id }}</TableCell>
              <TableCell>{{ site.barangay.municipality.province.province_name }}</TableCell>
              <TableCell>{{ site.barangay.municipality.municipality_name }}</TableCell>
              <TableCell>{{ site.barangay.barangay_name }}</TableCell>
              <TableCell>{{ site.latitude }}</TableCell>
              <TableCell>{{ site.longitude }}</TableCell>
              <TableCell>{{ siteStatusLabels[site.site_status] }}</TableCell>
              <TableCell>{{ site.target_purok }}</TableCell>
              <TableCell>{{ site.target_sition }}</TableCell>
              <TableCell>{{ site.no_household ?? "-" }}</TableCell>
              <TableCell>{{ site.population ?? "-" }}</TableCell>
              <TableCell class="text-right space-x-2">
                <Button size="sm" variant="ghost" @click="editSite(site)">Edit</Button>
                <Button size="sm" variant="destructive" @click="removeSite(site.site_id)">Delete</Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>

    <!-- Dialog / Form -->
    <Dialog v-model:open="isDialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ isEditing ? "Edit Site" : "Create Site" }}</DialogTitle>
        </DialogHeader>

        <form class="w-2/3 space-y-6" @submit.prevent="onSubmit">
          <!-- Barangay -->
          <FormField name="barangay_id" v-slot="{ field }">
            <FormItem>
              <FormLabel>Barangay</FormLabel>
              <FormControl>
                <BarangaySelector :model-value="field.value" @update:model-value="field.onChange" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>


          <!-- Latitude -->
          <FormField name="latitude" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Latitude</FormLabel>
              <FormControl>
                <Input type="number" step="0.00000001" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Longitude -->
          <FormField name="longitude" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Longitude</FormLabel>
              <FormControl>
                <Input type="number" step="0.00000001" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Status -->
          <FormField name="site_status" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Site Status</FormLabel>
              <FormControl>
                <Select v-bind="componentField">
                  <SelectTrigger>
                    <SelectValue placeholder="Select status" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem :value="1">Preparation Phase</SelectItem>
                    <SelectItem :value="2">Action Planning Done</SelectItem>
                    <SelectItem :value="3">With organized PKT</SelectItem>
                    <SelectItem :value="4">Implementing PK</SelectItem>
                    <SelectItem :value="5">Monitored PK Implementation</SelectItem>
                  </SelectContent>
                </Select>
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
          <FormField name="target_purok" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Target Purok</FormLabel>
              <FormControl>
                <Input type="number" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
          <FormField name="target_sition" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Target Sition</FormLabel>
              <FormControl>
                <Input type="number" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <div class="grid grid-cols-2 gap-2">
            <FormField name="no_household" v-slot="{ componentField }">
              <FormItem>
                <FormLabel>No. Households</FormLabel>
                <FormControl>
                  <Input type="number" v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>

            <FormField name="population" v-slot="{ componentField }">
              <FormItem>
                <FormLabel>Population</FormLabel>
                <FormControl>
                  <Input type="number" v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <Button variant="secondary" type="button" @click="isDialogOpen = false">Cancel</Button>
            <Button type="submit">{{ isEditing ? "Update" : "Create" }}</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>
