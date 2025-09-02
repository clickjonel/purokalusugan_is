<script setup lang="ts">
import { Calendar, Home, Inbox, Search, Settings,Users  } from "lucide-vue-next"
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarFooter,
  SidebarHeader
} from "@/components/ui/sidebar"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Button } from "@/components/ui/button"
import { SquareActivity } from 'lucide-vue-next';
import { Avatar, AvatarImage } from '@/components/ui/avatar'

// Menu items.
const items = [
  {
    title: "Dashboard",
    url: "/admin/dashboard",
    icon: Home,
  },
  {
    title: "HRH",
    url: "/admin/hrh",
    icon: Users,
  },
  {
    title: "Calendar",
    url: "#",
    icon: Calendar,
  },
  {
    title: "Search",
    url: "#",
    icon: Search,
  },
  {
    title: "Settings",
    url: "#",
    icon: Settings,
  },
];
</script>

<template>
  <Sidebar>
    <SidebarHeader>
        <div class="w-full flex justify-start items-center">
            <Button variant="outline" size="default" class="w-full flex justify-start items-center gap-2">
              <SquareActivity class="size-6"/>
              <span>PK PULSE</span>
            </Button>
        </div>
    </SidebarHeader>

    <SidebarContent>
      <SidebarGroup>
        <SidebarGroupLabel class="text-base">Navigation</SidebarGroupLabel>
        <SidebarGroupContent>
          <SidebarMenu>
              <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton asChild>
                    <a :href="item.url">
                      <component :is="item.icon" />
                      <span>{{item.title}}</span>
                    </a>
                </SidebarMenuButton>
              </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
      </SidebarGroup>
    </SidebarContent>

    <SidebarFooter>
        <div class="w-full flex justify-start items-center">
             <DropdownMenu class="w-full">
                <DropdownMenuTrigger class="w-full">
                    <Button variant="outline" class="w-full flex justify-start items-center gap-2">
                        <Avatar class="size-6">
                          <AvatarImage src="https://github.com/unovue.png" alt="@unovue"/>
                        </Avatar>
                        <span>Laksa Goreng</span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-[220px] font-poppins">
                  <DropdownMenuLabel>Laksa Goreng</DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem>Team</DropdownMenuItem>
                  <DropdownMenuItem>Program</DropdownMenuItem>
                  <DropdownMenuItem>Position</DropdownMenuItem>
                  <DropdownMenuItem>Province</DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
        </div>
    </SidebarFooter>
    
  </Sidebar>
</template>