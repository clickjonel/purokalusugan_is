<script setup lang="ts">
import { onMounted, ref } from "vue"
import L, { Map, GeoJSON, Layer } from "leaflet"
import type { PathOptions } from "leaflet"
import "leaflet/dist/leaflet.css"
import type { Feature, FeatureCollection } from "geojson"

const emit = defineEmits<{
  (e: "province-selected", payload: { name: string; info: Record<string, unknown> }): void
  (e: "marker-selected", payload: {
    title: string
    population?: number
    region: string
    level?: "province" | "municipality" | "barangay"
    status?: string
  }): void
  (e: "region-selected"): void
}>()

// Default and fallback colors
const defaultColor = "#3399ff"
const fallbackColor = "#00cc66"

// Province color mapping
const provinceColors: Record<string, string> = {
  "Cordillera Administrative Region": "#FF5733",
  "Abra": "#FF9999",
  "Apayao": "#FFD700",
  "Benguet": "#FF8C42",
  "Ifugao": "#FF69B4",
  "Kalinga": "#BA55D3",
  "Mountain Province": "#FF4500",
  "Baguio City": "#DA70D6",
}

const selectedLayer = ref<Layer | null>(null)

// 🔹 Municipality + Barangay dataset
const municipalityData: Record<
  string,
  {
    name: string
    lat: number
    lng: number
    barangays: { name: string; lat: number; lng: number }[]
  }[]
> = {
  Abra: [
    {
      name: "Bangued", lat: 17.6002, lng: 120.617, barangays: [
        { name: "Zone 1", lat: 17.605, lng: 120.620 },
        { name: "Zone 2", lat: 17.602, lng: 120.615 },
      ]
    },
    {
      name: "Boliney", lat: 17.530, lng: 120.750, barangays: [
        { name: "Barangay A", lat: 17.531, lng: 120.752 },
        { name: "Barangay B", lat: 17.528, lng: 120.748 },
      ]
    },
    {
      name: "Bucay", lat: 17.467, lng: 120.717, barangays: [
        { name: "Barangay 1", lat: 17.468, lng: 120.718 },
        { name: "Barangay 2", lat: 17.466, lng: 120.716 },
      ]
    },
    {
      name: "Pidigan", lat: 17.585, lng: 120.633, barangays: [
        { name: "Barangay 1", lat: 17.586, lng: 120.634 },
        { name: "Barangay 2", lat: 17.584, lng: 120.632 },
      ]
    },
    // ... add other Abra municipalities
  ],
  Apayao: [
    {
      name: "Conner", lat: 17.800, lng: 121.350, barangays: [
        { name: "Barangay A", lat: 17.801, lng: 121.351 },
        { name: "Barangay B", lat: 17.799, lng: 121.349 },
      ]
    },
    {
      name: "Flora", lat: 18.200, lng: 121.400, barangays: [
        { name: "Barangay A", lat: 18.201, lng: 121.401 },
        { name: "Barangay B", lat: 18.199, lng: 121.399 },
      ]
    },
    {
      name: "Kabugao", lat: 18.000, lng: 121.283, barangays: [
        { name: "Barangay A", lat: 18.001, lng: 121.284 },
        { name: "Barangay B", lat: 17.999, lng: 121.282 },
      ]
    },
    // ... add other Apayao municipalities
  ],
  Benguet: [
    {
      name: "La Trinidad", lat: 16.455, lng: 120.5887, barangays: [
        { name: "Poblacion", lat: 16.456, lng: 120.589 },
        { name: "Shilan", lat: 16.454, lng: 120.587 },
      ]
    },
    {
      name: "Itogon", lat: 16.350, lng: 120.700, barangays: [
        { name: "Barangay A", lat: 16.351, lng: 120.701 },
        { name: "Barangay B", lat: 16.349, lng: 120.699 },
      ]
    },
    {
      name: "Tuba", lat: 16.300, lng: 120.550, barangays: [
        { name: "Barangay A", lat: 16.301, lng: 120.551 },
        { name: "Barangay B", lat: 16.299, lng: 120.549 },
      ]
    },
    // ... add other Benguet municipalities
  ],
  Ifugao: [
    {
      name: "Lagawe", lat: 16.799, lng: 121.121, barangays: [
        { name: "Barangay A", lat: 16.800, lng: 121.122 },
        { name: "Barangay B", lat: 16.798, lng: 121.120 },
      ]
    },
    {
      name: "Kiangan", lat: 16.783, lng: 121.083, barangays: [
        { name: "Barangay A", lat: 16.784, lng: 121.084 },
        { name: "Barangay B", lat: 16.782, lng: 121.082 },
      ]
    },
    {
      name: "Banaue", lat: 16.917, lng: 121.050, barangays: [
        { name: "Barangay A", lat: 16.918, lng: 121.051 },
        { name: "Barangay B", lat: 16.916, lng: 121.049 },
      ]
    },
    // ... add other Ifugao municipalities
  ],
  Kalinga: [
    {
      name: "Tabuk City", lat: 17.479, lng: 121.4583, barangays: [
        { name: "Bulanao", lat: 17.480, lng: 121.460 },
        { name: "Dagupan", lat: 17.478, lng: 121.455 },
      ]
    },
    {
      name: "Pinukpuk", lat: 17.617, lng: 121.300, barangays: [
        { name: "Barangay A", lat: 17.618, lng: 121.301 },
        { name: "Barangay B", lat: 17.616, lng: 121.299 },
      ]
    },
    {
      name: "Rizal", lat: 17.517, lng: 121.450, barangays: [
        { name: "Barangay A", lat: 17.518, lng: 121.451 },
        { name: "Barangay B", lat: 17.516, lng: 121.449 },
      ]
    },
    // ... add other Kalinga municipalities
  ],
  "Mountain Province": [
    {
      name: "Bontoc", lat: 17.0899, lng: 120.9775, barangays: [
        { name: "Barangay A", lat: 17.091, lng: 120.978 },
        { name: "Barangay B", lat: 17.088, lng: 120.976 },
      ]
    },
    {
      name: "Sagada", lat: 17.083, lng: 120.900, barangays: [
        { name: "Barangay A", lat: 17.084, lng: 120.901 },
        { name: "Barangay B", lat: 17.082, lng: 120.899 },
      ]
    },
    {
      name: "Tadian", lat: 16.933, lng: 120.817, barangays: [
        { name: "Barangay A", lat: 16.934, lng: 120.818 },
        { name: "Barangay B", lat: 16.932, lng: 120.816 },
      ]
    },
    // ... add other Mountain Province municipalities
  ],
  "Baguio City": [
    {
      name: "Baguio City Proper", lat: 16.4023, lng: 120.596, barangays: [
        { name: "Barangay A", lat: 16.403, lng: 120.597 },
        { name: "Barangay B", lat: 16.401, lng: 120.595 },
      ]
    },
  ],
}


// 🔹 Track municipality + barangay markers
const municipalityMarkers: L.Marker[] = []

onMounted(async () => {
  const map: Map = L.map("map").setView([16.6, 121.2], 7)

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map)

  // Fetch GeoJSON
  const res = await fetch("http://localhost:8000/api/geojson")
  const provinces: FeatureCollection = await res.json()

  const geojsonLayer: GeoJSON = L.geoJSON(provinces, {
    style: (feature?: Feature<any>): PathOptions => {
      const provName = feature?.properties?.name ?? "Unknown";
      return {
        color: provinceColors[provName] || defaultColor,
        weight: 2,
        fillColor: provinceColors[provName] || fallbackColor,
        fillOpacity: 0.2,
      };
    },
    onEachFeature: (feature: Feature<any>, layer: Layer) => {
      const provName = feature.properties?.name ?? "Unknown"
      if (feature.properties?.name) {
        layer.bindTooltip(feature.properties.name)
      }

      layer.on("click", (e) => {
        e.originalEvent?.stopPropagation()

        if ("getBounds" in layer) {
          map.fitBounds((layer as any).getBounds(), { padding: [20, 20] })
        }

        if (selectedLayer.value) {
          (selectedLayer.value as L.Path).setStyle({ color: defaultColor, fillColor: defaultColor })
        }

        const color = provinceColors[provName] || defaultColor;
        (layer as L.Path).setStyle({ color, fillColor: color, fillOpacity: 0.5 })
        selectedLayer.value = layer

        emit("province-selected", {
          name: feature.properties?.name ?? "Unknown",
          info: feature.properties ?? {},
        })

        // 🔹 Show municipalities + barangays for this province
        showMunicipalities(provName, map)
      })
    },
  }).addTo(map)

  map.fitBounds(geojsonLayer.getBounds())
  // Province-level custom icon (bigger than default)
  const bigIcon = L.icon({
    iconUrl: "https://cdn-icons-png.flaticon.com/512/854/854878.png", // you can change this to another pin
    iconSize: [40, 40], // default is [25, 41] → make it bigger
    iconAnchor: [20, 40], // centers the bottom tip
    popupAnchor: [0, -35], // adjust popup position
  })

  // Province-level capital markers
  function addMarker(
    lat: number,
    lng: number,
    title: string,
    population: number,
    region: string
  ): void {
    L.marker([lat, lng], { icon: bigIcon })
      .addTo(map)
      .bindTooltip(`${title} (Capital)`) // tooltip shows province
      .on("click", () => {
        map.setView([lat, lng], 11)
        emit("marker-selected", { title, population, region })
      })
  }

  addMarker(17.6002, 120.617, "Abra", 250000, "Cordillera Administrative Region")
  addMarker(18.0182, 121.171, "Apayao", 125000, "Cordillera Administrative Region")
  addMarker(16.455, 120.5887, "Benguet", 450000, "Cordillera Administrative Region")
  addMarker(16.799, 121.121, "Ifugao", 200000, "Cordillera Administrative Region")
  addMarker(17.479, 121.4583, "Kalinga", 220000, "Cordillera Administrative Region")
  addMarker(17.0899, 120.9775, "Mountain Province", 160000, "Cordillera Administrative Region")
  addMarker(16.4023, 120.596, "Baguio City", 345000, "Cordillera Administrative Region")
})

// 🔹 Show municipalities + barangays when province is clicked
function showMunicipalities(province: string, map: Map) {
  // Remove old markers
  municipalityMarkers.forEach(m => m.remove())
  municipalityMarkers.length = 0

  const municipalities = municipalityData[province] || []
  municipalities.forEach(muni => {
    // Municipality marker
    const muniMarker = L.marker([muni.lat, muni.lng])
      .addTo(map)
      .bindTooltip(`${muni.name} (Municipality)`)
      .on("click", () => {
        map.setView([muni.lat, muni.lng], 12)
        emit("marker-selected", { title: muni.name, population: 0, region: province })
      })

    municipalityMarkers.push(muniMarker)
    const barangayIcon = L.icon({
      iconUrl: "https://cdn-icons-png.flaticon.com/512/149/149060.png",
      iconSize: [20, 20],
      iconAnchor: [10, 20],
      popupAnchor: [0, -20],
    })
    function addBarangayMarker(
      lat: number,
      lng: number,
      barangay: { code: string; name: string; status: string },
      province: string
    ): void {
      L.marker([lat, lng], { icon: barangayIcon })
        .addTo(map)
        .bindTooltip(`${barangay.name} (${barangay.status})`)
        .on("click", () => {
          map.setView([lat, lng], 13)
          emit("marker-selected", {
            title: barangay.name,
            region: province,
            level: "barangay",
            status: barangay.status,
          })
        })
    }
    // Barangay markers
    muni.barangays.forEach(brgy => {
      addBarangayMarker(
        brgy.lat,
        brgy.lng,
        { code: "", name: brgy.name, status: "active" }, // or whatever data you want
        province
      )
    })
  })
}
</script>

<style>
#map {
  min-height: 300px;
}
</style>

<template>
  <div id="map" class="w-full h-full font-poppins"></div>
</template>
