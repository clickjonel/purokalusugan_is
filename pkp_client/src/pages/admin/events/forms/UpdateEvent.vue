<script setup lang="ts">
    import { Button } from "@/components/ui/button";
    import { Input } from "@/components/ui/input";
    import { CalendarIcon } from "lucide-vue-next"
    import { onMounted, ref } from "vue"
    import { Calendar } from "@/components/ui/calendar"
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import { Select,SelectContent,SelectGroup,SelectItem,SelectLabel,SelectTrigger,SelectValue } from '@/components/ui/select'
    import axios from '@/axios/axios';
    import { Card,CardContent,CardDescription,CardFooter,CardHeader,CardTitle } from '@/components/ui/card'
    import { toast } from 'vue-sonner'
    import { useRouter,useRoute } from "vue-router";
    import MultipleSelectProgams from "@/components/selections/MultipleSelectProgams.vue";
    import MultipleSelectBarangay from "@/components/selections/MultipleSelectBarangay.vue";

    const router = useRouter()
    const route = useRoute()

    const event = ref<Event>({})


    onMounted(()=>{
        fetchEvent()
    })

    async function fetchEvent(): Promise<void> {
        try {
            const response = await axios.get('/event/fetch', {
                params: {
                    event_id: route.params.id
                }
            });

            event.value = response.data.event;
            event.value.event_type = String(event.value.event_type)
            console.log(event.value)
        } catch (error) {
            console.error('Error fetching event:', error);
            toast.error('Failed to load event data');
        }
    }


    interface event {
        event_type:string|number
    }






</script>

<template>
   
    <div class="w-full flex justify-center items-center p-4">
        <div class="w-full flex flex-col justify-center items-center gap-4">
            <Card class="w-full">
                <CardHeader>
                    <CardTitle>Update Event Details</CardTitle>
                    <CardDescription>Update Event Details and other data related to event</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">
                        <div class="w-full flex justify-start items-start gap-4">
                            <Input v-model="event.event_name" type="text" placeholder="Event Name" class=""/>
                            <Input v-model="event.event_venue" type="text" placeholder="Event Venue" class=""/>
                            <Input v-model="event.event_budget" type="number" placeholder="Event Budget" class=""/>
                            <Input v-model="event.event_actual_budget" type="number" placeholder="Event Actual Budget" class=""/>
                            <Input v-model="event.event_fund_source" type="text" placeholder="Fund Source" class=""/>
                        </div>
                        <div class="w-full flex flex-col justify-start items-start gap-4">
                            <Input v-model="event.event_proponents" type="text" placeholder="Event Proponents(if multiple separate by commas)" class=""/>
                            <Input v-model="event.event_partners" type="text" placeholder="Event Partners(if multiple separate by commas)" class=""/>
                        </div>
                        
                        <div class="w-full flex justify-between items-center gap-4">
                            
                            <Select v-model="event.event_type">
                                <SelectTrigger class="w-1/3">
                                    <SelectValue placeholder="Select Event Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Event Types</SelectLabel>
                                        <SelectItem value="1">Small Scale</SelectItem>
                                        <SelectItem value="2">Large Scale</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>

                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button class="w-1/3"
                                        variant="outline">
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        {{ event.event_date_start ?? 'Event Date Start' }}
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-auto p-0">
                                    <Calendar v-model="event.event_date_start" initial-focus />
                                </PopoverContent>
                            </Popover>

                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button class="w-1/3"
                                        variant="outline">
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        {{ event.event_date_end ?? 'Event Date End' }}
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-auto p-0">
                                    <Calendar v-model="event.event_date_end" initial-focus />
                                </PopoverContent>
                            </Popover>

                        </div>

                        <MultipleSelectProgams v-model="event.programs"/>
                        <MultipleSelectBarangay v-model="event.barangays"/>

                    </div>
                </CardContent>
                <CardFooter>
                    <Button variant="default" class="cursor-pointer" size="sm">Update Event Details</Button>
                </CardFooter>
            </Card>

             <Card class="w-full">
                <CardHeader>
                    <CardTitle>Update Event Values</CardTitle>
                    <CardDescription>Update Event Details and other data related to event</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">
                        <div v-for="value in event.values" class="w-full flex flex-col justify-start items-center gap-2 bg-sky-50 rounded0md shadow-md p-2">
                            <span class="w-full text-left uppercase font-medium">Barangay {{ value.barangay.barangay_name }}</span>
                            <span class="w-full text-left uppercase text-sm font-light">{{ value.indicator_disaggregation.indicator.indicator_name }}</span>
                           <Input v-model="value.value" type="text" placeholder="Value" class="bg-white"/>
                           <Input v-model="value.remarks" type="text" placeholder="Remarks" class="bg-white"/>
                        </div>
                    </div>
                </CardContent>
                <CardFooter>
                    <Button variant="default" class="cursor-pointer" size="sm">Update Event Values</Button>
                </CardFooter>
            </Card>

        </div>
    </div>
    
</template>