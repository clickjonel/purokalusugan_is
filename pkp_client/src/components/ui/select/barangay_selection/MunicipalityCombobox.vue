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

interface Municipality {
  municipality_id: number
  municipality_name: string
}

const props = defineProps<{
  modelValue?: Municipality
  provinceId?: number
}>()

const emit = defineEmits<{
  (e: "update:modelValue", value: Municipality | undefined): void
}>()

const municipalities = ref<Municipality[]>([])
const selectedMunicipality = ref<Municipality | undefined>(props.modelValue)

// Watch for changes from parent
watch(() => props.modelValue, (val) => {
  selectedMunicipality.value = val
})

// Emit updates back
watch(selectedMunicipality, (val) => {
  emit("update:modelValue", val)
})

// Fetch municipalities when province changes
watch(
  () => props.provinceId,
  async (newProvinceId) => {
    if (!newProvinceId) {
      municipalities.value = []
      selectedMunicipality.value = undefined
      return
    }
    try {
      const { data } = await axios.get("/municipality/province/find", {
        params: { province_id: newProvinceId }
      })
      municipalities.value = data.data ?? []
    } catch (error) {
      console.error("Error fetching municipalities:", error)
      municipalities.value = []
    }
  },
  { immediate: true }
)
</script>

<template>
  <Combobox v-model="selectedMunicipality" by="municipality_id">
    <ComboboxAnchor>
      <div class="relative w-full items-center">
        <ComboboxInput class="pl-1 max-w-xl" :display-value="val => val?.municipality_name ?? ''"
          placeholder="Select municipality..." />
        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
          <Search class="size-4 text-muted-foreground" />
        </span>
      </div>
    </ComboboxAnchor>

    <ComboboxList class="max-h-60 overflow-y-auto">
      <ComboboxEmpty>No municipality found.</ComboboxEmpty>
      <ComboboxGroup class="h-60 overflow-y-scroll">
        <ComboboxItem v-for="m in municipalities" :key="m.municipality_id" :value="m">
          {{ m.municipality_name }}
          <ComboboxItemIndicator>
            <Check class="ml-auto size-4" />
          </ComboboxItemIndicator>
        </ComboboxItem>
      </ComboboxGroup>
    </ComboboxList>
  </Combobox>
</template>
