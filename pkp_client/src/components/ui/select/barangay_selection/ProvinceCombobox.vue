<script setup lang="ts">
import { Check, Search, Loader2 } from 'lucide-vue-next'
import { ref, watch, defineProps, defineEmits, computed } from 'vue'
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
import axios from "@/axios/axios";

interface Province {
  province_id: number
  province_name: string
}

const props = defineProps<{
  regionId?: number
  modelValue?: Province
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: Province | undefined): void
}>()

const provinces = ref<Province[]>([])
const selectedProvince = ref<Province | undefined>(props.modelValue)
const isLoading = ref(false)
const error = ref<string | null>(null)

const isDisabled = computed(() => !props.regionId || isLoading.value)

// Watch when selected changes
watch(selectedProvince, (val) => {
  emit('update:modelValue', val)
})

watch(
  () => props.regionId,
  async (newRegionId) => {
    if (!newRegionId) {
      provinces.value = []
      selectedProvince.value = undefined
      error.value = null
      return
    }

    isLoading.value = true
    error.value = null

    try {
      const response = await axios.get("/province/region/find", {
        params: { region_id: newRegionId }
      })
      provinces.value = response.data.data ?? []
    } catch (err) {
      error.value = "Failed to load provinces"
      provinces.value = []
      console.error("Failed to load provinces", err)
    } finally {
      isLoading.value = false
    }
  },
  { immediate: true }
)
</script>

<template>
  <div class="w-full">
    <Combobox v-model="selectedProvince" by="province_id" :disabled="isDisabled" class='w-full'>
      <ComboboxAnchor class="w-full border rounded-md">
        <div class="relative w-full items-center">
          <!-- Consistent padding and added loading icon -->
          <ComboboxInput class="pl-1" :display-value="(val) => val?.province_name ?? ''"
            :placeholder="isLoading ? 'Loading provinces...' : 'Select province...'" :disabled="isDisabled" />
          <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
            <Loader2 v-if="isLoading" class="size-4 text-muted-foreground animate-spin" />
            <Search v-else class="size-4 text-muted-foreground" />
          </span>
        </div>
      </ComboboxAnchor>

      <!-- Fixed duplicate overflow styles and added error handling -->
      <ComboboxList class="max-h-60 overflow-y-auto">
        <ComboboxEmpty>
          {{ error || "No province found." }}
        </ComboboxEmpty>
        <ComboboxGroup class="h-60 overflow-y-scroll">
          <ComboboxItem v-for="province in provinces" :key="province.province_id" :value="province"
            class="cursor-pointer">
            {{ province.province_name }}
            <ComboboxItemIndicator>
              <Check class="ml-auto size-4" />
            </ComboboxItemIndicator>
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxList>
    </Combobox>
  </div>
</template>
