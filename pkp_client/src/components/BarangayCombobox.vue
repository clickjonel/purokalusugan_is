<script setup lang="ts">
import { Check, Search } from 'lucide-vue-next'
import axios from "@/axios/axios"
import { ref, watch } from 'vue'
import {
  Combobox,
  ComboboxAnchor,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxList,
} from '@/components/ui/combobox'
import {
  ScrollArea
} from '@/components/ui/scroll-area'

interface Barangay {
  barangay_id: number
  barangay_name: string
}

const props = defineProps<{
  modelValue?: Barangay
  municipalityId?: number
}>()

const emit = defineEmits<{
  (e: "update:modelValue", value: Barangay | undefined): void
}>()

const barangays = ref<Barangay[]>([])
const selectedBarangay = ref<Barangay | undefined>(props.modelValue)

// Sync prop ↔ local
watch(() => props.modelValue, (val) => {
  selectedBarangay.value = val
})
watch(selectedBarangay, (val) => {
  emit("update:modelValue", val)
})

// Fetch barangays when municipality changes
watch(
  () => props.municipalityId,
  async (newMunicipalityId) => {
    if (!newMunicipalityId) {
      barangays.value = []
      selectedBarangay.value = undefined
      return
    }
    try {
      const { data } = await axios.get("/barangay/municipality/find", {
        params: { municipality_id: newMunicipalityId }
      })
      barangays.value = data.data ?? []
    } catch (error) {
      console.error("Error fetching barangays:", error)
      barangays.value = []
    }
  },
  { immediate: true }
)
</script>

<template>
  <Combobox v-model="selectedBarangay" by="barangay_id">
    <ComboboxAnchor>
      <div class="relative w-full items-center">
        <ComboboxInput class="pl-1 max-w-xl" :display-value="val => val?.barangay_name ?? ''"
          placeholder="Select barangay..." />
        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
          <Search class="size-4 text-muted-foreground" />
        </span>
      </div>
    </ComboboxAnchor>

    <ComboboxList class="max-h-60 overflow-y-auto">
      <ComboboxEmpty>No barangay found.</ComboboxEmpty>
      <ComboboxGroup>
        <ScrollArea class="h-[200px] w-[350px] rounded-md border p-4">
          <ComboboxItem v-for="barangay in barangays" :key="barangay.barangay_id" :value="barangay">
            {{ barangay.barangay_name }}
            <ComboboxItemIndicator>
              <Check class="ml-auto size-4" />
            </ComboboxItemIndicator>
          </ComboboxItem>
        </ScrollArea>
      </ComboboxGroup>
    </ComboboxList>
  </Combobox>
</template>
