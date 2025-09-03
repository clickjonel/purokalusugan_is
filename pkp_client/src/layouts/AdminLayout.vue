<script setup lang="ts">
import { computed } from "vue";
import { SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
import { Bell } from "lucide-vue-next"
import { Button } from "@/components/ui/button"
import AppSidebar from "@/components/AppSidebar.vue";
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { useRoute } from "vue-router";

const route = useRoute();
const breadcrumbs = computed(() => Array.isArray(route.meta?.breadcrumbs) ? route.meta.breadcrumbs : []);

</script>

<template>
  <div class="flex w-full h-screen justify-start items-start gap-2 bg-[#EFEFEF] font-poppins">
    <SidebarProvider>
      <AppSidebar />
      <main class="w-full h-screen flex flex-col justify-between items-start gap-4 p-2">

        <div class="w-full flex justify-between items-center p-2 bg-sidebar rounded-md shadow-md">
          <SidebarTrigger />

          <Breadcrumb>
            <BreadcrumbList>
              <BreadcrumbItem v-for="(breadcrumb, index) in breadcrumbs" :key="index">
                <template v-if="index < breadcrumbs.length - 1">
                  <BreadcrumbLink :href="breadcrumb.link">
                    {{ breadcrumb.name }}
                  </BreadcrumbLink>
                  <BreadcrumbSeparator />
                </template>

                <template v-else>
                  <BreadcrumbPage>
                    {{ breadcrumb.name }}
                  </BreadcrumbPage>
                </template>
              </BreadcrumbItem>
            </BreadcrumbList>
          </Breadcrumb>

          <div class="flex justify-center items-center gap-2">
            <Button variant="outline" size="icon">
              <Bell class="w-4 h-4" />
            </Button>
            <Button variant="outline">
              Username
            </Button>
          </div>

        </div>

        <div class="w-full h-full flex flex-col justify-start items-start bg-sidebar rounded-md shadow-md">

          <router-view></router-view>

        </div>

        <div class="w-full flex flex-col justify-center items-center p-2 bg-sidebar rounded-md shadow-md">
          <span class="text-xs italic">Copyright © Department of Health</span>
          <span class="text-xs italic"><strong>Center for Health Development</strong> - Cordillera Administrative
            Region</span>
          <span class="text-xs italic">Developed By: ICTMU</span>
        </div>

      </main>
    </SidebarProvider>
  </div>
</template>
