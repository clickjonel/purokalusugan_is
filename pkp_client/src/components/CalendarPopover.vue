<script setup lang="ts">
import { ref, watch } from 'vue';
import { Calendar } from "@/components/ui/calendar"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Button } from "@/components/ui/button";
import type { DateValue } from "@internationalized/date"
import { format } from 'date-fns';

const model = defineModel<string|undefined>({
  default: undefined
})

const date = defineModel<DateValue|undefined>('date')

// Sync the date model with the string model
watch(date, (newDate) => {
  if (newDate) {
    try {
      const jsDate = newDate.toDate('UTC');
      model.value = format(jsDate, 'yyyy-MM-dd');
    } catch (error) {
      console.warn('Error formatting date:', error);
      model.value = undefined;
    }
  } else {
    model.value = undefined;
  }
}, { immediate: true });
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button variant="outline" class="w-[240px] justify-start text-left font-normal">
        <CalendarIcon class="mr-2 h-4 w-4" />
        {{ date ? format(date.toDate('UTC'), 'PPP') : 'Pick a date' }}
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-auto p-0" align="start">
      <Calendar v-model="date" initial-focus />
    </PopoverContent>
  </Popover>
</template>