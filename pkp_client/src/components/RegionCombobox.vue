<script setup lang="ts">
import { Check, Search } from 'lucide-vue-next'
import { ref } from 'vue'
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

// Region interface
interface Region {
  region_id: number
  region_name: string
}

// Sample API data (replace with fetch/axios later)
const apiData = {
  message: "Data retrieved successfully",
  data: [
    { region_id: 1, region_name: "Region I (Ilocos Region)" },
    { region_id: 2, region_name: "Region II (Cagayan Valley)" },
    { region_id: 3, region_name: "Region III (Central Luzon)" },
    { region_id: 4, region_name: "Region IV-A (CALABARZON)" },
    { region_id: 5, region_name: "Region V (Bicol Region)" },
    { region_id: 6, region_name: "Region VI (Western Visayas)" },
    { region_id: 7, region_name: "Region VII (Central Visayas)" },
    { region_id: 8, region_name: "Region VIII (Eastern Visayas)" },
    { region_id: 9, region_name: "Region IX (Zamboanga Peninsula)" },
    { region_id: 10, region_name: "Region X (Northern Mindanao)" },
    { region_id: 11, region_name: "Region XI (Davao Region)" },
    { region_id: 12, region_name: "Region XII (SOCCSKSARGEN)" },
    { region_id: 13, region_name: "National Capital Region (NCR)" },
    { region_id: 14, region_name: "Cordillera Administrative Region" },
    { region_id: 15, region_name: "Bangsamoro Autonomous Region in Muslim Mindanao" },
    { region_id: 16, region_name: "Region XIII (Caraga)" },
    { region_id: 17, region_name: "Region IV-B (MIMAROPA)" },
  ]
}

// Assign data to regions array
const regions = ref<Region[]>(apiData.data)

// Selected region
const selectedRegion = ref<Region | undefined>()
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

      <ComboboxList>
        <ComboboxEmpty>No region found.</ComboboxEmpty>
        <ComboboxGroup>
          <ComboboxItem v-for="region in regions" :key="region.region_id" :value="region">
            {{ region.region_name }}
            <ComboboxItemIndicator>
              <Check class="ml-auto size-4" />
            </ComboboxItemIndicator>
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxList>
    </Combobox>

    <!-- Example: Show selected region_id -->
    <p v-if="selectedRegion" class="mt-4 text-sm text-gray-600">
      Selected Region ID: <b>{{ selectedRegion.region_id }}</b>
    </p>
  </div>
</template>
