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
const defaultColor = "#50C878"
const fallbackColor = "#50C878"

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
      name: "Bangued (Capital)",
      lat: 17.5155,
      lng: 120.6181,
      barangays: [
        { name: "Maoay", lat: 17.5155, lng: 120.6181 },
        { name: "Macray", lat: 17.5201, lng: 120.6433 },
        { name: "Sagap", lat: 17.5167, lng: 120.62 }
      ]
    },
    {
      name: "Bucloc",
      lat: 17.4725,
      lng: 120.7575,
      barangays: [
        { name: "Ducligan", lat: 17.4725, lng: 120.7575 },
        { name: "Labaan", lat: 17.47, lng: 120.75 },
        { name: "Lingey", lat: 17.465, lng: 120.745 }
      ]
    },
    {
      name: "Daguioman",
      lat: 17.541,
      lng: 120.841,
      barangays: [
        { name: "Ableg", lat: 17.541, lng: 120.841 },
        { name: "Cabaruyan", lat: 17.545, lng: 120.845 },
        { name: "Pikek", lat: 17.548, lng: 120.848 }
      ]
    },
    {
      name: "Lacub",
      lat: 17.641,
      lng: 121.003,
      barangays: [
        { name: "Buneg", lat: 17.641, lng: 121.003 }
      ]
    },
    {
      name: "Lagangilang",
      lat: 17.61,
      lng: 120.71,
      barangays: [
        { name: "Cayapa", lat: 17.61, lng: 120.71 },
        { name: "Paganao", lat: 17.615, lng: 120.715 },
        { name: "San Isidro", lat: 17.62, lng: 120.72 }
      ]
    },
    {
      name: "Lagayan",
      lat: 17.7,
      lng: 120.8,
      barangays: [
        { name: "Ba-i", lat: 17.7, lng: 120.8 },
        { name: "Collago", lat: 17.705, lng: 120.805 },
        { name: "Pang-ot", lat: 17.71, lng: 120.81 }
      ]
    },
    {
      name: "Luba",
      lat: 17.5262,
      lng: 120.6812,
      barangays: [
        { name: "Ampalioc", lat: 17.5262, lng: 120.6812 },
        { name: "Barit", lat: 17.528, lng: 120.683 },
        { name: "Gayaman", lat: 17.53, lng: 120.685 }
      ]
    },
    {
      name: "Malibcong",
      lat: 17.6249,
      lng: 121.0433,
      barangays: [
        { name: "Mataragan", lat: 17.6249, lng: 121.0433 }
      ]
    },
    {
      name: "Manabo",
      lat: 17.4323,
      lng: 120.7056,
      barangays: [
        { name: "Ayyeng", lat: 17.4323, lng: 120.7056 },
        { name: "San Ramon East", lat: 17.43, lng: 120.71 },
        { name: "Catacdegan Nuevo", lat: 17.428, lng: 120.712 }
      ]
    },
    {
      name: "Peñarrubia",
      lat: 17.5309,
      lng: 120.6462,
      barangays: [
        { name: "Malamsit", lat: 17.5309, lng: 120.6462 }
      ]
    },
    {
      name: "San Isidro",
      lat: 17.4961,
      lng: 120.6051,
      barangays: [
        { name: "Dalimag", lat: 17.4961, lng: 120.6051 },
        { name: "Manayday", lat: 17.49, lng: 120.61 },
        { name: "San Marcial", lat: 17.485, lng: 120.615 }
      ]
    },
    {
      name: "San Juan",
      lat: 17.6766,
      lng: 120.7378,
      barangays: [
        { name: "Ba-ug", lat: 17.6766, lng: 120.7378 },
        { name: "Quidaoen", lat: 17.682, lng: 120.745 },
        { name: "Supi-il", lat: 17.683, lng: 120.748 }
      ]
    },
    {
      name: "San Quintin",
      lat: 17.5,
      lng: 120.55,
      barangays: [
        { name: "Palang", lat: 17.5, lng: 120.55 },
        { name: "Pantoc", lat: 17.5192, lng: 120.5357 }
      ]
    },
    {
      name: "Tayum",
      lat: 17.5935,
      lng: 120.7018,
      barangays: [
        { name: "Basbasa", lat: 17.5935, lng: 120.7018 },
        { name: "Gaddani", lat: 17.6213, lng: 120.6777 },
        { name: "Patucannay", lat: 17.6061, lng: 120.6471 }
      ]
    },
    {
      name: "Tineg",
      lat: 17.7507,
      lng: 120.8336,
      barangays: [
        { name: "Alaoa", lat: 17.7507, lng: 120.8336 },
        { name: "Apao", lat: 17.755, lng: 120.83 },
        { name: "Lapat-Balantay", lat: 17.76, lng: 120.835 }
      ]
    },
    {
      name: "Tubo",
      lat: 17.2194,
      lng: 120.7703,
      barangays: [
        { name: "Alangtin", lat: 17.2194, lng: 120.7703 },
        { name: "Amtuagan", lat: 17.3058, lng: 120.7308 },
        { name: "Tabacda", lat: 17.225, lng: 120.765 }
      ]
    }
  ],
  Benguet: [
    {
      name: "Atok",
      lat: 16.638,
      lng: 120.719,
      barangays: [
        { name: "Naguey", lat: 16.638, lng: 120.719 },
        { name: "Pasdong", lat: 16.6409, lng: 120.7158 },
        { name: "Poblacion", lat: 16.5636, lng: 120.6689 }
      ]
    },
    {
      name: "Bakun",
      lat: 16.8833,
      lng: 120.6811,
      barangays: [
        { name: "Bagu", lat: 16.8833, lng: 120.6811 },
        { name: "Dalipey", lat: 16.8244, lng: 120.7219 },
        { name: "Kayapa", lat: 16.7822, lng: 120.6127 }
      ]
    },
    {
      name: "Bokod",
      lat: 16.4036,
      lng: 120.7931,
      barangays: [
        { name: "Bobok-Bisal", lat: 16.4036, lng: 120.7931 },
        { name: "Nawal", lat: 16.5502, lng: 120.766 },
        { name: "Pito", lat: 16.3691, lng: 120.8349 }
      ]
    },
    {
      name: "Buguias",
      lat: 16.695,
      lng: 120.8532,
      barangays: [
        { name: "Amlimay", lat: 16.695, lng: 120.8532 },
        { name: "Baculongan Sur", lat: 16.7, lng: 120.86 },
        { name: "Sebang", lat: 16.71, lng: 120.865 }
      ]
    },
    {
      name: "Kabayan",
      lat: 16.7,
      lng: 120.87,
      barangays: [
        { name: "Ballay", lat: 16.7, lng: 120.87 },
        { name: "Bashoy", lat: 16.705, lng: 120.875 },
        { name: "Pacso", lat: 16.71, lng: 120.88 }
      ]
    },
    {
      name: "Kapangan",
      lat: 16.633,
      lng: 120.637,
      barangays: [
        { name: "Gaswiling", lat: 16.633, lng: 120.637 },
        { name: "Sagubo", lat: 16.64, lng: 120.63 },
        { name: "Taba-ao", lat: 16.645, lng: 120.625 }
      ]
    },
    {
      name: "Sablan",
      lat: 16.5,
      lng: 120.58,
      barangays: [
        { name: "Bagong", lat: 16.5, lng: 120.58 },
        { name: "Kamog", lat: 16.505, lng: 120.585 },
        { name: "Pappa", lat: 16.51, lng: 120.59 }
      ]
    },
    {
      name: "Tuba",
      lat: 16.3,
      lng: 120.6,
      barangays: [
        { name: "Ansagan", lat: 16.3, lng: 120.6 },
        { name: "Camp 3", lat: 16.32, lng: 120.61 },
        { name: "Tadiangan", lat: 16.34, lng: 120.62 }
      ]
    },
    {
      name: "Tublay",
      lat: 16.583,
      lng: 120.65,
      barangays: [
        { name: "Ambassador", lat: 16.583, lng: 120.65 },
        { name: "Daclan", lat: 16.59, lng: 120.655 },
        { name: "Caponga", lat: 16.595, lng: 120.66 }
      ]
    }
  ],
  Ifugao: [
    {
      name: "Hungduan",
      lat: 16.8,
      lng: 121.1,
      barangays: [
        { name: "Poblacion", lat: 16.8, lng: 121.1 },
        { name: "Bokiawan", lat: 16.79, lng: 121.11 },
        { name: "Hapao", lat: 16.81, lng: 121.12 }
      ]
    },
    {
      name: "Kiangan",
      lat: 16.74,
      lng: 121.07,
      barangays: [
        { name: "Hucab", lat: 16.74, lng: 121.07 },
        { name: "Mungayang", lat: 16.73, lng: 121.06 },
        { name: "Nagacadan", lat: 16.72, lng: 121.05 }
      ]
    },
    {
      name: "Lagawe",
      lat: 16.8,
      lng: 121,
      barangays: [
        { name: "Buyabuyan", lat: 16.8, lng: 121 },
        { name: "Jucbong", lat: 16.79, lng: 121.01 },
        { name: "Montabiong", lat: 16.78, lng: 121.02 }
      ]
    },
    {
      name: "Lamut",
      lat: 16.76,
      lng: 121.12,
      barangays: [
        { name: "Nayon", lat: 16.76, lng: 121.12 },
        { name: "Holowon", lat: 16.75, lng: 121.13 },
        { name: "Sanafe", lat: 16.74, lng: 121.14 }
      ]
    },
    {
      name: "Mayoyao",
      lat: 16.9,
      lng: 121.25,
      barangays: [
        { name: "Balangbang", lat: 16.9, lng: 121.25 },
        { name: "Chaya", lat: 16.91, lng: 121.26 },
        { name: "Tulaed", lat: 16.92, lng: 121.27 }
      ]
    },
    {
      name: "Aguinaldo",
      lat: 16.96,
      lng: 121.42,
      barangays: [
        { name: "Chalalo", lat: 16.96, lng: 121.42 },
        { name: "Itab", lat: 16.955, lng: 121.415 },
        { name: "Ubao", lat: 16.9522, lng: 121.4131 }
      ]
    },
    {
      name: "Tinoc",
      lat: 16.7722,
      lng: 120.9255,
      barangays: [
        { name: "Ahin", lat: 16.7722, lng: 120.9255 },
        { name: "Binablayan", lat: 16.7279, lng: 120.9773 },
        { name: "Wangwang", lat: 16.7705, lng: 120.9815 }
      ]
    },
    {
      name: "Asipulo",
      lat: 16.6189,
      lng: 121.0217,
      barangays: [
        { name: "Camandag", lat: 16.6189, lng: 121.0217 },
        { name: "Cawayan", lat: 16.6486, lng: 121.0932 },
        { name: "Namal", lat: 16.6786, lng: 121.0168 }
      ]
    }
  ],
  Kalinga: [
    {
      name: "Lubuagan",
      lat: 17.3794,
      lng: 121.2875,
      barangays: [
        { name: "Mabongtot", lat: 17.3794, lng: 121.2875 },
        { name: "Tanglag", lat: 17.3844, lng: 121.2806 },
        { name: "Antonio Canao", lat: 17.39, lng: 121.29 }
      ]
    },
    {
      name: "Pasil",
      lat: 17.41,
      lng: 121.18,
      barangays: [
        { name: "Balinciagao Norte", lat: 17.41, lng: 121.18 },
        { name: "Colayo", lat: 17.42, lng: 121.19 },
        { name: "Bagtayan", lat: 17.425, lng: 121.195 }
      ]
    },
    {
      name: "Pinukpuk",
      lat: 17.565,
      lng: 121.34,
      barangays: [
        { name: "Aciga", lat: 17.565, lng: 121.34 },
        { name: "Malagnat", lat: 17.57, lng: 121.345 },
        { name: "Pakawit", lat: 17.575, lng: 121.35 }
      ]
    },
    {
      name: "Rizal",
      lat: 17.46,
      lng: 121.47,
      barangays: [
        { name: "Kinama", lat: 17.46, lng: 121.47 },
        { name: "San Francisco", lat: 17.465, lng: 121.475 },
        { name: "San Pedro", lat: 17.47, lng: 121.48 }
      ]
    },
    {
      name: "City of Tabuk",
      lat: 17.4481,
      lng: 121.4571,
      barangays: [
        { name: "Agbannawag", lat: 17.4481, lng: 121.4571 },
        { name: "Balawag", lat: 17.446, lng: 121.46 },
        { name: "Dilag", lat: 17.45, lng: 121.465 },
        { name: "Laya East", lat: 17.455, lng: 121.47 },
        { name: "Laya West", lat: 17.46, lng: 121.475 },
        { name: "Magnao", lat: 17.465, lng: 121.48 },
        { name: "Nambucayan", lat: 17.47, lng: 121.485 }
      ]
    },
    {
      name: "Tinglayan",
      lat: 17.3083,
      lng: 121.185,
      barangays: [
        { name: "Basao", lat: 17.3083, lng: 121.185 },
        { name: "Loccong", lat: 17.315, lng: 121.19 },
        { name: "Sumadel 1", lat: 17.32, lng: 121.195 }
      ]
    }
  ],
  "Mountain Province": [
    {
      name: "Barlig",
      lat: 17.0333,
      lng: 121.0833,
      barangays: [
        { name: "Kaleo", lat: 17.0333, lng: 121.0833 },
        { name: "Lias Kanluran", lat: 17.05, lng: 121.1 },
        { name: "Gawana", lat: 17.04, lng: 121.09 }
      ]
    },
    {
      name: "Bauko",
      lat: 16.9333,
      lng: 120.8333,
      barangays: [
        { name: "Mabaay", lat: 16.9333, lng: 120.8333 },
        { name: "Monamon Norte", lat: 16.95, lng: 120.85 },
        { name: "Monamon Sur", lat: 16.94, lng: 120.84 }
      ]
    },
    {
      name: "Besao",
      lat: 17.0833,
      lng: 120.7833,
      barangays: [
        { name: "Gueday", lat: 17.0833, lng: 120.7833 },
        { name: "Laylaya", lat: 17.09, lng: 120.79 },
        { name: "Tamboan", lat: 17.1, lng: 120.8 }
      ]
    },
    {
      name: "Bontoc",
      lat: 17.0833,
      lng: 120.9833,
      barangays: [
        { name: "Caneo", lat: 17.0833, lng: 120.9833 },
        { name: "Dalican", lat: 17.07, lng: 120.98 },
        { name: "Mainit", lat: 17.06, lng: 120.97 }
      ]
    },
    {
      name: "Natonin",
      lat: 17.1333,
      lng: 121.3333,
      barangays: [
        { name: "Banawal", lat: 17.1333, lng: 121.3333 },
        { name: "Saliok", lat: 17.12, lng: 121.32 },
        { name: "Tonglayan", lat: 17.11, lng: 121.31 }
      ]
    },
    {
      name: "Paracelis",
      lat: 17.25,
      lng: 121.4,
      barangays: [
        { name: "Bantay", lat: 17.25, lng: 121.4 },
        { name: "Buringal", lat: 17.24, lng: 121.39 },
        { name: "Poblacion", lat: 17.23, lng: 121.38 }
      ]
    },
    {
      name: "Sabangan",
      lat: 17,
      lng: 120.85,
      barangays: [
        { name: "Busa", lat: 17, lng: 120.85 },
        { name: "Losad", lat: 17.01, lng: 120.86 },
        { name: "Napua", lat: 17.02, lng: 120.87 }
      ]
    },
    {
      name: "Sadanga",
      lat: 17.15,
      lng: 120.95,
      barangays: [
        { name: "Anabel", lat: 17.15, lng: 120.95 },
        { name: "Betwagan", lat: 17.16, lng: 120.96 },
        { name: "Saclit", lat: 17.17, lng: 120.97 }
      ]
    },
    {
      name: "Sagada",
      lat: 17.0833,
      lng: 120.9,
      barangays: [
        { name: "Fidelisan", lat: 17.0833, lng: 120.9 },
        { name: "Pide", lat: 17.09, lng: 120.91 },
        { name: "Tetepan Norte", lat: 17.1, lng: 120.92 }
      ]
    },
    {
      name: "Tadian",
      lat: 16.95,
      lng: 120.85,
      barangays: [
        { name: "Duagan", lat: 16.95, lng: 120.85 },
        { name: "Dacudac", lat: 16.96, lng: 120.86 },
        { name: "Mabalite", lat: 16.97, lng: 120.87 }
      ]
    }
  ],
  Apayao: [
    {
      name: "Conner",
      lat: 17.6333,
      lng: 121.2833,
      barangays: [
        { name: "Malama", lat: 17.6333, lng: 121.2833 },
        { name: "Paddaoan", lat: 17.65, lng: 121.3 },
        { name: "Sacpil", lat: 17.64, lng: 121.29 }
      ]
    },
    {
      name: "Flora",
      lat: 18.25,
      lng: 121.4,
      barangays: [
        { name: "Allig", lat: 18.25, lng: 121.4 },
        { name: "Mallig", lat: 18.26, lng: 121.41 },
        { name: "Poblacion West", lat: 18.27, lng: 121.42 }
      ]
    },
    {
      name: "Kabugao",
      lat: 17.9474,
      lng: 121.1092,
      barangays: [
        { name: "Dagara", lat: 17.9474, lng: 121.1092 },
        { name: "Madatag", lat: 18.0488, lng: 121.096 },
        { name: "Maragat", lat: 17.8941, lng: 121.0526 }
      ]
    },
    {
      name: "Santa Marcela",
      lat: 18.2943,
      lng: 121.4261,
      barangays: [
        { name: "Barocboc", lat: 18.2943, lng: 121.4261 },
        { name: "Consuelo", lat: 18.216, lng: 121.4717 },
        { name: "San Juan", lat: 18.2519, lng: 121.477 }
      ]
    }
  ],
  "Baguio City": [
    {
      name: "Baguio City",
      lat: 16.4023,
      lng: 120.596,
      barangays: [
        { name: "Asin Road", lat: 16.4046, lng: 120.5636 },
        { name: "Bakakeng Central", lat: 16.3935, lng: 120.5862 },
        { name: "Bakakeng North", lat: 16.398, lng: 120.584 },
        { name: "Happy Hollow", lat: 16.4025, lng: 120.615 },
        { name: "Balsigan", lat: 16.3989, lng: 120.6035 },
        { name: "Bayan Park East", lat: 16.419, lng: 120.6155 },
        { name: "Camp Allen", lat: 16.412, lng: 120.594 },
        { name: "Camp 7", lat: 16.391, lng: 120.61 },
        { name: "Country Club Village", lat: 16.402, lng: 120.62 },
        { name: "Dizon Subdivision", lat: 16.417, lng: 120.61 },
        { name: "Quirino Hill East", lat: 16.43, lng: 120.6 },
        { name: "Fairview Village", lat: 16.426, lng: 120.61 },
        { name: "Gibraltar", lat: 16.418, lng: 120.625 },
        { name: "Guisad Sorong", lat: 16.42, lng: 120.61 },
        { name: "Irisan", lat: 16.4167, lng: 120.57 },
        { name: "Kias", lat: 16.39, lng: 120.59 },
        { name: "Loakan Proper", lat: 16.395, lng: 120.62 },
        { name: "Lucnab", lat: 16.4, lng: 120.63 },
        { name: "Quirino Hill Middle", lat: 16.428, lng: 120.598 },
        { name: "Military Cut-off", lat: 16.408, lng: 120.6 },
        { name: "Modern Site East", lat: 16.415, lng: 120.605 },
        { name: "Aurora Hill  North Central", lat: 16.42, lng: 120.61 },
        { name: "Outlook Drive", lat: 16.41, lng: 120.625 },
        { name: "Pacdal", lat: 16.4125, lng: 120.62 },
        { name: "Pinsao Proper", lat: 16.425, lng: 120.59 },
        { name: "Poliwes", lat: 16.41, lng: 120.58 },
        { name: "MRR-Queen Of Peace", lat: 16.4155, lng: 120.595 },
        { name: "San Luis Village", lat: 16.42, lng: 120.585 },
        { name: "San Roque Village", lat: 16.418, lng: 120.58 },
        { name: "San Vicente", lat: 16.416, lng: 120.575 },
        { name: "Saint Joseph Village", lat: 16.419, lng: 120.618 },
        { name: "Victoria Village", lat: 16.417, lng: 120.61 },
        { name: "Dominican Hill-Mirador", lat: 16.408, lng: 120.6 },
        { name: "Gabriela Silang", lat: 16.41, lng: 120.605 },
        { name: "Liwanag-Loakan", lat: 16.396, lng: 120.615 },
        { name: "Padre Burgos", lat: 16.41, lng: 120.61 },
        { name: "SLU-SVP Housing Village", lat: 16.413, lng: 120.612 }
      ]
    }
  ]
};



// 🔹 Track municipality + barangay markers
const municipalityMarkers: L.Marker[] = []

onMounted(async () => {
  const map: Map = L.map("map").setView([16.6, 121.2], 7)

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map)

  // Fetch GeoJSON
  const res = await fetch("http://192.168.225.100:8000/api/geojson")
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

const provinceComplementaryColors: Record<string, string> = {
  "Abra": "#99FF99",          // Complement of #FF9999 (light green)
  "Apayao": "#0000FF",        // Complement of #FFD700 (blue)
  "Benguet": "#4276FF",       // Complement of #FF8C42 (light blue)
  "Ifugao": "#69FFB4",        // Complement of #FF69B4 (mint green)
  "Kalinga": "#D3AA55",       // Complement of #BA55D3 (golden yellow)
  "Mountain Province": "#00AFFF", // Complement of #FF4500 (light blue)
  "Baguio City": "#70DA66"    // Complement of #DA70D6 (light green)
};

// Create a function to generate colored markers
function showMunicipalities(province: string, map: L.Map) {
  // Remove old markers
  municipalityMarkers.forEach(m => m.remove());
  municipalityMarkers.length = 0;

  const municipalities = municipalityData[province] || [];
  municipalities.forEach(muni => {
    // Get the complementary color for this province
    const color = provinceComplementaryColors[province] || "#808080";

    // Municipality marker
    const muniMarker = L.marker([muni.lat, muni.lng], {
      icon: createColoredIcon(color)
    })
      .addTo(map)
      .bindTooltip(`${muni.name} (Municipality)`)
      .on("click", () => {
        map.setView([muni.lat, muni.lng], 12);
        emit("marker-selected", { title: muni.name, population: 0, region: province });
      });
    municipalityMarkers.push(muniMarker);

    // Barangay markers with same complementary color
    muni.barangays.forEach(brgy => {
      const barangayMarker = L.marker([brgy.lat, brgy.lng], {
        icon: createColoredIcon(color)
      })
        .addTo(map)
        .bindTooltip(`${brgy.name}`)
        .on("click", () => {
          map.setView([brgy.lat, brgy.lng], 13);
          emit("marker-selected", {
            title: brgy.name,
            region: province,
            level: "barangay",
            status: "active"
          });
        });
      municipalityMarkers.push(barangayMarker);
    });
  });
}

// Create colored icon using DivIcon
function createColoredIcon(color: string): L.DivIcon {
  return L.divIcon({
    html: `
      <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <circle cx="10" cy="10" r="8" fill="${color}" stroke="white" stroke-width="1"/>
      </svg>
    `,
    iconSize: [20, 20],
    iconAnchor: [10, 10],
    className: 'barangay-marker'
  });
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
