
<script setup lang="ts">
import { Check, Search } from 'lucide-vue-next'
import { ref, defineProps, defineEmits, watch } from 'vue'
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

interface Region {
  region_id: number
  region_name: string
}

const props = defineProps<{
  regions: Region[]
  modelValue?: Region
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: Region | undefined): void
}>()

const selectedRegion = ref<Region | undefined>(props.modelValue)

watch(selectedRegion, (val) => {
  emit('update:modelValue', val)
})

watch(
  () => props.modelValue,
  (val) => {
    selectedRegion.value = val
  }
)
</script>

<template>
  <div class="w-full font-poppins">
    <Combobox v-model="selectedRegion" by="region_id">
      <ComboboxAnchor>
        <div class="relative w-full items-center">
          <ComboboxInput class="pl-1 max-w-xl" :display-value="(val) => val?.region_name ?? ''"
            placeholder="Select region..." />
          <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
            <Search class="size-4 text-muted-foreground" />
          </span>
        </div>
      </ComboboxAnchor>

      <ComboboxList class="max-h-60 overflow-y-auto">
        <ComboboxEmpty>No region found.</ComboboxEmpty>
        <ComboboxGroup>
          <ComboboxItem v-for="region in props.regions" :key="region.region_id" :value="region">
            {{ region.region_name }}
            <ComboboxItemIndicator>
              <Check class="ml-auto size-4" />
            </ComboboxItemIndicator>
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxList>
    </Combobox>
  </div>
</template>
