<template>
  <div class="app-container flex">
    <MapView @province-selected="selectedProvince = $event" />
    <ProvincePreview v-if="selectedProvince" :province="selectedProvince" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import MapView from '@/components/MapView.vue'
import ProvincePreview from '@/components/ProvincePreview.vue'

const selectedProvince = ref(null)
</script>

<style scoped>
.app-container {
  height: 100vh;
}
</style>
