<script setup lang="ts">
import { ref } from "vue"

import BarangaySelector from '@/components/ui/select/barangay_selection/BarangaySelector.vue'


const selectedBarangayId = ref<number | undefined>()

</script>

<template>
  <div class="p-6 space-y-4">
    <BarangaySelector v-model="selectedBarangayId" />
    <!-- Show result -->
    <div v-if="selectedBarangayId" class="text-sm">
      Selected Barangay ID: <b>{{ selectedBarangayId }}</b>
    </div>
  </div>
</template>
