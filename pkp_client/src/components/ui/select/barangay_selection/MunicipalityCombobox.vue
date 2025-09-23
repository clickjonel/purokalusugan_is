<script setup lang="ts">
import { Check, Search, Loader2 } from 'lucide-vue-next'
import axios from "@/axios/axios"
import { ref, watch, computed } from 'vue'
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
const isLoading = ref(false)
const error = ref<string | null>(null)

const isDisabled = computed(() => !props.provinceId || isLoading.value)

// Watch for changes from parent
watch(() => props.modelValue, (val) => {
  selectedMunicipality.value = val
})

// Emit updates back
watch(selectedMunicipality, (val) => {
  emit("update:modelValue", val)
})

watch(
  () => props.provinceId,
  async (newProvinceId) => {
    if (!newProvinceId) {
      municipalities.value = []
      selectedMunicipality.value = undefined
      error.value = null
      return
    }

    isLoading.value = true
    error.value = null

    try {
      const { data } = await axios.get("/municipality/province/find", {
        params: { province_id: newProvinceId }
      })
      municipalities.value = data.data ?? []
    } catch (err) {
      error.value = "Failed to load municipalities"
      municipalities.value = []
      console.error("Error fetching municipalities:", err)
    } finally {
      isLoading.value = false
    }
  },
  { immediate: true }
)
</script>

<template>
  <Combobox v-model="selectedMunicipality" by="municipality_id" :disabled="isDisabled">
    <ComboboxAnchor class="w-full border rounded-md">
      <div class="relative w-full items-center">
        <!-- Consistent padding and added loading state -->
        <ComboboxInput class="pl-1" :display-value="val => val?.municipality_name ?? ''"
          :placeholder="isLoading ? 'Loading municipalities...' : 'Select municipality...'" :disabled="isDisabled" />
        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
          <Loader2 v-if="isLoading" class="size-4 text-muted-foreground animate-spin" />
          <Search v-else class="size-4 text-muted-foreground" />
        </span>
      </div>
    </ComboboxAnchor>

    <!-- Fixed duplicate overflow styles and added error handling -->
    <ComboboxList class="max-h-60 overflow-y-auto">
      <ComboboxEmpty>
        {{ error || "No municipality found." }}
      </ComboboxEmpty>
      <ComboboxGroup class="h-60 overflow-y-scroll">
        <ComboboxItem v-for="m in municipalities" :key="m.municipality_id" :value="m" class="cursor-pointer">
          {{ m.municipality_name }}
          <ComboboxItemIndicator>
            <Check class="ml-auto size-4" />
          </ComboboxItemIndicator>
        </ComboboxItem>
      </ComboboxGroup>
    </ComboboxList>
  </Combobox>
</template>
