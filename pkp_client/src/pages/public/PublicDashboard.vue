<script setup lang="ts">
import { ref, computed, onMounted } from "vue"
import MapView from "@/components/MapView.vue"
import ProvinceDashboard from "@/components/ProvinceDashboard.vue"
import type { CatchmentOverviewProps, Site } from "@/components/ProvinceDashboard.vue"
import axios from '@/axios/axios';

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
const selectMarker = (marker: {
  title: string
  population?: number
  region: string
  level?: "province" | "municipality" | "barangay"
  status?: string
}) => {
  selectedMarker.value = {
    ...marker,
    level: marker.level ?? "barangay", // fallback
    status: marker.status ?? "Active"  // fallback
  } as MarkerPayload
  selectedProvince.value = null
}


const selectRegion = () => {
  selectedProvince.value = null;
  selectedMarker.value = null;
};
const sites = ref<any[]>([])

onMounted(async () => {
  try {
    const res = await axios.get("map/dashboardSites")
    sites.value = res.data
  } catch (err) {
    console.error("Failed to fetch sites:", err)
  }
})
// Example dataset
const provinceData: Record<string, { cards: CatchmentOverviewProps[]; sites: Site[] }> = {
  Abra: {
    cards: [
      { title: "Total No. of Barangay", value: 200, subtitle: "" },
      { title: "Total No. of Purok", value: 247, subtitle: "" },
      { title: "Total No. PuroKalusugan Sites", value: 130, subtitle: "" },
      { title: "Coverage Rate", value: "30%", subtitle: "" },
      { title: "Field Operations", value: "100/130", subtitle: "" },
      { title: "Training Completed", value: "0/130", subtitle: "" },
      { title: "Monitoring Activities", value: 24, subtitle: "This month" },
      { title: "HRH Deployed", value: 287, subtitle: "Active field personnel" },
      { title: "High Priority Areas", value: 12, subtitle: "Districts" },
      { title: "Population Enumeration", value: "500/1000", subtitle: "91.1%" },
    ],
    sites: [
      { code: "A001", name: "Adams (Pob.)", status: "Active" },
      { code: "A002", name: "Bani", status: "Inactive" },
      { code: "A003", name: "Buyon", status: "Active" },
      { code: "A004", name: "Cabaruan", status: "Inactive" },
      { code: "A005", name: "Cabulaoan", status: "Active" },
    ],
  },
  Apayao: {
    cards: [
      { title: "Total No. of Barangay", value: 133, subtitle: "" },
      { title: "Total No. of Purok", value: 190, subtitle: "" },
      { title: "Total No. PuroKalusugan Sites", value: 75, subtitle: "" },
      { title: "Coverage Rate", value: "40%", subtitle: "" },
      { title: "Field Operations", value: "80/100", subtitle: "" },
      { title: "Training Completed", value: "10/100", subtitle: "" },
      { title: "Monitoring Activities", value: 12, subtitle: "This month" },
      { title: "HRH Deployed", value: 120, subtitle: "Active field personnel" },
      { title: "High Priority Areas", value: 6, subtitle: "Districts" },
      { title: "Population Enumeration", value: "300/400", subtitle: "75%" },
    ],
    sites: [
      { code: "AP001", name: "Barangay Alpha", status: "Active" },
      { code: "AP002", name: "Barangay Beta", status: "Active" },
      { code: "AP003", name: "Barangay Gamma", status: "Inactive" },
    ],
  },
  Benguet: {
    cards: [
      { title: "Total No. of Barangay", value: 140, subtitle: "" },
      { title: "Total No. of Purok", value: 220, subtitle: "" },
      { title: "Total No. PuroKalusugan Sites", value: 95, subtitle: "" },
      { title: "Coverage Rate", value: "60%", subtitle: "" },
      { title: "Field Operations", value: "70/95", subtitle: "" },
      { title: "Training Completed", value: "20/95", subtitle: "" },
      { title: "Monitoring Activities", value: 15, subtitle: "This month" },
      { title: "HRH Deployed", value: 180, subtitle: "Active field personnel" },
      { title: "High Priority Areas", value: 4, subtitle: "Districts" },
      { title: "Population Enumeration", value: "600/800", subtitle: "75%" },
    ],
    sites: [
      { code: "BG001", name: "Barangay Ambiong", status: "Active" },
      { code: "BG002", name: "Barangay Betag", status: "Inactive" },
      { code: "BG003", name: "Barangay Poblacion", status: "Active" },
    ],
  },
  Kalinga: {
    cards: [
      { title: "Total No. of Barangay", value: 152, subtitle: "" },
      { title: "Total No. of Purok", value: 210, subtitle: "" },
      { title: "Total No. PuroKalusugan Sites", value: 80, subtitle: "" },
      { title: "Coverage Rate", value: "35%", subtitle: "" },
      { title: "Field Operations", value: "50/80", subtitle: "" },
      { title: "Training Completed", value: "5/80", subtitle: "" },
      { title: "Monitoring Activities", value: 18, subtitle: "This month" },
      { title: "HRH Deployed", value: 95, subtitle: "Active field personnel" },
      { title: "High Priority Areas", value: 3, subtitle: "Districts" },
      { title: "Population Enumeration", value: "400/700", subtitle: "57%" },
    ],
    sites: [
      { code: "KL001", name: "Barangay Bulanao", status: "Active" },
      { code: "KL002", name: "Barangay Laya", status: "Inactive" },
      { code: "KL003", name: "Barangay Dagupan", status: "Active" },
    ],
  },
  Ifugao: {
    cards: [
      { title: "Total No. of Barangay", value: 175, subtitle: "" },
      { title: "Total No. of Purok", value: 250, subtitle: "" },
      { title: "Total No. PuroKalusugan Sites", value: 110, subtitle: "" },
      { title: "Coverage Rate", value: "55%", subtitle: "" },
      { title: "Field Operations", value: "90/110", subtitle: "" },
      { title: "Training Completed", value: "30/110", subtitle: "" },
      { title: "Monitoring Activities", value: 20, subtitle: "This month" },
      { title: "HRH Deployed", value: 210, subtitle: "Active field personnel" },
      { title: "High Priority Areas", value: 8, subtitle: "Districts" },
      { title: "Population Enumeration", value: "700/900", subtitle: "78%" },
    ],
    sites: [
      { code: "IF001", name: "Barangay Banaue", status: "Active" },
      { code: "IF002", name: "Barangay Hingyon", status: "Active" },
      { code: "IF003", name: "Barangay Lagawe", status: "Inactive" },
      { code: "IF004", name: "Barangay Lamut", status: "Active" },
    ],
  },
  "Mountain Province": {
    cards: [
      { title: "Total No. of Barangay", value: 180, subtitle: "" },
      { title: "Total No. of Purok", value: 230, subtitle: "" },
      { title: "Total No. PuroKalusugan Sites", value: 100, subtitle: "" },
      { title: "Coverage Rate", value: "50%", subtitle: "" },
      { title: "Field Operations", value: "85/100", subtitle: "" },
      { title: "Training Completed", value: "25/100", subtitle: "" },
      { title: "Monitoring Activities", value: 16, subtitle: "This month" },
      { title: "HRH Deployed", value: 190, subtitle: "Active field personnel" },
      { title: "High Priority Areas", value: 5, subtitle: "Districts" },
      { title: "Population Enumeration", value: "550/850", subtitle: "65%" },
    ],
    sites: [
      { code: "MP001", name: "Barangay Bontoc", status: "Active" },
      { code: "MP002", name: "Barangay Sadanga", status: "Active" },
      { code: "MP003", name: "Barangay Sagada", status: "Inactive" },
      { code: "MP004", name: "Barangay Paracelis", status: "Active" },
    ],
  },
  "City of Baguio": {
    cards: [
      { title: "Total No. of Barangay", value: 129, subtitle: "" },
      { title: "Total No. of Purok", value: 300, subtitle: "" },
      { title: "Total No. PuroKalusugan Sites", value: 150, subtitle: "" },
      { title: "Coverage Rate", value: "80%", subtitle: "" },
      { title: "Field Operations", value: "120/150", subtitle: "" },
      { title: "Training Completed", value: "50/150", subtitle: "" },
      { title: "Monitoring Activities", value: 25, subtitle: "This month" },
      { title: "HRH Deployed", value: 300, subtitle: "Active field personnel" },
      { title: "High Priority Areas", value: 10, subtitle: "Districts" },
      { title: "Population Enumeration", value: "900/1000", subtitle: "90%" },
    ],
    sites: [
      { code: "BC001", name: "Barangay Session Road", status: "Active" },
      { code: "BC002", name: "Barangay Burnham", status: "Active" },
      { code: "BC003", name: "Barangay Gibraltar", status: "Inactive" },
      { code: "BC004", name: "Barangay Loakan", status: "Active" },
      { code: "BC005", name: "Barangay Mines View", status: "Active" },
    ],
  },
};


const carData = computed(() => {
  // Initialize totals with proper typing
  const totals = {
    totalBarangay: 0,
    totalPurok: 0,
    totalPuroKalusuganSites: 0,
    totalFieldOperations: { numerator: 0, denominator: 0 },
    totalTrainingCompleted: { numerator: 0, denominator: 0 },
    totalMonitoringActivities: 0,
    totalHRHDeployed: 0,
    totalHighPriorityAreas: 0,
    totalPopulationEnumeration: { numerator: 0, denominator: 0 },
    totalSites: 0,
  };

  // Aggregate data from all provinces
  Object.values(provinceData).forEach((province) => {
    // Convert values to numbers explicitly
    totals.totalBarangay += Number(province.cards[0]?.value || 0);
    totals.totalPurok += Number(province.cards[1]?.value || 0);
    totals.totalPuroKalusuganSites += Number(province.cards[2]?.value || 0);

    // Handle fractional values safely
    const fieldOps = String(province.cards[4]?.value || "0/0");
    const [num, denom] = fieldOps.split("/").map(Number);
    totals.totalFieldOperations.numerator += num || 0;
    totals.totalFieldOperations.denominator += denom || 0;

    const training = String(province.cards[5]?.value || "0/0");
    const [tNum, tDenom] = training.split("/").map(Number);
    totals.totalTrainingCompleted.numerator += tNum || 0;
    totals.totalTrainingCompleted.denominator += tDenom || 0;

    totals.totalMonitoringActivities += Number(province.cards[6]?.value || 0);
    totals.totalHRHDeployed += Number(province.cards[7]?.value || 0);
    totals.totalHighPriorityAreas += Number(province.cards[8]?.value || 0);

    const popEnum = String(province.cards[9]?.value || "0/0");
    const [pNum, pDenom] = popEnum.split("/").map(Number);
    totals.totalPopulationEnumeration.numerator += pNum || 0;
    totals.totalPopulationEnumeration.denominator += pDenom || 0;
  });

  // Generate CAR overview cards
  const allCards: CatchmentOverviewProps[] = [
    { title: "Total No. of Barangay", value: totals.totalBarangay, subtitle: "" },
    { title: "Total No. of Purok", value: totals.totalPurok, subtitle: "" },
    { title: "Total No. PuroKalusugan Sites", value: totals.totalPuroKalusuganSites, subtitle: "" },
    {
      title: "Average Coverage Rate",
      value: totals.totalFieldOperations.denominator > 0
        ? `${Math.round(
          (totals.totalFieldOperations.numerator / totals.totalFieldOperations.denominator) * 100
        )}%`
        : "0%",
      subtitle: `(${totals.totalFieldOperations.numerator}/${totals.totalFieldOperations.denominator})`,
    },
    {
      title: "Total Field Operations",
      value: `${totals.totalFieldOperations.numerator}/${totals.totalFieldOperations.denominator}`,
      subtitle: "",
    },
    {
      title: "Total Training Completed",
      value: `${totals.totalTrainingCompleted.numerator}/${totals.totalTrainingCompleted.denominator}`,
      subtitle: "",
    },
    { title: "Total Monitoring Activities", value: totals.totalMonitoringActivities, subtitle: "This month" },
    { title: "Total HRH Deployed", value: totals.totalHRHDeployed, subtitle: "Active field personnel" },
    { title: "Total High Priority Areas", value: totals.totalHighPriorityAreas, subtitle: "Districts" },
    {
      title: "Total Population Enumeration",
      value: `${totals.totalPopulationEnumeration.numerator}/${totals.totalPopulationEnumeration.denominator}`,
      subtitle: totals.totalPopulationEnumeration.denominator > 0
        ? `${Math.round(
          (totals.totalPopulationEnumeration.numerator / totals.totalPopulationEnumeration.denominator) * 100
        )}%`
        : "0%",
    },
  ];

  // Flatten all sites from all provinces
  const allSites: Site[] = Object.values(provinceData).flatMap((prov) => prov.sites);

  return { cards: allCards, sites: allSites };
});

</script>

<template>
  <div class="flex flex-col md:flex-row h-screen">
    <!-- Map Section -->
    <div class="flex-1">
      <MapView ref="mapViewRef" @province-selected="selectProvince" @marker-selected="selectMarker"
        @region-selected="selectRegion" />
    </div>

    <!-- Sidebar Panel -->
    <div
      class="w-full md:w-[48rem] border-t md:border-t-0 md:border-l bg-card text-card-foreground overflow-y-auto shadow-lg">
      <div class="p-4 sm:p-6 space-y-4">
        <!-- Back Button (sticky at top) -->
        <div v-if="selectedProvince || selectedMarker" class="sticky top-0 bg-card pb-2">
          <Button variant="outline" size="sm" @click="selectRegion">
            ← Back to CAR
          </Button>
        </div>

        <!-- Province Mode -->
        <ProvinceDashboard v-if="selectedProvince" :provinceName="selectedProvince.name"
          :cards="provinceData[selectedProvince.name]?.cards || []"
          :sites="provinceData[selectedProvince.name]?.sites || []" />

        <!-- Barangay Mode -->
        <ProvinceDashboard v-else-if="selectedMarker?.level === 'barangay'"
          :provinceName="`${selectedMarker.title} (Barangay)`" :cards="[]"
          :sites="[{ code: 'BRG', name: selectedMarker.title, status: selectedMarker.status || '' }]" />

        <!-- Province Capital Marker Mode -->
        <ProvinceDashboard v-else-if="selectedMarker?.level === 'province'" :provinceName="selectedMarker.title"
          :cards="[]" :sites="[]" />

        <!-- Region (CAR) Mode -->
        <ProvinceDashboard v-else provinceName="Cordillera Administrative Region" :cards="carData.cards"
          :sites="carData.sites" />

      </div>
    </div>
  </div>
</template>