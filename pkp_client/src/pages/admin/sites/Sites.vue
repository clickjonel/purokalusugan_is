<script setup lang="ts">
import { ref, onMounted, computed } from "vue"
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
import BarangaySelector from "@/components/ui/select/barangay_selection/BarangaySelector.vue"
import { toast } from "vue-sonner"
import SelectContent from '@/components/ui/select/SelectContent.vue'
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue'
import Select from '@/components/ui/select/Select.vue'
import SelectValue from '@/components/ui/select/SelectValue.vue'
import SelectItem from '@/components/ui/select/SelectItem.vue'
import { ChevronRight, Edit, Trash2 } from 'lucide-vue-next'


interface PkpSite {
  site_id?: number
  barangay_id: number
  latitude: number
  longitude: number
  site_status: number
  target_purok: number
  target_sitio: number
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

const formSchema = toTypedSchema(
  z.object({
    site_id: z.coerce.number().optional(),
    barangay_id: z.coerce.number().optional(),
    latitude: z.coerce
      .number()
      .min(-90, "Latitude must be between -90 and 90")
      .max(90, "Latitude must be between -90 and 90"),
    longitude: z.coerce
      .number()
      .min(-180, "Longitude must be between -180 and 180")
      .max(180, "Longitude must be between -180 and 180"),
    site_status: z.coerce.number().int(),
    target_sitio: z.coerce.number().int(),
    target_purok: z.coerce.number().int(),
    no_household: z.coerce.number().nullable().optional(),
    population: z.coerce.number().nullable().optional(),
  })
)


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
function defaultPkpSite(): PkpSite {
  return {
    barangay_id: 0,
    latitude: 0,
    longitude: 0,
    site_status: 1,
    target_sitio: 0,
    target_purok: 0,
    no_household: null,
    population: null,
    barangay: {
      barangay_id: 0,
      barangay_name: "",
      municipality: {
        municipality_id: 0,
        municipality_name: "",
        province: {
          province_id: 0,
          province_name: "",
        },
      },
    },
  }
}
const { handleSubmit, resetForm } = useForm<PkpSite>({
  validationSchema: formSchema,
  initialValues: defaultPkpSite(),
})



function openCreate() {
  isEditing.value = false
  editingId.value = null
  resetForm({ values: defaultPkpSite() })
  isDialogOpen.value = true
}

function editSite(site: PkpSite) {
  isEditing.value = true
  editingId.value = site.site_id ?? null

  resetForm({
    values: {
      ...site,
      site_id: site.site_id,
      barangay_id: site.barangay_id,
    },
  })

  isDialogOpen.value = true
}

const saveSite = async (values: PkpSite) => {
  try {
    if (isEditing.value && editingId.value) {
      console.log('ito yung data', values)
      //'ito yung data',{"barangay_id": 37494,"latitude": 16.8879,"longitude": 120.6833,"site_status": 1,"target_sitio": 0,"target_purok": 0,"no_household": 2,"population": 2500
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

const searchQuery = ref("")
const currentPage = ref(1)
const pageSize = ref(12) // rows per page

// Filter sites by search
const filteredSites = computed(() => {
  if (!searchQuery.value) return sites.value
  return sites.value.filter((site) => {
    const search = searchQuery.value.toLowerCase()
    return (
      site.site_id?.toString().includes(search) ||
      site.barangay.barangay_name.toLowerCase().includes(search) ||
      site.barangay.municipality.municipality_name.toLowerCase().includes(search) ||
      site.barangay.municipality.province.province_name.toLowerCase().includes(search)
    )
  })
})

// Paginate filtered sites
const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredSites.value.length / pageSize.value))
)

const paginatedSites = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  return filteredSites.value.slice(start, start + pageSize.value)
})

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--
}

</script>

<template>
  <div class="p-4 space-y-4 w-full">
    <div class="p-4 space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">PKP Sites</h2>
        <div class="flex items-center gap-2">
          <!-- Search -->
          <Input v-model="searchQuery" type="text" placeholder="Search..." class="h-8 w-48 text-sm" />
          <Button @click="openCreate">New Site</Button>
        </div>
      </div>

      <!-- Sites Table -->
      <Card>
        <CardContent>
          <Table class="text-sm">
            <TableHeader>
              <TableRow class="h-8">
                <TableHead class="px-2 py-1">ID</TableHead>
                <TableHead class="px-2 py-1">Province</TableHead>
                <TableHead class="px-2 py-1">Municipality</TableHead>
                <TableHead class="px-2 py-1">Barangay</TableHead>
                <TableHead class="px-2 py-1">Lat</TableHead>
                <TableHead class="px-2 py-1">Lng</TableHead>
                <TableHead class="px-2 py-1">Status</TableHead>
                <TableHead class="px-2 py-1">Purok</TableHead>
                <TableHead class="px-2 py-1">Sitio</TableHead>
                <TableHead class="px-2 py-1">Households</TableHead>
                <TableHead class="px-2 py-1">Population</TableHead>
                <TableHead class="px-2 py-1 text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>

            <TableBody>
              <TableRow v-for="site in paginatedSites" :key="site.site_id" class="h-8">
                <TableCell class="px-2 py-1">{{ site.site_id }}</TableCell>
                <TableCell class="px-2 py-1">{{ site.barangay.municipality.province.province_name }}</TableCell>
                <TableCell class="px-2 py-1">{{ site.barangay.municipality.municipality_name }}</TableCell>
                <TableCell class="px-2 py-1">{{ site.barangay.barangay_name }}</TableCell>
                <TableCell class="px-2 py-1">{{ site.latitude }}</TableCell>
                <TableCell class="px-2 py-1">{{ site.longitude }}</TableCell>
                <TableCell class="px-2 py-1">{{ siteStatusLabels[site.site_status] }}</TableCell>
                <TableCell class="px-2 py-1">{{ site.target_purok }}</TableCell>
                <TableCell class="px-2 py-1">{{ site.target_sitio }}</TableCell>
                <TableCell class="px-2 py-1">{{ site.no_household ?? "-" }}</TableCell>
                <TableCell class="px-2 py-1">{{ site.population ?? "-" }}</TableCell>

                <TableCell class="px-2 py-1 text-right flex justify-end gap-1">
                  <Button size="icon" variant="ghost" @click="editSite(site)" class="h-7 w-7">
                    <Edit class="h-3.5 w-3.5" />
                  </Button>
                  <Button size="icon" variant="destructive" @click="removeSite(site.site_id)" class="h-7 w-7">
                    <Trash2 class="h-3.5 w-3.5" />
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>

          <!-- Pagination -->
          <div class="flex items-center justify-between mt-2 text-sm text-muted-foreground">
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            <div class="flex gap-2">
              <Button size="sm" variant="outline" @click="prevPage" :disabled="currentPage === 1">Previous</Button>
              <Button size="sm" variant="outline" @click="nextPage" :disabled="currentPage === totalPages">Next</Button>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Dialog / Form -->
    <Dialog v-model:open="isDialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ isEditing ? "Edit Site" : "Create Site" }}</DialogTitle>
        </DialogHeader>

        <form class="w-full space-y-6" @submit.prevent="onSubmit">
          <!-- Barangay -->
          <FormField name="barangay_id" v-slot="{ field }">
            <FormItem>
              <FormLabel>Barangay</FormLabel>
              <FormControl>
                <!-- Show summary when editing -->
                <template v-if="isEditing">
                  <div class="rounded-lg border bg-muted/50 p-3 text-sm flex gap-2 items-center" role="status"
                    aria-live="polite">
                    <div class="font-medium text-muted-foreground  ">Selected Location:</div>
                    <div class="font-bold flex items-center gap-1 ">
                      <span class="font-medium">
                        {{sites.find(s => s.site_id === editingId)?.barangay.municipality.province.province_name}}
                      </span>
                      <ChevronRight class="h-3 w-3" />
                      <span class="font-medium">
                        {{sites.find(s => s.site_id === editingId)?.barangay.municipality.municipality_name}}
                      </span>
                      <ChevronRight class="h-3 w-3" />
                      <span class="font-medium">
                        {{sites.find(s => s.site_id === editingId)?.barangay.barangay_name}}
                      </span>
                    </div>
                  </div>
                </template>

                <!-- Show selector only when creating -->
                <template v-else>
                  <BarangaySelector :model-value="field.value" @update:model-value="field.onChange" />
                </template>
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
          <FormField name="site_status" v-slot="{ componentField }" class="w-full">
            <FormItem class="w-full">
              <FormLabel>Site Status</FormLabel>
              <FormControl>
                <Select v-bind="componentField">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select status" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem :value="1">1 - Preparation Phase</SelectItem>
                    <SelectItem :value="2">2 - Action Planning Done</SelectItem>
                    <SelectItem :value="3">3 - With organized PKT</SelectItem>
                    <SelectItem :value="4">4 - Implementing PK</SelectItem>
                    <SelectItem :value="5">5 - Monitored PK Implementation</SelectItem>
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
          <FormField name="target_sitio" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Target Sitio</FormLabel>
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
