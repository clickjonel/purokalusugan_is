<template>
  <div id="map" style="height: 100%; width: 100%"></div>
</template>

<script setup>
import { onMounted, defineEmits } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const emit = defineEmits(['province-selected'])

onMounted(async () => {
  const map = L.map('map').setView([16.6, 121.2], 7)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map)


  const res = await fetch('../assets/cordillera.geojson')
  const geoData = await res.json()

  const geojsonLayer = L.geoJSON(geoData, {
    onEachFeature: (feature, layer) => {
      layer.on('click', () => {
        const bounds = layer.getBounds()
        map.fitBounds(bounds, { padding: [20, 20] })

        emit('province-selected', {
          name: feature.properties.name,
          info: feature.properties,
        })
      })
      layer.bindTooltip(feature.properties.name)
    },
    style: {
      color: '#3388ff',
      weight: 2
    }
  }).addTo(map)
})
</script>
