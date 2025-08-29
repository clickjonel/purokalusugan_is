<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { ArrowRightFromLine,ArrowLeftFromLine  } from "lucide-vue-next"
import { cn } from "@/lib/utils"
import { Button } from '@/components/ui/button'
import { useSidebar } from "./utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const { toggleSidebar,open } = useSidebar()
</script>

<template>
  <Button
    data-sidebar="trigger"
    data-slot="sidebar-trigger"
    variant="outline"
    size="icon"
    :class="cn('size-8 cursor-pointer', props.class)"
    @click="toggleSidebar"
  >
    <ArrowRightFromLine v-if="!open" class="size-5"/>
    <ArrowLeftFromLine v-if="open" class="size-5"/>
    <span class="sr-only">Toggle Sidebar</span>
  </Button>
</template>
