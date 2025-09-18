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
    import { format } from 'date-fns';
    import { useRouter,useRoute } from "vue-router";

    const router = useRouter()
    const route = useRoute()
    const event = ref()
   

    onMounted(()=>{
       fetchEvent()
    })

    function fetchEvent(){
        axios.get('/event/fetch',{
            params:{
                event_id:route.params.id
            }
        })
        .then((response) => {
            event.value = response.data.event
            console.log(event.value)
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

</script>

<template>
   
    <div class="w-full flex justify-center items-center">
        <div class="w-full p-4 flex justify-center items-center">
            <Card class="w-full">
                <CardHeader>
                    <CardTitle>Event Population Form</CardTitle>
                    <!-- <CardDescription>Fill up the program indicators according to program services served.</CardDescription> -->
                </CardHeader>
                <CardContent>
                    <div class="w-full flex flex-col justify-start items-start gap-4 font-poppins">
                       
                        <!-- event details -->
                        <Card v-if="event" class="w-full">
                            <CardHeader>
                                <CardTitle>{{ event.event_name }}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="w-full flex flex-col justify-start items-start gap-4 font-poppins">
                                    <div class="w-full flex flex-col justify-start items-start gap-2 text-sm">
                                        <div class="w-full flex justify-start items-center gap-4">
                                            <span class="font-medium">Venue: </span>
                                            <span class="font-light">{{ event.event_venue }}</span>
                                        </div>
                                         <div class="w-full flex justify-start items-center gap-4">
                                                <span class="font-medium">Budget: </span>
                                                <span class="font-light">{{ event.event_budget }}</span>
                                            </div>
                                            <div class="w-full flex justify-start items-center gap-4">
                                                <span class="font-medium">Actual Budget: </span>
                                                <span class="font-light">{{ event.event_actual_budget }}</span>
                                            </div>
                                        <div class="w-full flex justify-start items-center gap-4">
                                           <div class="w-1/2 flex justify-start items-start gap-2">
                                                <span class="w-1/4 font-medium">Proponents:</span>
                                                <div class="w-3/4 flex flex-col justify-start items-start gap-2 font-light">
                                                    <span v-for="proponent in event.event_proponent_array">{{ proponent }}</span>
                                                </div>
                                           </div>
                                           <div class="w-1/2 flex justify-start items-start gap-2">
                                                <span class="w-1/4 font-medium">Partners:</span>
                                                <div class="w-3/4 flex flex-col justify-start items-start gap-2 font-light">
                                                    <span v-for="partner in event.event_partner_array">{{ partner }}</span>
                                                </div>
                                           </div>
                                        </div>
                                    </div>

                                </div>
                            </CardContent>
                        </Card>

                        <!-- event barangays with programs -->
                        <Card v-if="event" v-for="barangay in event.barangays" class="w-full">
                            <CardHeader>
                                <CardTitle>{{ barangay.barangay_name }}</CardTitle>
                                <CardDescription>Fill up the program indicators according to program services served.</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div class="w-full flex flex-col justify-start items-start font-poppins border border-black divide-y divide-black">
                                    <div class="w-full flex justify-start items-center divide-x divide-black">
                                        <span class="w-1/4 p-2 font-medium uppercase text-left text-sm">Program</span>
                                        <span class="w-3/4 p-2 font-medium uppercase text-center text-sm">Indicators</span>
                                    </div>
                                    <div v-for="program in event.programs" class="w-full flex justify-start items-stretch divide-x divide-black text-light text-sm">
                                        <span class="w-1/4 p-2">{{ program.program_name }}</span>
                                        <div class="w-3/4 flex flex-col justify-start items-stretch divide-y divide-black">
                                            <div v-for="indicator in program.indicators" class="w-full flex flex-col justify-start items-start gap-2 p-2">
                                                <span class="text-xs">{{ indicator.indicator_name }}</span>
                                                <div class="w-full flex justify-between items-center gap-2">
                                                    <Input v-for="disaggregate in indicator.disaggregations" type="number" :placeholder="disaggregate.disaggregation_name"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                    </div>
                </CardContent>
                <CardFooter>
                    <Button variant="default" class="cursor-pointer" size="sm">Save Data</Button>
                </CardFooter>
            </Card>
        </div>
    </div>

</template>