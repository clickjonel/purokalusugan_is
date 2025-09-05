<template>
  <div id="map" class="w-full h-full"></div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import L from "leaflet"
import "leaflet/dist/leaflet.css"

const emit = defineEmits(["province-selected", "marker-selected"])

const provinceColors = {
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
  const map = L.map("map").setView([16.6, 121.2], 7)

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map)

  // Fetch GeoJSON
  const res = await fetch("http://localhost:8000/api/geojson")
  const provinces = await res.json()

  const geojsonLayer = L.geoJSON(provinces, {
    style: (feature) => {
      const provName = feature.geometry.properties.name
      const color = provinceColors[provName] || "#CCCCCC"
      return {
        color,
        weight: 2,
        fillColor: color,
        fillOpacity: 0.4,
      }
    },
    onEachFeature: (feature, layer) => {
      layer.bindTooltip(feature.properties.name)

      layer.on("click", () => {
        map.fitBounds(layer.getBounds(), { padding: [20, 20] })
        emit("province-selected", {
          name: feature.properties.name,
          info: feature.properties,
        })
      })
    },
  }).addTo(map)

  map.fitBounds(geojsonLayer.getBounds())

  // Add markers (capitals)
  function addMarker(lat, lng, title, population, region) {
    L.marker([lat, lng]).addTo(map).on("click", () => {
      map.setView([lat, lng], 11)
      emit("marker-selected", { title, population, region })
    })
  }

  addMarker(17.6002, 120.6170, "Bangued, Abra", 250000, "Cordillera Administrative Region")
  addMarker(18.0182, 121.1710, "Kabugao, Apayao", 125000, "Cordillera Administrative Region")
  addMarker(16.4550, 120.5887, "La Trinidad, Benguet", 450000, "Cordillera Administrative Region")
  addMarker(16.7990, 121.1210, "Lagawe, Ifugao", 200000, "Cordillera Administrative Region")
  addMarker(17.4790, 121.4583, "Tabuk, Kalinga", 220000, "Cordillera Administrative Region")
  addMarker(17.0899, 120.9775, "Bontoc, Mountain Province", 160000, "Cordillera Administrative Region")
  addMarker(16.4023, 120.5960, "Baguio City", 345000, "Cordillera Administrative Region")
})
</script>

<style>
#map {
  min-height: 300px;
  /* Ensures it's usable on mobile */
}
</style>
