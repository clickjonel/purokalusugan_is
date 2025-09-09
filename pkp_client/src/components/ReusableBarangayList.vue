<script setup lang="ts">
import { Check, Search } from 'lucide-vue-next'
import axios from "@/axios/axios"
import { ref, onMounted } from 'vue'
import {
  Combobox,
  ComboboxAnchor,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxList,
} from '@/components/ui/combobox'

// ✅ Interface
interface Barangay {
  barangay_id: number
  barangay_name: string
}

// ✅ State
const barangays = ref<Barangay[]>([])
const selectedBarangay = ref<Barangay | undefined>()

// ✅ Fetch barangays
const fetchBarangays = () => {
  axios
    .get("/barangay/list")
    .then((response) => {
      barangays.value = response.data.data
    })
    .catch((error) => {
      console.error("Error fetching barangays:", error)
    })
}

onMounted(() => {
  fetchBarangays()
})
</script>

<template>
  <div class="w-full font-poppins">
    <Combobox v-model="selectedBarangay" by="barangay_id">
      <ComboboxAnchor>
        <div class="relative w-full items-center">
          <ComboboxInput class="pl-1 max-w-xl" :display-value="(val) => val?.barangay_name ?? ''"
            placeholder="Select barangay..." />
          <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
            <Search class="size-4 text-muted-foreground" />
          </span>
        </div>
      </ComboboxAnchor>

      <!-- ✅ Scrollable dropdown -->
      <ComboboxList class="max-h-60 overflow-y-auto">
        <ComboboxEmpty>No barangay found.</ComboboxEmpty>
        <ComboboxGroup>
          <ComboboxItem v-for="barangay in barangays" :key="barangay.barangay_id" :value="barangay">
            {{ barangay.barangay_name }}
            <ComboboxItemIndicator>
              <Check class="ml-auto size-4" />
            </ComboboxItemIndicator>
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxList>
    </Combobox>

    <!-- ✅ Show selected barangay_id -->
    <p v-if="selectedBarangay" class="mt-4 text-sm text-gray-600">
      Selected Barangay ID: <b>{{ selectedBarangay.barangay_id }}</b>
    </p>
  </div>
</template>
