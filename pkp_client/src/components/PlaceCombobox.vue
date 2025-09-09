<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import RegionCombobox from '@/components/RegionCombobox.vue'
import ProvinceCombobox from '@/components/ProvinceCombobox.vue'
import MunicipalityCombobox from './MunicipalityCombobox.vue'
import BarangayCombobox from './BarangayCombobox.vue'
import axios from "@/axios/axios";

interface Region {
  region_id: number
  region_name: string
}
interface Province {
  province_id: number
  province_name: string
}
interface Municipality {
  municipality_id: number
  municipality_name: string
}
interface Barangay {
  barangay_id: number
  barangay_name: string
}

const regions = ref<Region[]>([])

const selectedRegion = ref<Region | undefined>()
const selectedProvince = ref<Province | undefined>()
const selectedMunicipality = ref<Municipality | undefined>()
const selectedBarangay = ref<Barangay | undefined>()

const fetchRegions = () => {
  axios
    .get("/region/list")
    .then((response) => {
      regions.value = response.data.data
    })
    .catch((error) => {
      console.error("Error fetching data:", error)
    })
}

// 🔄 Reset child selections if parent changes
watch(selectedRegion, () => {
  selectedProvince.value = undefined
  selectedMunicipality.value = undefined
  selectedBarangay.value = undefined
})

watch(selectedProvince, () => {
  selectedMunicipality.value = undefined
  selectedBarangay.value = undefined
})

watch(selectedMunicipality, () => {
  selectedBarangay.value = undefined
})

onMounted(() => {
  fetchRegions()
})
</script>


<template>
  <div class="space-y-4 max-w-sm">
    <RegionCombobox v-model="selectedRegion" :regions="regions" />
    <ProvinceCombobox v-model="selectedProvince" :region-id="selectedRegion?.region_id" />
    <MunicipalityCombobox v-model="selectedMunicipality" :province-id="selectedProvince?.province_id" />
    <BarangayCombobox v-model="selectedBarangay" :municipality-id="selectedMunicipality?.municipality_id" />

    <div v-if="selectedRegion && selectedProvince && selectedMunicipality && selectedBarangay" class="text-sm mt-4">
      Selected:
      <b>{{ selectedRegion.region_name }}</b> →
      <b>{{ selectedProvince.province_name }}</b> →
      <b>{{ selectedMunicipality.municipality_name }}</b> →
      <b>{{ selectedBarangay.barangay_name }}</b>
    </div>
  </div>
</template>
