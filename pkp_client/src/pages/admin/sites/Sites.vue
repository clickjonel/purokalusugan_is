<script setup lang="ts">
import { onMounted, ref, computed } from "vue"
import axios from '@/axios/axios';
import { Button } from "@/components/ui/button";
import { Input } from '@/components/ui/input'
import { Search, EllipsisVertical, Edit, Trash2, ChevronRight } from "lucide-vue-next"
import { toast } from 'vue-sonner'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import BarangaySelector from "@/components/ui/select/barangay_selection/BarangaySelector.vue"

interface PkpSite {
  site_id?: number
  barangay_id: number
  latitude: number
  longitude: number
  site_status: number
  no_sitio: number
  no_purok: number
  total_no_sitio_purok: number
  target_purok: number
  target_sitio: number
  total_target_sitio_purok: number
  no_household?: number
  population?: number
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

const sites = ref<PkpSite[]>([])
const currentList = ref<PkpSite[]>([]);
const isDialogOpen = ref(false)
const isCreateDialogOpen = ref(false) // New ref for create dialog
const editingSite = ref<PkpSite | null>(null)

const newSite = ref<Omit<PkpSite, 'site_id' | 'barangay' | 'total_no_sitio_purok' | 'total_target_sitio_purok'>>({
  barangay_id: 0,
  latitude: 0,
  longitude: 0,
  site_status: 1,
  no_sitio: 0,
  no_purok: 0,
  target_sitio: 0,
  target_purok: 0,
  no_household: 0,
  population: 0,
})

let searchKeyword = ref("");
onMounted(() => {
  fetchSites()
})

function search() {
  const searchTerm = searchKeyword.value.toLowerCase().trim();
  const searchedData = sites.value.filter(item => {
    const record = item.barangay.barangay_name.toLowerCase();
    return record.includes(searchTerm);
  });
  currentList.value = searchedData;
}
function determineStatusColor(color: number) {
  let appliedColor = "";
  switch (color) {
    case 1: appliedColor = "bg-blue-200 text-blue-800"; break;
    case 2: appliedColor = "bg-yellow-200 text-yellow-800"; break;
    case 3: appliedColor = "bg-orange-200 text-orange-800"; break;
    case 4: appliedColor = "bg-green-200 text-green-800"; break;
    case 5: appliedColor = "bg-green-400 text-green-900"; break;
    default: appliedColor = "bg-black"; break;
  }
  return appliedColor;
}
function fetchSites() {
  axios.get<{ data: PkpSite[] }>('/site/list')
    .then((response) => {
      sites.value = response.data.data
      currentList.value = response.data.data;
      // console.log(response.data.data)
    })
    .catch((error) => {
      console.log(error)
    })
}

function saveSite() {
  if (newSite.value.barangay_id === 0) {
    toast('Validation Error', {
      description: 'Barangay is Required',
      action: {
        label: 'Close',
        onClick: () => toast.dismiss(),
      },
    })
    return
  }

  // Calculate total fields
  const siteData = {
    ...newSite.value,
    total_no_sitio_purok: newSite.value.no_sitio + newSite.value.no_purok,
    total_target_sitio_purok: newSite.value.target_sitio + newSite.value.target_purok
  }

  axios.post('/site/create', siteData)
    .then((response) => {
      toast('Action Success', {
        description: response.data.message,
        action: {
          label: 'Close',
          onClick: () => toast.dismiss(),
        },
      })
      // Reset form fields
      newSite.value = {
        barangay_id: 0,
        latitude: 0,
        longitude: 0,
        site_status: 1,
        no_sitio: 0,
        no_purok: 0,
        target_sitio: 0,
        target_purok: 0,
        no_household: 0,
        population: 0,
      }
      // Close the dialog
      isCreateDialogOpen.value = false
      fetchSites()
    })
    .catch((error) => {
      console.log(error)
      if (error.response) {
        toast('Failed With Errors', {
          description: error.response.data.message,
          action: {
            label: 'Close',
            onClick: () => toast.dismiss(),
          },
        })
      }
    })
}

function openEditDialog(site: PkpSite) {
  editingSite.value = JSON.parse(JSON.stringify(site))
  isDialogOpen.value = true
}

function updateSite() {
  if (!editingSite.value?.site_id) return

  // Calculate total fields
  const siteData = {
    ...editingSite.value,
    total_no_sitio_purok: editingSite.value.no_sitio + editingSite.value.no_purok,
    total_target_sitio_purok: editingSite.value.target_sitio + editingSite.value.target_purok
  }

  axios.put('/site/update', siteData)
    .then((response) => {
      toast('Action Success', {
        description: response.data.message,
        action: {
          label: 'Close',
          onClick: () => toast.dismiss(),
        },
      })
      isDialogOpen.value = false
      fetchSites()
    })
    .catch((error) => {
      console.log(error)
      if (error.response) {
        toast('Failed With Errors', {
          description: error.response.data.message,
          action: {
            label: 'Close',
            onClick: () => toast.dismiss(),
          },
        })
      }
    })
}

async function removeSite(id?: number) {
  if (!id) return
  if (!confirm("Delete this site?")) return
  try {
    await axios.delete(`/site/delete`, { data: { site_id: id } })
    await fetchSites()
    toast.success("Site deleted.")
  } catch (e) {
    console.error("Delete failed", e)
    toast.error("Could not delete site.")
  }
}


</script>

<template>
  <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
    <!-- header -->
    <div class="w-full flex justify-between items-center p-2 border">
      <div class="relative items-center">
        <Input v-model="searchKeyword" id="search" type="text" placeholder="Search Barangay Name" class="pl-8"
          @input="search" />
        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
          <Search class="size-4 text-muted-foreground" />
        </span>
      </div>
      <Button variant="default" class="cursor-pointer" size="sm" @click="isCreateDialogOpen = true">
        Create Site
      </Button>
    </div>

    <!-- table -->
    <div class="w-full h-full flex flex-col justify-start items-start border">
      <div class="w-full h-[700px] overflow-y-scroll">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>ID</TableHead>
              <!-- 
              <TableHead>Province</TableHead>
              <TableHead>Municipality</TableHead>
              <TableHead>Barangay</TableHead>
               -->
              <TableHead>Location</TableHead>
              <TableHead>Coordinates (Lat/Long)</TableHead>
              <TableHead>Sitio/Purok</TableHead>
              <TableHead>Targets</TableHead>
              <TableHead>Population</TableHead>
              <TableHead>Households</TableHead>
              <TableHead>Status</TableHead>
              <TableHead class="text-end">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="site in currentList" :key="site.site_id">
              <TableCell>{{ site.site_id }}</TableCell>
              <!-- 
              <TableCell>{{ site.barangay.municipality.province.province_name }}</TableCell>
              <TableCell>{{ site.barangay.municipality.municipality_name }}</TableCell>
              <TableCell>{{ site.barangay.barangay_name }}</TableCell>
               -->
              <TableCell>
                <Popover>
                  <PopoverTrigger asChild>
                    <Button variant="ghost" size="sm"
                      class="cursor-pointer text-xs font-normal justify-start p-0 h-auto">
                      {{ site.barangay.municipality.province.province_name }}, {{
                        site.barangay.municipality.municipality_name }}, {{
                        site.barangay.barangay_name }}
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent class="w-50 p-2">
                    <div class="flex flex-col">
                      <span class="text-xs">{{ site.barangay.barangay_name }}</span>
                      <span class="text-xs">{{ site.barangay.municipality.municipality_name }}, {{
                        site.barangay.municipality.province.province_name }}</span>
                    </div>
                  </PopoverContent>
                </Popover>
              </TableCell>
              <TableCell>{{ site.latitude }}, {{ site.longitude }}</TableCell>
              <TableCell>{{ site.total_no_sitio_purok }} ({{ site.no_purok }} Purok, {{ site.no_sitio }} Sitio)
              </TableCell>
              <TableCell>{{ site.total_target_sitio_purok }} ({{ site.target_purok }} Purok, {{ site.target_sitio }}
                Sitio)</TableCell>
              <TableCell>{{ site.population ?? '-' }}</TableCell>
              <TableCell>{{ site.no_household ?? '-' }}</TableCell>
              <TableCell :class="determineStatusColor(site.site_status)">{{ site.site_status }} ({{
                siteStatusLabels[site.site_status] }})</TableCell>
              <TableCell class="w-full flex justify-end items-center gap-2">
                <Popover>
                  <PopoverTrigger asChild>
                    <Button variant="ghost" size="icon" class="cursor-pointer">
                      <EllipsisVertical class="h-4 w-4" />
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent class="w-45 p-2">
                    <div class="flex flex-col">
                      <Button variant="ghost" size="sm" class="justify-start text-xs" @click="openEditDialog(site)">
                        <Edit class="mr-2 h-4 w-4" /> Edit
                      </Button>
                      <Button variant="ghost" size="sm" class="justify-start text-xs text-red-600"
                        @click="removeSite(site.site_id)">
                        <Trash2 class="mr-2 h-4 w-4" /> Delete
                      </Button>
                    </div>
                  </PopoverContent>
                </Popover>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>

    <!-- footer -->
    <div class="w-full h-[50px] flex justify-between items-center border p-2">
      Pagination
    </div>

    <!-- Create Dialog -->
    <Dialog v-model:open="isCreateDialogOpen">
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Create New Site</DialogTitle>
        </DialogHeader>
        <div class="space-y-4 py-4">
          <div class="space-y-2">
            <div>
              <label class="text-sm font-medium">Barangay</label>
              <BarangaySelector v-model="newSite.barangay_id" />
              Selected Barangay ID: <b>{{ newSite.barangay_id }}</b>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="text-sm font-medium">Latitude</label>
                <Input type="number" step="0.00000001" v-model="newSite.latitude" />
              </div>
              <div>
                <label class="text-sm font-medium">Longitude</label>
                <Input type="number" step="0.00000001" v-model="newSite.longitude" />
              </div>
            </div>
            <div>
              <label class="text-sm font-medium">Site Status</label>
              <select v-model="newSite.site_status" class="w-full p-2 border rounded">
                <option v-for="(label, value) in siteStatusLabels" :value="Number(value)">{{ value }} - {{ label }}
                </option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="text-sm font-medium">No. Purok</label>
                <Input type="number" v-model="newSite.no_purok" />
              </div>
              <div>
                <label class="text-sm font-medium">No. Sitio</label>
                <Input type="number" v-model="newSite.no_sitio" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="text-sm font-medium">Target Purok</label>
                <Input type="number" v-model="newSite.target_purok" />
              </div>
              <div>
                <label class="text-sm font-medium">Target Sitio</label>
                <Input type="number" v-model="newSite.target_sitio" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="text-sm font-medium">No. Households</label>
                <Input type="number" v-model="newSite.no_household" />
              </div>
              <div>
                <label class="text-sm font-medium">Population</label>
                <Input type="number" v-model="newSite.population" />
              </div>
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-2">
          <Button variant="secondary" type="button" @click="isCreateDialogOpen = false">Cancel</Button>
          <Button @click="saveSite">Create Site</Button>
        </div>
      </DialogContent>
    </Dialog>

    <!-- Edit Dialog -->
    <Dialog v-model:open="isDialogOpen">
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Edit Site</DialogTitle>
        </DialogHeader>
        <div class="space-y-4 py-4">
          <div>
            <label class="text-sm font-medium">Barangay</label>
            <div class="rounded-lg border bg-muted/50 p-3 text-sm flex gap-2 items-center">
              <div class="font-medium text-muted-foreground">Selected Location:</div>
              <div class="font-bold flex items-center gap-1">
                <span v-if="editingSite?.barangay">{{ editingSite.barangay.municipality.province.province_name }}</span>
                <ChevronRight v-if="editingSite?.barangay" class="h-3 w-3" />
                <span v-if="editingSite?.barangay">{{ editingSite.barangay.municipality.municipality_name }}</span>
                <ChevronRight v-if="editingSite?.barangay" class="h-3 w-3" />
                <span v-if="editingSite?.barangay">{{ editingSite.barangay.barangay_name }}</span>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium">Latitude</label>
              <Input type="number" step="0.00000001" v-model="editingSite!.latitude" />
            </div>
            <div>
              <label class="text-sm font-medium">Longitude</label>
              <Input type="number" step="0.00000001" v-model="editingSite!.longitude" />
            </div>
          </div>
          <div>
            <label class="text-sm font-medium">Site Status</label>
            <select v-model="editingSite!.site_status" class="w-full p-2 border rounded">
              <option v-for="(label, value) in siteStatusLabels" :value="Number(value)">{{ value }} - {{ label }}
              </option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium">No. Purok</label>
              <Input type="number" v-model="editingSite!.no_purok" />
            </div>
            <div>
              <label class="text-sm font-medium">No. Sitio</label>
              <Input type="number" v-model="editingSite!.no_sitio" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium">Target Purok</label>
              <Input type="number" v-model="editingSite!.target_purok" />
            </div>
            <div>
              <label class="text-sm font-medium">Target Sitio</label>
              <Input type="number" v-model="editingSite!.target_sitio" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium">No. Households</label>
              <Input type="number" v-model="editingSite!.no_household" />
            </div>
            <div>
              <label class="text-sm font-medium">Population</label>
              <Input type="number" v-model="editingSite!.population" />
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-2">
          <Button variant="secondary" type="button" @click="isDialogOpen = false">Cancel</Button>
          <Button @click="updateSite">Save changes</Button>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>
