<template>
  <div class="preview-panel">
    <h2 class="text-lg font-bold mb-2">{{ province.name }}</h2>
    <p v-for="(val, key) in province.info" :key="key">
      <strong>{{ key }}:</strong> {{ val }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  province: Object
})
</script>

<style scoped>
.preview-panel {
  width: 300px;
  padding: 16px;
  background-color: #f8f8f8;
  border-left: 1px solid #ccc;
  overflow-y: auto;
}
</style>
