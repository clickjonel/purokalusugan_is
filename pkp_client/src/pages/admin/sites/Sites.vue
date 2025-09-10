<template>
  <div class="p-4 space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold">PKP Sites</h2>
      <Button @click="openCreate">New Site</Button>
    </div>

    <!-- Table -->
    <Card>
      <CardContent>
        <table class="w-full table-auto">
          <thead>
            <tr class="text-left text-sm text-slate-600">
              <th>Site ID</th>
              <th>Barangay</th>
              <th>Lat</th>
              <th>Lng</th>
              <th>Status</th>
              <th>Households</th>
              <th>Population</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="site in sites" :key="site.site_id" class="border-t">
              <td class="py-2">{{ site.site_id }}</td>
              <td class="py-2">{{ site.barangay_name ?? site.barangay_id }}</td>
              <td class="py-2">{{ site.latitude }}</td>
              <td class="py-2">{{ site.longitude }}</td>
              <td class="py-2">{{ site.site_status }}</td>
              <td class="py-2">{{ site.no_household ?? '-' }}</td>
              <td class="py-2">{{ site.population ?? '-' }}</td>
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
          <DialogTitle>{{ isEditing ? 'Edit Site' : 'Create Site' }}</DialogTitle>
        </DialogHeader>

        <form @submit.prevent="onSubmit">
          <div class="grid grid-cols-1 gap-3">
            <FormField>
              <FormItem>
                <FormLabel>Barangay</FormLabel>
                <Select v-model="form.barangay_id" :options="barangayOptions" :placeholder="'Select barangay'" />
                <FormMessage v-if="errors.barangay_id">{{ errors.barangay_id }}</FormMessage>
              </FormItem>
            </FormField>

            <FormField>
              <FormItem>
                <FormLabel>Latitude</FormLabel>
                <Input type="number" step="0.00000001" v-model.number="form.latitude" />
                <FormMessage v-if="errors.latitude">{{ errors.latitude }}</FormMessage>
              </FormItem>
            </FormField>

            <FormField>
              <FormItem>
                <FormLabel>Longitude</FormLabel>
                <Input type="number" step="0.00000001" v-model.number="form.longitude" />
                <FormMessage v-if="errors.longitude">{{ errors.longitude }}</FormMessage>
              </FormItem>
            </FormField>

            <FormField>
              <FormItem>
                <FormLabel>Site Status</FormLabel>
                <Select v-model="form.site_status" :options="statusOptions" />
              </FormItem>
            </FormField>

            <div class="grid grid-cols-2 gap-2">
              <FormField>
                <FormItem>
                  <FormLabel>No. Households</FormLabel>
                  <Input type="number" v-model.number="form.no_household" />
                </FormItem>
              </FormField>

              <FormField>
                <FormItem>
                  <FormLabel>Population</FormLabel>
                  <Input type="number" v-model.number="form.population" />
                </FormItem>
              </FormField>
            </div>

            <div class="flex justify-end gap-2 pt-2">
              <Button variant="secondary" @click="closeDialog" type="button">Cancel</Button>
              <Button type="submit">{{ isEditing ? 'Update' : 'Create' }}</Button>
            </div>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

// shadcn-ui components - adapt names to your project's registration
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Select } from '@/components/ui/select'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'

// Types
interface PkpSite {
  site_id?: number
  barangay_id: number
  barangay_name?: string | null
  latitude: number
  longitude: number
  site_status: number
  no_purok?: number | null
  no_sitio?: number | null
  target_purok?: number | null
  target_sition?: number | null
  no_household?: number | null
  population?: number | null
  created_at?: string
  updated_at?: string
  deleted_at?: string | null
}

interface Option { label: string; value: number }

// Local state
const sites = ref<PkpSite[]>([])
const barangayOptions = ref<Option[]>([])
const statusOptions = ref<Option[]>([{ label: 'Inactive', value: 0 }, { label: 'Active', value: 1 }])

const isDialogOpen = ref(false)
const isEditing = ref(false)

const form = reactive<PkpSite>({
  barangay_id: 0,
  latitude: 0,
  longitude: 0,
  site_status: 1,
})

const errors = reactive<Record<string, string | null>>({})

// API endpoints - adapt base URL / endpoints to your backend
const api = axios.create({ baseURL: '/api' })

async function loadSites() {
  try {
    const res = await api.get('/pkp-sites')
    sites.value = res.data.map((s: any) => ({ ...s }))
  } catch (e) {
    console.error('failed to load sites', e)
  }
}

async function loadBarangays() {
  try {
    const res = await api.get('/pkp-barangays')
    barangayOptions.value = res.data.map((b: any) => ({ label: b.name || b.barangay_name || (`#${b.barangay_id}`), value: b.barangay_id }))
  } catch (e) {
    console.error('failed to load barangays', e)
  }
}

onMounted(() => {
  loadSites()
  loadBarangays()
})

function openCreate() {
  isEditing.value = false
  resetForm()
  isDialogOpen.value = true
}

function editSite(site: PkpSite) {
  isEditing.value = true
  Object.assign(form, site)
  isDialogOpen.value = true
}

function closeDialog() {
  isDialogOpen.value = false
}

function resetForm() {
  form.site_id = undefined
  form.barangay_id = 0
  form.latitude = 0
  form.longitude = 0
  form.site_status = 1
  form.no_household = undefined
  form.population = undefined
  Object.keys(errors).forEach(k => errors[k] = null)
}

function validate() {
  let ok = true
  errors.barangay_id = null
  errors.latitude = null
  errors.longitude = null

  if (!form.barangay_id || form.barangay_id === 0) { errors.barangay_id = 'Barangay is required'; ok = false }
  if (typeof form.latitude !== 'number' || isNaN(form.latitude)) { errors.latitude = 'Latitude is required'; ok = false }
  if (typeof form.longitude !== 'number' || isNaN(form.longitude)) { errors.longitude = 'Longitude is required'; ok = false }

  // latitude range check
  if (ok) {
    if (form.latitude < -90 || form.latitude > 90) { errors.latitude = 'Latitude must be between -90 and 90'; ok = false }
    if (form.longitude < -180 || form.longitude > 180) { errors.longitude = 'Longitude must be between -180 and 180'; ok = false }
  }

  return ok
}

async function onSubmit() {
  if (!validate()) return

  try {
    if (isEditing.value && form.site_id) {
      const payload = { ...form }
      await api.put(`/pkp-sites/${form.site_id}`, payload)
    } else {
      const payload = { ...form }
      await api.post('/pkp-sites', payload)
    }

    await loadSites()
    closeDialog()
  } catch (e) {
    console.error('save failed', e)
    // simple error handling example
    if (axios.isAxiosError(e) && e.response) {
      const data = e.response.data
      if (data && typeof data === 'object') {
        Object.assign(errors, data.errors ?? {})
      }
    }
  }
}

async function removeSite(id?: number) {
  if (!id) return
  if (!confirm('Delete this site?')) return
  try {
    await api.delete(`/pkp-sites/${id}`)
    await loadSites()
  } catch (e) {
    console.error('delete failed', e)
  }
}
</script>

<style scoped>
/* light styling tweaks (Tailwind should handle most) */
</style>
