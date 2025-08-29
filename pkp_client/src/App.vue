<script setup lang="ts">
  import { SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
  import AppSidebar from "@/components/AppSidebar.vue";
</script>

<template>
  <div class="flex w-full h-screen justify-start items-start gap-2 bg-gray-200 font-poppins">
    <SidebarProvider>
      <AppSidebar />
      <main class="w-full h-screen flex flex-col justify-between items-start gap-2">

        <div class="w-full flex justify-between items-center p-2 border-b border-black">
          <SidebarTrigger />
          <span>Page Title</span>
          <span>Profile</span>
        </div>

        <slot />

        <div class="w-full flex flex-col justify-center items-center p-2 border-t border-black">
          <span class="text-xs italic">Copyright © Department of Health</span>
          <span class="text-xs italic"><strong>Center for Health Development</strong> - Cordillera Administrative Region</span>
          <span class="text-xs italic">Developed By: ICTMU</span>
        </div>

      </main>
    </SidebarProvider>
  </div>
</template>
