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
import BarangaySelector from "@/components/ui/select/barangay_selection/BarangaySelector.vue"
import { toast } from "vue-sonner"


interface PkpSite {
  site_id?: number
  barangay_id: number
  barangay: {
    barangay_id: number
    barangay_name: string
  }
  latitude: number
  longitude: number
  site_status: number
  target_purok: number
  target_sition: number
  no_household?: number | null
  population?: number | null
}

// ✅ Validation schema
const formSchema = toTypedSchema(z.object({
  barangay_id: z.number().min(1, "Barangay is required"),
  latitude: z.number().min(-90).max(90, "Latitude must be between -90 and 90"),
  longitude: z.number().min(-180).max(180, "Longitude must be between -180 and 180"),
  site_status: z.number().int(),
  target_sition: z.number().int(),
  target_purok: z.number().int(),
  no_household: z.number().nullable().optional(),
  population: z.number().nullable().optional(),
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

const { handleSubmit, resetForm } = useForm({
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
  resetForm({ values: site })
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
        <table class="w-full table-auto">
          <thead>
            <tr class="text-left text-sm text-slate-600">
              <th>ID</th>
              <th>Barangay</th>
              <th>Lat</th>
              <th>Lng</th>
              <th>Status</th>
              <th>Target Purok</th>
              <th>Target Sition</th>
              <th>Households</th>
              <th>Population</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="site in sites" :key="site.site_id" class="border-t">
              <td class="py-2">{{ site.site_id }}</td>
              <td class="py-2">{{ site.barangay.barangay_name }}</td>
              <td class="py-2">{{ site.latitude }}</td>
              <td class="py-2">{{ site.longitude }}</td>
              <td class="py-2">{{ site.site_status }}</td>
              <td class="py-2">{{ site.target_purok }}</td>
              <td class="py-2">{{ site.target_sition }}</td>
              <td class="py-2">{{ site.no_household ?? "-" }}</td>
              <td class="py-2">{{ site.population ?? "-" }}</td>
              <td class="py-2 text-right">
                <Button size="sm" variant="ghost" @click="editSite(site)">Edit</Button>
                <Button size="sm" variant="destructive" @click="removeSite(site.site_id)">Delete</Button>
              </td>
            </tr>
          </tbody>
        </table>
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
          <FormField name="barangay_id" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Barangay</FormLabel>
              <FormControl>
                <BarangaySelector v-bind="componentField" />
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
                <Input type="number" v-bind="componentField" />
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
