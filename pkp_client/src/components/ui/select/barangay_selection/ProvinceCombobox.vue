<script setup lang="ts">
import { Check, Search } from 'lucide-vue-next'
import { ref, watch, defineProps, defineEmits } from 'vue'
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

// Watch when selected changes
watch(selectedProvince, (val) => {
  emit('update:modelValue', val)
})

// Fetch provinces whenever regionId changes
watch(
  () => props.regionId,
  async (newRegionId) => {
    if (!newRegionId) {
      provinces.value = []
      selectedProvince.value = undefined
      return
    }
    console.log("Fetching provinces for region:", newRegionId);

    try {
      axios.get("/province/region/find", { params: { region_id: newRegionId } })
        .then((response) => {
          provinces.value = response.data.data ?? [];
          console.log(response.data.data);
        })
        .catch((error) => {
          console.error("Error fetching data:", error);
          provinces.value = [];
        });
    } catch (err) {
      console.error("Failed to load provinces", err);
      provinces.value = [];
    }
  },
  { immediate: true }
)
console.log("Provinces:", provinces);
</script>

<template>
  <div class="w-full">
    <Combobox v-model="selectedProvince" by="province_id">
      <ComboboxAnchor>
        <div class="relative w-full items-center">
          <ComboboxInput class="pl-2" :display-value="(val) => val?.province_name ?? ''"
            placeholder="Select province..." />
          <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
            <Search class="size-4 text-muted-foreground" />
          </span>
        </div>
      </ComboboxAnchor>

      <ComboboxList class="max-h-60 overflow-y-auto">
        <ComboboxEmpty>No province found.</ComboboxEmpty>
        <ComboboxGroup class="h-60 overflow-y-scroll">
          <ComboboxItem v-for="province in provinces" :key="province.province_id" :value="province">
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
