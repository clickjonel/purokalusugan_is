<script setup lang="ts">
import { ref } from "vue"
import MapView from "@/components/MapView.vue"
import ProvinceDashboard from "@/components/ProvinceDashboard.vue"

interface ProvincePayload {
  name: string
  info: Record<string, unknown>
}

interface MarkerPayload {
  title: string
  population: number
  region: string
}

const selectedProvince = ref<ProvincePayload | null>(null)
const selectedMarker = ref<MarkerPayload | null>(null)


const selectProvince = (province: ProvincePayload) => {
  selectedProvince.value = province
  selectedMarker.value = null
}

const selectMarker = (marker: MarkerPayload) => {
  selectedMarker.value = marker
  selectedProvince.value = null
}
</script>

<template>
  <div class="flex flex-col md:flex-row h-screen">

    <div class="flex-1">
      <MapView @province-selected="selectProvince" @marker-selected="selectMarker" />
    </div>


    <div class="w-full md:w-[28rem] border-t md:border-t-0 md:border-l bg-white overflow-y-auto font-poppins">
      <ProvinceDashboard v-if="selectedProvince" :provinceName="selectedProvince.name" />
      <ProvinceDashboard v-else-if="selectedMarker" :provinceName="selectedMarker.title" />
      <div v-else class="p-4 text-gray-500 text-center">
        📍 Tap a province or capital city to view details
      </div>
    </div>
  </div>
</template>
