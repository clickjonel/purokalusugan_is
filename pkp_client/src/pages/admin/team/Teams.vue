<script setup lang="ts">
import { onMounted, ref } from "vue"
import axios from '@/axios/axios';
import { Button } from "@/components/ui/button";
import { Input } from '@/components/ui/input'
import { Search,EllipsisVertical,UserRoundCog,UserLock } from "lucide-vue-next"
import { toast } from 'vue-sonner'
import { Dialog,DialogContent,DialogDescription,DialogFooter,DialogHeader,DialogTitle,DialogTrigger } from '@/components/ui/dialog'
import { Table,TableBody,TableCell,TableHead,TableHeader,TableRow } from '@/components/ui/table'
import { Select,SelectContent,SelectGroup,SelectItem,SelectLabel,SelectTrigger,SelectValue } from "@/components/ui/select"
import { Popover,PopoverContent,PopoverTrigger } from '@/components/ui/popover'


var searchKeyword = ref('')


onMounted(() => {

})


</script>


<template>
    <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
        <!-- header -->
        <div class="w-full flex justify-between items-center p-2 border">
            <div class="relative items-center">
                <Input v-model="searchKeyword" id="search" type="text" placeholder="Search Keyword" class="pl-8"/>
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>
            <!-- <Button @click="isCreateHrhModalOpen = true" variant="default" class="cursor-pointer" size="sm">Create Hrh</Button> -->
        </div>

        <!-- table -->
        <div class="w-full h-full flex flex-col jusitfy-start items-start border">
            <div class="w-full h-[640px] overflow-y-scroll">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Team Name</TableHead>
                            <TableHead>Team Scope</TableHead>
                            <TableHead>Team Members</TableHead>
                            <TableHead class="text-end">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <!-- <TableRow v-for="hrh in hrhList">
                            <TableCell>{{ hrh.first_name }}</TableCell>
                            <TableCell>{{ hrh.middle_name }}</TableCell>
                            <TableCell>{{ hrh.last_name }}</TableCell>
                            <TableCell>{{ hrh.nickname }}</TableCell>
                            <TableCell class="w-full flex justify-end items-center gap-2">
                                <Popover>
                                    <PopoverTrigger asChild>
                                        <Button variant="ghost" size="icon" class="cursor-pointer">
                                            <EllipsisVertical class="h-4 w-4" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-45 p-2">
                                        <div class="flex flex-col">
                                            <Button @click="openAssignHrhModal(hrh)" variant="ghost" size="sm" class="justify-start text-xs">
                                                <UserRoundCog/> 
                                                Assign Team
                                            </Button>
                                            <Button @click="openAssignHrhModal(hrh)" variant="ghost" size="sm" class="justify-start text-xs">
                                                <UserRoundCog/> 
                                                Update Details
                                            </Button>
                                             <Button @click="openAssignHrhModal(hrh)" variant="ghost" size="sm" class="justify-start text-xs">
                                                <UserRoundCog/> 
                                                Update Credentials
                                            </Button>
                                            <Button @click="confirmDeactivateHrh(hrh.hrh_user_id)" variant="ghost" size="sm" class="justify-start text-xs text-red-600">
                                                <UserLock /> 
                                                Deactivate
                                            </Button>
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </TableCell>
                        </TableRow> -->
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- footer -->
        <div class="w-full h-[50px] flex justify-center items-center border p-2">
            <span>Pagination Here</span>
        </div>
    </div>

    

</template>