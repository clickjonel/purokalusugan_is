<script setup lang="ts">
import { Check, Search } from 'lucide-vue-next'
import axios from "@/axios/axios";
import { ref,onMounted } from 'vue'
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

const fetchRegions=()=>{
  axios
    .get("/region/list")
    .then((response) => {
      regions.value = response.data.data;      
      console.log("data here", regions.value);
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
}
// Assign data to regions array
const regions = ref<Region[]>([])

// Selected region
const selectedRegion = ref<Region | undefined>()

onMounted(()=>{
  fetchRegions();
})
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
