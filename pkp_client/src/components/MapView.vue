<script setup lang="ts">
import { onMounted } from "vue"
import L, { Map, GeoJSON, Layer } from "leaflet"
import type { PathOptions } from "leaflet"
import "leaflet/dist/leaflet.css"
import type { Feature, FeatureCollection, Geometry } from "geojson"

const emit = defineEmits<{
  (e: "province-selected", payload: { name: string; info: Record<string, unknown> }): void
  (e: "marker-selected", payload: { title: string; population: number; region: string }): void
}>()

// Province color mapping
const provinceColors: Record<string, string> = {
  "Cordillera Administrative Region": "#FF5733",
  "Abra": "#FF9999",
  "Apayao": "#99CCFF",
  "Benguet": "#66CC66",
  "Ifugao": "#FFCC66",
  "Kalinga": "#CC99FF",
  "Mountain Province": "#FF66B2",
  "Baguio City": "#66FFFF",
}

onMounted(async () => {
  const map: Map = L.map("map").setView([16.6, 121.2], 7)

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map)

  // Fetch GeoJSON
  const res = await fetch("http://localhost:8000/api/geojson")
  const provinces: FeatureCollection = await res.json()

  const geojsonLayer: GeoJSON = L.geoJSON(provinces, {
    style: (feature?: Feature<Geometry, any>): PathOptions => {
      const provName: string | undefined = feature?.properties?.name
      const color: string = (provName && provinceColors[provName]) || "#CCCCCC"
      return {
        color,
        weight: 2,
        fillColor: color,
        fillOpacity: 0.4,
      }
    },
    onEachFeature: (feature: Feature<Geometry, any>, layer: Layer) => {
      if (feature.properties?.name) {
        layer.bindTooltip(feature.properties.name)
      }

      layer.on("click", () => {
        if ("getBounds" in layer) {
          map.fitBounds((layer as any).getBounds(), { padding: [20, 20] })
        }
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
  addMarker(17.6002, 120.617, "Bangued, Abra", 250000, "Cordillera Administrative Region")
  addMarker(18.0182, 121.171, "Kabugao, Apayao", 125000, "Cordillera Administrative Region")
  addMarker(16.455, 120.5887, "La Trinidad, Benguet", 450000, "Cordillera Administrative Region")
  addMarker(16.799, 121.121, "Lagawe, Ifugao", 200000, "Cordillera Administrative Region")
  addMarker(17.479, 121.4583, "Tabuk, Kalinga", 220000, "Cordillera Administrative Region")
  addMarker(17.0899, 120.9775, "Bontoc, Mountain Province", 160000, "Cordillera Administrative Region")
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
