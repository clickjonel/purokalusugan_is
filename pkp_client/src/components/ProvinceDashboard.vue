<script setup lang="ts">
import { Card, CardContent } from "@/components/ui/card"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"


const props = defineProps<{
  provinceName: string
}>()

interface CardItem {
  title: string
  value: string | number
  subtitle: string
}

interface Site {
  code: string
  name: string
  status: "Active" | "Inactive"
}

// ✅ Demo data
const cards: CardItem[] = [
  { title: "Total No. of Barangay", value: 200, subtitle: "" },
  { title: "Total No. of Purok", value: 247, subtitle: "" },
  { title: "Total No. of Sites", value: 130, subtitle: "" },
  { title: "Coverage Rate", value: "30%", subtitle: "" },
  { title: "Field Operations", value: "100/130", subtitle: "" },
  { title: "Training Completed", value: "0/130", subtitle: "" },
  { title: "Monitoring Activities", value: 24, subtitle: "This month" },
  { title: "HRH Deployed", value: 287, subtitle: "Active personnel" },
  { title: "High Priority Areas", value: 12, subtitle: "Districts" },
  { title: "Population Enumeration", value: "500/1000", subtitle: "91.1%" },
]

const sites: Site[] = [
  { code: "B001", name: "Barangay Uno", status: "Active" },
  { code: "B002", name: "Barangay Dos", status: "Inactive" },
  { code: "B003", name: "Barangay Tres", status: "Active" },
]
</script>

<template>
  <div class="p-4 space-y-4">
    <!-- Title -->
    <h2 class="text-lg sm:text-xl font-bold text-center">CATCHMENT OVERVIEW</h2>
    <p class="text-xs sm:text-sm text-gray-500 text-center">
      Real-time monitoring of {{ props.provinceName }} with operational status, data collection, and administrative
      metrics.
    </p>

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <Card v-for="card in cards" :key="card.title" class="shadow-sm rounded-xl">
        <CardContent class="p-4">
          <div class="text-xs sm:text-sm font-medium text-gray-500">
            {{ card.title }}
          </div>
          <div class="text-base sm:text-lg font-bold">{{ card.value }}</div>
          <div class="text-xs text-gray-400">{{ card.subtitle }}</div>
        </CardContent>
      </Card>
    </div>

    <!-- Table -->
    <div class="mt-6">
      <h3 class="font-semibold mb-2 text-center sm:text-left">
        PuroKalusugan Sites
      </h3>
      <div class="border rounded-lg overflow-x-auto">
        <Table class="min-w-[500px] w-full">
          <TableHeader>
            <TableRow>
              <TableHead>Barangay Code</TableHead>
              <TableHead>Barangay</TableHead>
              <TableHead>Status</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="(row, i) in sites" :key="i">
              <TableCell>{{ row.code }}</TableCell>
              <TableCell>{{ row.name }}</TableCell>
              <TableCell>
                <Badge :variant="row.status === 'Active' ? 'default' : 'destructive'">
                  {{ row.status }}
                </Badge>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </div>
</template>
