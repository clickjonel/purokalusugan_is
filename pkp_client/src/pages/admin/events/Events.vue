<script setup lang="ts">
import { onMounted, ref } from "vue"
import axios from '@/axios/axios';
import { Button } from "@/components/ui/button";
import { Input } from '@/components/ui/input'
import { Search,EllipsisVertical } from "lucide-vue-next"
import { toast } from 'vue-sonner'
import { Table,TableBody,TableCell,TableHead,TableHeader,TableRow } from '@/components/ui/table'
import { Popover,PopoverContent,PopoverTrigger } from '@/components/ui/popover'
import { useRouter } from  'vue-router';
import { Badge } from '@/components/ui/badge'

const router = useRouter()
const events = ref<Event[]>([])

const pagination = ref({
    page:0,
    perPage:10,
    searchKeyword:'',
    total:0
})


onMounted(() => {
    fetchEvents()
})

function fetchEvents() {
    axios.get('/event/list')
        .then((response) => {
            events.value = response.data.data
            pagination.value.total = response.data.total
            console.log(events.value)
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
}

interface Event {
    event_name:string,
    event_type:number,
    event_budget:number,
    event_actual_budget:number,
    event_type_name:string,
    event_venue:string,
    event_fund_source:string,
    event_id:number
}


</script>


<template>
    <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
        <!-- header -->
        <div class="w-full flex justify-between items-center p-2">
            <div class="relative items-center">
                <Input v-model="pagination.searchKeyword" id="search" type="text" placeholder="Search Keyword" class="pl-8"/>
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>
            <Button @click="router.push({path:'/event/create'})" variant="default" class="cursor-pointer" size="sm">Create Event</Button>
        </div>

        <!-- table -->
        <div class="w-full h-full flex flex-col jusitfy-start items-start border">
            <div class="w-full h-[640px] overflow-y-scroll">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Event Name</TableHead>
                            <TableHead>Event Venue</TableHead>
                            <TableHead>Event Type</TableHead>
                            <TableHead>Event Budget / Actual Budget</TableHead>
                            <TableHead>Event Fund Source</TableHead>
                            <TableHead>Other Data</TableHead>
                            <TableHead class="text-end">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="event in events" class="font-light">
                            <TableCell>{{ event.event_name }}</TableCell>
                            <TableCell>{{ event.event_venue }}</TableCell>
                            <TableCell>{{ event.event_type_name }}</TableCell>
                            <TableCell>{{ event.event_budget }} / {{ event.event_actual_budget }}</TableCell>
                            <TableCell>{{ event.event_fund_source }}</TableCell>
                            <TableCell class="flex gap-1">
                                <Badge class="bg-sky-900 font-light">Partners</Badge>
                                <Badge class="bg-sky-900 font-light">Proponents</Badge>
                                <Badge class="bg-sky-900 font-light">Barangays</Badge>
                                <Badge class="bg-sky-900 font-light">Programs</Badge>
                            </TableCell>
                            <TableCell class="text-end">
                                <Popover>
                                    <PopoverTrigger asChild>
                                        <Button variant="ghost" size="icon" class="cursor-pointer">
                                            <EllipsisVertical class="h-4 w-4" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-45 p-2">
                                        <div class="flex flex-col">
                                            <Button variant="ghost" size="sm" class="justify-start text-xs">Edit</Button>
                                            <Button variant="ghost" size="sm" class="justify-start text-xs">Manage Barangays</Button>
                                            <Button variant="ghost" size="sm" class="justify-start text-xs">Manage Programs</Button>
                                            <Button @click="router.push({path:`/event/populate/${event.event_id}`})" variant="ghost" size="sm" class="justify-start text-xs">Populate Indicators</Button>
                                            <Button variant="ghost" size="sm" class="justify-start text-xs text-red-600">Delete</Button>
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- footer -->
        <div class="w-full h-[50px] flex justify-center items-center border p-2">
           
        </div>
    </div>

</template>