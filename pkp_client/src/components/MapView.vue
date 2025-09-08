<script setup lang="ts">
import { onMounted, ref } from "vue"
import L, { Map, GeoJSON, Layer } from "leaflet"
import type { PathOptions } from "leaflet"
import "leaflet/dist/leaflet.css"
import type { Feature, FeatureCollection } from "geojson"

const emit = defineEmits<{
  (e: "province-selected", payload: { name: string; info: Record<string, unknown> }): void
  (e: "marker-selected", payload: { title: string; population: number; region: string }): void
  (e: "region-selected"): void // <-- New event for CAR
}>()
// Default and fallback colors
const defaultColor = "#3399ff"
const fallbackColor = "#00cc66"
// Province color mapping
const provinceColors: Record<string, string> = {
  "Cordillera Administrative Region": "#FF5733", // Warm orange (kept as your primary region color)
  "Abra": "#FF9999",                  // Soft red (kept, but adjusted below for better contrast)
  "Apayao": "#FFD700",                // Gold (replaces light blue)
  "Benguet": "#FF8C42",               // Terracotta (replaces green)
  "Ifugao": "#FF69B4",                // Hot pink
  "Kalinga": "#BA55D3",               // Medium orchid
  "Mountain Province": "#FF4500",     // Orange-red
  "Baguio City": "#DA70D6",           // Orchid (replaces cyan)
};
// Track currently selected province layer
const selectedLayer = ref<Layer | null>(null)

onMounted(async () => {
  const map: Map = L.map("map").setView([16.6, 121.2], 7)

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map)

  // Fetch GeoJSON
  const res = await fetch("http://localhost:8000/api/geojson")
  const provinces: FeatureCollection = await res.json()

  const geojsonLayer: GeoJSON = L.geoJSON(provinces, {
    style: (feature?: Feature<any>): PathOptions => {  // <-- Add this function
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
        map.fitBounds((layer as any).getBounds(), { padding: [20, 20] })
        emit("province-selected", {
          name: feature.properties?.name ?? "Unknown",
          info: feature.properties ?? {},
        })
      })
    },
  }).addTo(map)

  map.fitBounds(geojsonLayer.getBounds())

  // Add markers with typing
  function addMarker(
    lat: number,
    lng: number,
    title: string,
    population: number,
    region: string
  ): void {
    L.marker([lat, lng]).addTo(map).on("click", () => {
      map.setView([lat, lng], 11)
      emit("marker-selected", { title, population, region })
    })
  }

  // Capital markers
  addMarker(17.6002, 120.617, "Abra", 250000, "Cordillera Administrative Region")
  addMarker(18.0182, 121.171, "Apayao", 125000, "Cordillera Administrative Region")
  addMarker(16.455, 120.5887, "Benguet", 450000, "Cordillera Administrative Region")
  addMarker(16.799, 121.121, "Ifugao", 200000, "Cordillera Administrative Region")
  addMarker(17.479, 121.4583, "Kalinga", 220000, "Cordillera Administrative Region")
  addMarker(17.0899, 120.9775, "Mountain Province", 160000, "Cordillera Administrative Region")
  addMarker(16.4023, 120.596, "Baguio City", 345000, "Cordillera Administrative Region")
})
</script>

<style>
#map {
  min-height: 300px;
  /* Ensures it's usable on mobile */
}
</style>

<template>
  <div id="map" class="w-full h-full font-poppins"></div>
</template>
