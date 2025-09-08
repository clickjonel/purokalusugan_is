<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription } from "@/components/ui/card"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"

export interface CatchmentOverviewProps {
  title: string
  value: string | number
  subtitle: string
}

export interface Site {
  code: string
  name: string
  status: string
}

const props = defineProps<{
  provinceName: string
  cards: CatchmentOverviewProps[]
  sites: Site[]
}>()
</script>

<template>
  <div class="p-3 space-y-4">
    <!-- Compact Title -->
    <div class="text-center space-y-1">
      <h2 class="text-lg font-bold tracking-tight">CATCHMENT OVERVIEW</h2>
      <p class="text-xs text-muted-foreground">{{ props.provinceName }}</p>
    </div>

    <!-- Compact Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
      <Card v-for="card in props.cards" :key="card.title" class="rounded-lg shadow-sm">
        <CardHeader class="p-3 space-y-1">
          <CardTitle class="text-xs font-medium text-muted-foreground leading-tight">
            {{ card.title }}
          </CardTitle>
          <CardDescription class="text-base font-bold text-foreground">
            {{ card.value }}
          </CardDescription>
          <p v-if="card.subtitle" class="text-xs text-muted-foreground">{{ card.subtitle }}</p>
        </CardHeader>
      </Card>
    </div>

    <!-- Compact Table -->
    <div class="space-y-2">
      <h3 class="font-semibold text-sm text-center sm:text-left">Sites ({{ props.sites.length }})</h3>
      <div class="border rounded-lg overflow-hidden">
        <Table>
          <TableHeader>
            <TableRow class="h-10">
              <TableHead class="text-xs font-semibold py-2">Code</TableHead>
              <TableHead class="text-xs font-semibold py-2">Barangay</TableHead>
              <TableHead class="text-xs font-semibold py-2 text-center w-20">Status</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="(row, i) in props.sites" :key="i" class="h-12">
              <TableCell class="text-sm py-2 font-mono">{{ row.code }}</TableCell>
              <TableCell class="text-sm py-2">{{ row.name }}</TableCell>
              <TableCell class="text-center py-2">
                <Badge :variant="row.status === 'Active' ? 'default' : 'destructive'" class="text-xs px-2 py-0.5">
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