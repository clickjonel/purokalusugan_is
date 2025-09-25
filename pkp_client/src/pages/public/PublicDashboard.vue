<script setup lang="ts">
import { ref, onMounted, computed } from "vue"
import MapView from "@/components/MapView.vue"
import ProvinceDashboard from "@/components/ProvinceDashboard.vue"
import type { CatchmentOverviewProps, Site } from "@/components/ProvinceDashboard.vue"
import axios from "@/axios/axios"

interface ProvincePayload {
  name: string
  info: Record<string, unknown>
}

interface MarkerPayload {
  title: string
  population?: number
  region: string
  level?: "province" | "municipality" | "barangay"
  status?: string
}

const selectedProvince = ref<ProvincePayload | null>(null)
const selectedMarker = ref<MarkerPayload | null>(null)
const selectProvince = (province: ProvincePayload) => {
  selectedProvince.value = province
  selectedMarker.value = null
}

const selectMarker = (marker: MarkerPayload) => {
  selectedMarker.value = {
    ...marker,
    level: marker.level ?? "barangay",
    status: marker.status ?? "Active"
  }
  selectedProvince.value = null
}

const selectRegion = () => {
  selectedProvince.value = null
  selectedMarker.value = null
}

// --- API Data ---
const provinceData = ref<Record<
  string,
  {
    cards: CatchmentOverviewProps[]
    sites: Site[]
    municipalities: Record<
      string,
      {
        cards: CatchmentOverviewProps[]
        sites: Site[]
        barangays: Site[]
      }
    >
  }
>>({})

const carData = ref<{ cards: CatchmentOverviewProps[]; sites: Site[] }>({
  cards: [],
  sites: []
})

// Add barangay lookup ref
const barangayLookup = ref<Record<string, any>>({})

// computed array of sites (province + all municipalities) for the currently selected province
const municipalitySites = computed<Site[]>(() => {
  const sel = selectedProvince.value
  if (!sel) return []
  const prov = provinceData.value[sel.name]
  if (!prov) return []
  const munSites = Object.values(prov.municipalities || {}).flatMap(m => m.sites || [])
  return [...(prov.sites || []), ...munSites]
})

// Add computed properties for barangay data
const barangayCards = computed(() => {
  if (!selectedMarker.value || selectedMarker.value.level !== 'barangay') return []
  return barangayLookup.value[selectedMarker.value.title]?.cards || []
})

const barangaySites = computed(() => {
  if (!selectedMarker.value || selectedMarker.value.level !== 'barangay') return []
  const brgy = barangayLookup.value[selectedMarker.value.title]
  if (!brgy) return []
  return [{
    code: brgy.barangay_id,
    name: brgy.barangay_name,
    status: "Active",
    cards: brgy.cards
  }]
})

const handleRowClick = (row: Site) => {
  console.log("row clicked", row)
}

onMounted(async () => {
  try {
    // Fetch province, municipality, barangay totals
    const [resProv, resMun, resBrgy] = await Promise.all([
      axios.get("/map/pkpsites/rtotal"),
      axios.get("/map/pkpsites/mtotal"),
      axios.get("/map/pkpsites/btotal")
    ])

    const apiProv = resProv.data.data
    const apiMun = resMun.data.data
    const apiBrgy = resBrgy.data.data
    const provinces: Record<
      string,
      {
        cards: CatchmentOverviewProps[]
        sites: Site[]
        municipalities: Record<string, { cards: CatchmentOverviewProps[]; sites: Site[]; barangays: Site[] }>
      }
    > = {}

    // Create barangay lookup map
    const lookup: Record<string, any> = {}
    apiBrgy.forEach((brgy: any) => {
      lookup[brgy.barangay_name] = {
        ...brgy,
        cards: [
          { title: "Total No. Sitio + Purok", value: Number(brgy.total_no_sitio_purok), subtitle: "" },
          { title: "Target Sitio + Purok", value: Number(brgy.total_target_sitio_purok), subtitle: "" },
          { title: "Total Sites", value: Number(brgy.site_count), subtitle: "" }
        ]
      }
    })
    barangayLookup.value = lookup

    // --- Provinces (key by province_name) ---
    apiProv.forEach((prov: any) => {
      const provCards = [
        { title: "Total No. Sitio + Purok", value: Number(prov.total_no_sitio_purok), subtitle: "" },
        { title: "Target Sitio + Purok", value: Number(prov.total_target_sitio_purok), subtitle: "" },
        { title: "Total Sites", value: Number(prov.site_count), subtitle: "" }
      ]
      provinces[prov.province_name] = {
        cards: provCards,
        sites: [
          {
            code: String(prov.province_id),
            name: prov.province_name,
            status: "Active",
            cards: provCards
          }
        ],
        municipalities: {}
      }
    })

    // --- Municipalities (attach under province by province_name) ---
    apiMun.forEach((mun: any) => {
      const munCards = [
        { title: "Total No. Sitio + Purok", value: Number(mun.total_no_sitio_purok), subtitle: "" },
        { title: "Target Sitio + Purok", value: Number(mun.total_target_sitio_purok), subtitle: "" },
        { title: "Total Sites", value: Number(mun.site_count), subtitle: "" }
      ]
      const provKey = mun.province_name
      if (!provinces[provKey]) {
        provinces[provKey] = { cards: [], sites: [], municipalities: {} }
      }
      provinces[provKey].municipalities[mun.municipality_name] = {
        cards: munCards,
        sites: [
          {
            code: String(mun.municipality_id),
            name: mun.municipality_name,
            status: "Active",
            cards: munCards
          }
        ],
        barangays: []
      }
    })

    // --- Barangays (attach under province_name -> municipality_name) ---
    apiBrgy.forEach((brgy: any) => {
      const brgyCards = [
        { title: "Total No. Sitio + Purok", value: Number(brgy.total_no_sitio_purok), subtitle: "" },
        { title: "Target Sitio + Purok", value: Number(brgy.total_target_sitio_purok), subtitle: "" },
        { title: "Total Sites", value: Number(brgy.site_count), subtitle: "" }
      ]
      const provKey = brgy.province_name
      const munKey = brgy.municipality_name
      if (provinces[provKey] && provinces[provKey].municipalities[munKey]) {
        provinces[provKey].municipalities[munKey].barangays.push({
          code: String(brgy.barangay_id),
          name: brgy.barangay_name,
          status: "Active",
          cards: brgyCards
        })
      }
    })

    provinceData.value = provinces

    // --- CAR Totals (grand totals from rtotal response) ---
    const grand = resProv.data.grand_totals || resProv.data.grandTotals || { total_no_sitio_purok: 0, total_target_sitio_purok: 0, site_count: 0 }
    carData.value = {
      cards: [
        { title: "Total No. Sitio + Purok", value: Number(grand.total_no_sitio_purok), subtitle: "" },
        { title: "Target Sitio + Purok", value: Number(grand.total_target_sitio_purok), subtitle: "" },
        { title: "Total Sites", value: Number(grand.site_count), subtitle: "" }
      ],
      sites: apiProv.map((prov: any) => ({
        code: String(prov.province_id),
        name: prov.province_name,
        status: "Active",
        cards: [
          { title: "Total No. Sitio + Purok", value: Number(prov.total_no_sitio_purok), subtitle: "" },
          { title: "Target Sitio + Purok", value: Number(prov.total_target_sitio_purok), subtitle: "" },
          { title: "Total Sites", value: Number(prov.site_count), subtitle: "" }
        ]
      }))
    }
  } catch (err) {
    console.error("Failed to fetch CAR totals:", err)
  }
})
</script>

<template>
  <div class="flex flex-col md:flex-row h-screen">
    <div class="flex-1">
      <MapView @province-selected="selectProvince" @marker-selected="selectMarker" @region-selected="selectRegion" />
    </div>
    <div
      class="w-full md:w-[48rem] border-t md:border-t-0 md:border-l bg-card text-card-foreground overflow-y-auto shadow-lg">
      <div class="p-4 sm:p-6 space-y-4">
        <div v-if="selectedProvince || selectedMarker" class="sticky top-0 bg-card pb-2">
          <Button variant="outline" size="sm" @click="selectRegion">
            ← Back to CAR
          </Button>
        </div>
        <!-- Province Mode: show province cards + municipalities as sites -->
        <ProvinceDashboard v-if="selectedProvince" level="province" :provinceName="selectedProvince.name"
          :cards="provinceData[selectedProvince.name]?.cards || []" :sites="municipalitySites"
          @row-clicked="handleRowClick" />
        <!-- Barangay Mode -->
        <ProvinceDashboard v-else-if="selectedMarker?.level === 'barangay'"
          :provinceName="`${selectedMarker.title} (Barangay)`" :cards="barangayCards" :sites="barangaySites"
          level="municipality" />
        <!-- Province Capital Marker Mode -->
        <ProvinceDashboard v-else-if="selectedMarker?.level === 'province'" :provinceName="selectedMarker.title"
          :cards="[]" :sites="[]" level="municipality" />
        <!-- Region (CAR) Mode -->
        <ProvinceDashboard v-else provinceName="Cordillera Administrative Region" :cards="carData.cards"
          :sites="carData.sites" level="region" />
      </div>
    </div>
  </div>
</template>
