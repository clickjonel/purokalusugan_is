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

interface Barangay {
  barangay_id: number
  barangay_name: string
}

const props = defineProps<{
  modelValue?: number          // 👈 expect just the barangay_id
  municipalityId?: number
}>()

const emit = defineEmits<{
  (e: "update:modelValue", value: number | undefined): void
}>()

const barangays = ref<Barangay[]>([])
const selectedBarangay = ref<Barangay | undefined>()

// keep local selection in sync when modelValue changes
watch(
  () => props.modelValue,
  (id) => {
    selectedBarangay.value = barangays.value.find(b => b.barangay_id === id)
  }
)

// emit only the id when selection changes
watch(selectedBarangay, (val) => {
  emit("update:modelValue", val?.barangay_id)
})

// fetch barangays when municipalityId changes
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
        params: { municipality_id: newMunicipalityId },
      })
      barangays.value = data.data ?? []

      // reselect if current id exists in the new list
      if (props.modelValue) {
        selectedBarangay.value = barangays.value.find(
          (b) => b.barangay_id === props.modelValue
        )
      }
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

    <ComboboxList class="w-full max-h-60 overflow-y-auto">
      <ComboboxEmpty>No barangay found.</ComboboxEmpty>
      <ComboboxGroup class="h-60 overflow-y-scroll">
        <ComboboxItem v-for="barangay in barangays" :key="barangay.barangay_id" :value="barangay">
          {{ barangay.barangay_name }}
          <ComboboxItemIndicator>
            <Check class="ml-auto size-4" />
          </ComboboxItemIndicator>
        </ComboboxItem>
      </ComboboxGroup>
    </ComboboxList>
  </Combobox>
</template>
