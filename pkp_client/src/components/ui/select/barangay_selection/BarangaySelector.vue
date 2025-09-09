<script setup lang="ts">
import { ref, watch, computed, nextTick } from 'vue'
import ProvinceCombobox from '@/components/ui/select/barangay_selection/ProvinceCombobox.vue'
import MunicipalityCombobox from './MunicipalityCombobox.vue'
import BarangayCombobox from './BarangayCombobox.vue'
import {
  Drawer,
  DrawerTrigger,
  DrawerContent,
  DrawerHeader,
  DrawerTitle,
  DrawerFooter,
  DrawerClose,
} from "@/components/ui/drawer"
import { Button } from "@/components/ui/button"
import { MapPin, ChevronRight } from 'lucide-vue-next'

interface Province {
  province_id: number
  province_name: string
}

interface Municipality {
  municipality_id: number
  municipality_name: string
}

interface Barangay {
  barangay_id: number
  barangay_name: string
}

interface SelectedLocation {
  province?: Province
  municipality?: Municipality
  barangayId?: number
  barangayName?: string
}

const props = withDefaults(defineProps<{
  modelValue?: number
  regionId?: number
  placeholder?: string
  disabled?: boolean
}>(), {
  regionId: 14,
  placeholder: 'Select Location',
  disabled: false
})

const emit = defineEmits<{
  (e: "update:modelValue", value: number | undefined): void
  (e: "locationSelected", location: SelectedLocation): void
}>()

const selectedProvince = ref<Province | undefined>()
const selectedMunicipality = ref<Municipality | undefined>()
const selectedBarangayId = ref<number | undefined>(props.modelValue)
const selectedBarangayName = ref<string>()
const open = ref(false)
const isLoading = ref(false)

const buttonText = computed(() => {
  if (selectedProvince.value && selectedMunicipality.value && selectedBarangayId.value) {
    return `${selectedProvince.value.province_name}, ${selectedMunicipality.value.municipality_name}`
  }
  return props.placeholder
})

const isSelectionComplete = computed(() => {
  return selectedProvince.value && selectedMunicipality.value && selectedBarangayId.value
})

watch(selectedProvince, (newProvince) => {
  if (!newProvince) return

  // Reset dependent selections
  selectedMunicipality.value = undefined
  selectedBarangayId.value = undefined
  selectedBarangayName.value = undefined
}, { flush: 'post' })

watch(selectedMunicipality, (newMunicipality) => {
  if (!newMunicipality) return

  // Reset barangay selection
  selectedBarangayId.value = undefined
  selectedBarangayName.value = undefined
}, { flush: 'post' })

watch(selectedBarangayId, (newBarangayId) => {
  emit("update:modelValue", newBarangayId)
})

async function confirmSelection() {
  if (!isSelectionComplete.value) return

  isLoading.value = true

  try {
    // Emit detailed location data
    const locationData: SelectedLocation = {
      province: selectedProvince.value,
      municipality: selectedMunicipality.value,
      barangayId: selectedBarangayId.value,
      barangayName: selectedBarangayName.value
    }

    emit("locationSelected", locationData)

    // Close drawer after a brief delay for better UX
    await nextTick()
    setTimeout(() => {
      open.value = false
    }, 100)
  } catch (error) {
    console.error('Error confirming selection:', error)
  } finally {
    isLoading.value = false
  }
}

function resetSelections() {
  selectedProvince.value = undefined
  selectedMunicipality.value = undefined
  selectedBarangayId.value = undefined
  selectedBarangayName.value = undefined
}
</script>

<template>
  <div class="w-full max-w-sm">
    <Drawer v-model:open="open">
      <DrawerTrigger as-child>
        <Button variant="outline" class="w-full justify-between text-left font-normal" :disabled="disabled"
          @click="resetSelections" :aria-label="`Open location selector. Current selection: ${buttonText}`">
          <div class="flex items-center gap-2 truncate">
            <MapPin class="h-4 w-4 shrink-0 opacity-50" />
            <span class="truncate">{{ buttonText }}</span>
          </div>
          <ChevronRight class="h-4 w-4 shrink-0 opacity-50" />
        </Button>
      </DrawerTrigger>

      <DrawerContent class="bg-gray-100 rounded-t-[10px]   max-h-[96%] fixed bottom-0 left-0 right-0">
        <DrawerHeader class="text-left">
          <DrawerTitle>Select Your Location</DrawerTitle>
          <p class="text-sm text-muted-foreground">
            Choose your province, municipality, and barangay
          </p>
        </DrawerHeader>

        <div class="px-4 pb-4">
          <div class="space-y-4 max-w-sm mx-auto">
            <!-- Enhanced combobox components with better labels -->
            <div class="space-y-2">
              <label class="text-sm font-medium">Province</label>
              <ProvinceCombobox v-model="selectedProvince" :region-id="regionId" aria-label="Select province" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">Municipality</label>
              <MunicipalityCombobox v-model="selectedMunicipality" :province-id="selectedProvince?.province_id"
                :disabled="!selectedProvince" aria-label="Select municipality" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">Barangay</label>
              <BarangayCombobox v-model="selectedBarangayId" :municipality-id="selectedMunicipality?.municipality_id"
                :disabled="!selectedMunicipality" aria-label="Select barangay" />

            </div>

            <!-- Enhanced selection preview with better styling -->
            <div v-if="isSelectionComplete" class="rounded-lg border bg-muted/50 p-3 text-sm" role="status"
              aria-live="polite">
              <div class="font-medium text-foreground mb-1">Selected Location:</div>
              <div class="flex items-center gap-1 text-muted-foreground">
                <span class="font-medium">{{ selectedProvince?.province_name }}</span>
                <ChevronRight class="h-3 w-3" />
                <span class="font-medium">{{ selectedMunicipality?.municipality_name }}</span>
                <ChevronRight class="h-3 w-3" />
                <span class="font-medium">{{ selectedBarangayName || `ID: ${selectedBarangayId}` }}</span>
              </div>
            </div>
          </div>
        </div>

        <DrawerFooter class="flex-row gap-2">
          <DrawerClose as-child>
            <Button variant="outline" class="flex-1">
              Cancel
            </Button>
          </DrawerClose>
          <Button @click="confirmSelection" :disabled="!isSelectionComplete || isLoading" class="flex-1"
            :aria-label="isSelectionComplete ? 'Confirm location selection' : 'Please complete your selection'">
            <span v-if="isLoading">Confirming...</span>
            <span v-else>Confirm Selection</span>
          </Button>
        </DrawerFooter>
      </DrawerContent>
    </Drawer>
  </div>
</template>
