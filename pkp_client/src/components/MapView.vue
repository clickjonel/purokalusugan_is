<script setup lang="ts">
import { onMounted, ref } from "vue"
import L, { Map, GeoJSON, Layer } from "leaflet"
import type { PathOptions } from "leaflet"
import "leaflet/dist/leaflet.css"
import type { Feature, FeatureCollection } from "geojson"
import axios from '@/axios/axios'

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

const provinceColors: Record<string, string> = {
  "Cordillera Administrative Region": "#FFFFFF00", // transparent white
  "Abra": "#4682B4",        // Steel Blue
  "Apayao": "#FFB347",      // Soft Amber
  "Benguet": "#20B2AA",     // Light Sea Green
  "Ifugao": "#9370DB",      // Medium Purple
  "Kalinga": "#5F9EA0",     // Cadet Blue
  "Mountain Province": "#708090",  // Slate Gray
  "Baguio City": "#FFD700", // Gold
}

const defaultColor = "#FFFFFF00" // transparent white by default
const fallbackColor = "#3CB375"

const selectedLayer = ref<Layer | null>(null)

onMounted(async () => {
  const map: Map = L.map("map", {
    center: [17.0, 119.0], // Central CAR
    zoom: 9,
    minZoom: 9,
    maxZoom: 20,
  });

  // Define CAR bounds
  const carBounds = L.latLngBounds(
    L.latLng(15, 119.705),   // Southwest corner
    L.latLng(20, 122.618)    // Northeast corner
  );
  map.setMaxBounds(carBounds);
  map.on("drag", () => map.panInsideBounds(carBounds, { animate: false }));

  // Basemap
  L.tileLayer("https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png", {
    attribution: '&copy; <a href="https://carto.com/">CartoDB</a>',
    subdomains: "abcd",
    maxZoom: 20
  }).addTo(map);
  L.control.scale().addTo(map);

  // Fetch GeoJSON (Provinces only)
  const res = await axios.get("/map/geojson");
  const provinces: FeatureCollection = res.data;
  const geojsonLayer: GeoJSON = L.geoJSON(provinces, {
    style: (feature?: Feature<any>): PathOptions => {
      const provName = feature?.geometry?.properties?.name ?? "";
      if (provName === "Cordillera Administrative Region" || provName == "") {
        return {
          color: "#FFFFFF00",       // outline for CAR
          weight: 2,
          fillColor: "#FFFFFF00", // transparent fill
          fillOpacity: 0.0,
        };
      }
      return {
        color: provinceColors[provName] || defaultColor,
        weight: 2,
        fillColor: provinceColors[provName] || fallbackColor,
        fillOpacity: 0.2,
      };
    },
    onEachFeature: (feature: Feature<any>, layer: Layer) => {
      const provName = feature.properties?.name ?? "";

      if (provName) {
        layer.bindTooltip(provName);
      }

      layer.on("click", (e) => {
        e.originalEvent?.stopPropagation();

        if ("getBounds" in layer) {
          if (provName === "Cordillera Administrative Region") {
            map.fitBounds(geojsonLayer.getBounds(), { padding: [20, 20] });
            emit("region-selected");
            return;
          } else {
            map.fitBounds((layer as any).getBounds(), { padding: [20, 20] });
          }
        }


        // reset old highlight (skip CAR)
        if (selectedLayer.value && (selectedLayer.value as any).feature?.properties?.name !== "Cordillera Administrative Region") {
          const prevName = (selectedLayer.value as any).feature?.properties?.name ?? "";
          (selectedLayer.value as L.Path).setStyle({
            color: provinceColors[prevName] || defaultColor,
            fillColor: provinceColors[prevName] || fallbackColor,
            fillOpacity: 0.2,
          });
        }

        // highlight only provinces, not CAR
        if (provName !== "Cordillera Administrative Region") {
          const color = provinceColors[provName] || defaultColor;
          (layer as L.Path).setStyle({
            color,
            fillColor: color,
            fillOpacity: 0.5,
          });
          selectedLayer.value = layer;
        }

        emit("province-selected", {
          name: provName,
          info: feature.properties ?? {}
        });
      });
    },
  }).addTo(map);


  map.fitBounds(geojsonLayer.getBounds());

  // Province-level capital markers
  const bigIcon = L.icon({
    iconUrl: await axios.get("/map/red-marker", { responseType: "blob" }).then(res => URL.createObjectURL(res.data)),
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -35],
  });

  function addMarker(lat: number, lng: number, title: string, population: number, region: string) {
    L.marker([lat, lng], { icon: bigIcon })
      .addTo(map)
      .bindTooltip(`${title} (Capital)`)
      .on("click", () => {
        map.setView([lat, lng], 11);
        emit("marker-selected", { title, population, region });
      });
  }

  addMarker(17.5556, 120.7906, "Bangued, Abra", 250000, "Cordillera Administrative Region");
  addMarker(18.022963, 121.18412, "Kabugao Apayao", 125000, "Cordillera Administrative Region");
  addMarker(16.4484, 120.5905, "La Trinidad Benguet", 450000, "Cordillera Administrative Region");
  addMarker(16.8083, 121.1939, "Lagawe Ifugao", 200000, "Cordillera Administrative Region");
  addMarker(17.41, 121.4583, "Tabuk Kalinga", 220000, "Cordillera Administrative Region");
  addMarker(17.0913, 121.0106, "Bontoc Mountain Province", 160000, "Cordillera Administrative Region");
  addMarker(16.4130, 120.5914, "Baguio City", 345000, "Cordillera Administrative Region");
});

</script>

<style>
#map {
  min-height: 300px;
}
</style>

<template>
  <div id="map" class="w-full h-full font-poppins"></div>
</template>
