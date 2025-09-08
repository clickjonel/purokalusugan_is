<template>
  <div class="flex flex-col md:flex-row h-screen">
    <!-- Map -->
    <div class="flex-1">
      <MapView @province-selected="selectProvince" @marker-selected="selectMarker" />
    </div>

    <!-- Sidebar -->
    <div class="w-full md:w-[28rem] border-t md:border-t-0 md:border-l bg-white overflow-y-auto font-poppins">
      <ProvinceDashboard v-if="selectedProvince" :provinceName="selectedProvince.name" />
      <ProvinceDashboard v-else-if="selectedMarker" :provinceName="selectedMarker.title" />
      <div v-else class="p-4 text-gray-500 text-center">
        📍 Tap a province or capital city to view details
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import MapView from "@/components/MapView.vue"
import ProvinceDashboard from "@/components/ProvinceDashboard.vue"

const selectedProvince = ref(null)
const selectedMarker = ref(null)

const selectProvince = (province) => {
  selectedProvince.value = province
  selectedMarker.value = null
}

const selectMarker = (marker) => {
  selectedMarker.value = marker
  selectedProvince.value = null
}
</script>
