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
    import { useRouter } from "vue-router";
    import MultipleSelectProgams from "@/components/selections/MultipleSelectProgams.vue";
    import MultipleSelectBarangay from "@/components/selections/MultipleSelectBarangay.vue";

    const router = useRouter()
    const event = ref<Event>({
        event_name:'',
        event_venue:'',
        event_budget:undefined,
        event_actual_budget:undefined,
        event_fund_source:'',
        event_proponents:'',
        event_partners:'',
        event_date_start:undefined,
        event_date_end:undefined,
        programs:[],
        barangays:[],
        event_type:undefined
    })

    const provinces = ref<Province[]>([])
    const municipalities = ref<Municipality[]>([])
    const barangays = ref<Barangay[]>([])

    const selectedProvince = ref()
    const selectedMunicipality = ref()
    const selectedBarangay = ref()

    onMounted(()=>{
        fetchProvinces()
    })

    function fetchProvinces(){
         axios.get('/province/list')
        .then((response) => {
            provinces.value = response.data.data
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

    function fetchMunicipalities(){
        axios.get('/municipality/province/find',{
            params:{
                province_id:selectedProvince.value.province_id
            }
        })
        .then((response) => {
            municipalities.value = response.data.data
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

    function fetchBarangays(){
        axios.get('/barangay/municipality/find',{
            params:{
                municipality_id:selectedMunicipality.value.municipality_id
            }
        })
        .then((response) => {
            barangays.value = response.data.data
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }


    function pushToBarangays(){
        if(event.value.barangays.some(brgy => brgy.barangay_id === selectedBarangay.value.barangay_id)){
            toast('Duplicate', {
                description: 'Barangay Already Added to list.',
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
       }
       else{
            event.value.barangays.push(selectedBarangay.value)
            selectedBarangay.value = { barangay_name: '', barangay_id: 0 }
       } 
    }

    function saveEvent(){
        const event = formatEvent()
        
         axios.post('/event/save', event)
        .then((response) => {
            toast('Action Successfull', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
            router.push({path:'/admin/events'})
        })
        .catch((error) => {
            console.log(error)
            if (error.response) {
                toast('Failed With Errors', {
                    description: error.response.data.message,
                    action: {
                        label: 'Close',
                        onClick: () => toast.dismiss(),
                    },
                })
            }
        })
        .finally(() => {

        })
    }

    function formatEvent(){
        const formattedDateStart = event.value.event_date_start ? format(event.value.event_date_start, 'yyyy-MM-dd') : undefined 
        const formattedDateEnd = event.value.event_date_end ? format(event.value.event_date_end, 'yyyy-MM-dd') : undefined 
        const formattedEvent:FormattedEvent = {
            event_name: event.value.event_name,
            event_venue: event.value.event_venue,
            event_budget: event.value.event_budget as number,
            event_actual_budget: event.value.event_actual_budget as number,
            event_fund_source: event.value.event_fund_source,
            event_proponents: event.value.event_proponents,
            event_partners: event.value.event_partners,
            programs: event.value.programs.map(program => program.program_id),
            event_date_start: formattedDateStart as string,
            event_date_end: formattedDateEnd as string,
            barangays: event.value.barangays.map(barangay => barangay.barangay_id),
            event_type: event.value.event_type as number
        }

        return formattedEvent
    }


    interface Event {
        event_name:string
        event_venue:string
        event_budget:number|undefined
        event_actual_budget:number|undefined
        event_fund_source:string
        event_proponents:string
        event_partners:string
        programs:Program[]
        event_date_start:undefined
        event_date_end:undefined
        barangays:Barangay[]
        event_type:number|undefined
    }

    interface FormattedEvent {
        event_name:string
        event_venue:string
        event_budget:number
        event_actual_budget:number
        event_fund_source:string
        event_proponents:string
        event_partners:string
        programs:number[]
        event_date_start:string
        event_date_end:string
        barangays:number[]
        event_type:number
    }

    interface Program {
        program_name:string
        program_id:number
    }

    interface Barangay {
        barangay_id:number
        barangay_name:string
    }

    interface Province {
        province_id:number
        province_name:string
    }

    interface Municipality {
        municipality_id:number
        municipality_name:string
    }

</script>

<template>
   
    <div class="w-full flex justify-center items-center">
        <div class="w-3/4 p-4 flex justify-center items-center">
            <Card class="w-full">
                <CardHeader>
                    <CardTitle>Create Event</CardTitle>
                    <CardDescription>Fill Up the form then add programs and barangays which is mandatory. Click Create Event Button to save event when all required data are filled.</CardDescription>
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

                        <!-- <div class="w-full flex flex-col justify-start items-start">
                            <div class="w-full flex justify-between items-center">
                                <span class="font-semibold uppercase">Added Programs</span>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button variant="outline">
                                        Add Program
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="flex flex-col justify-start items-start w-[500px] p-4 gap-4">
                                        <Select v-model="selectedProgram">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Select Program" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectLabel>Programs</SelectLabel>
                                                    <SelectItem v-for="program in programs" :value="program">{{ program.program_name }}</SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                        <Button @click="pushToPrograms()" class="w-full bg-gray-200" variant="outline">Push To Program List</Button>
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div class="w-full flex flex-col justify-start items-start ml-4">
                                <span v-for="program in event.programs" class="font-light">{{ program.program_name }}</span>
                            </div>
                        </div> -->

                        <MultipleSelectProgams v-model="event.programs"/>
                        <MultipleSelectBarangay v-model="event.barangays"/>

                        <!-- <div class="w-full flex flex-col justify-start items-start">
                            <div class="w-full flex justify-between items-center">
                                <span class="font-semibold uppercase">Added Barangays</span>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button variant="outline">
                                        Add Barangays
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="flex flex-col justify-start items-start w-[500px] p-4 gap-4">
                                        <Select v-model="selectedProvince" @update:model-value="fetchMunicipalities">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Select Province" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectLabel>Programs</SelectLabel>
                                                    <SelectItem v-for="province in provinces" :value="province">{{ province.province_name }}</SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>

                                        <Select v-model="selectedMunicipality" @update:model-value="fetchBarangays">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Select Municipality" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectLabel>Municipalities</SelectLabel>
                                                    <SelectItem v-for="municipality in municipalities" :value="municipality">{{ municipality.municipality_name }}</SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>

                                        <Select v-model="selectedBarangay">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Select Barangay" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectLabel>Barangays</SelectLabel>
                                                    <SelectItem v-for="barangay in barangays" :value="barangay">{{ barangay.barangay_name }}</SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                        <Button @click="pushToBarangays" class="w-full bg-gray-200" variant="outline">Push To Barangays List</Button>
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div class="w-full flex flex-col justify-start items-start ml-4">
                                <span v-for="barangay in event.barangays" class="font-light">{{ barangay.barangay_name }}</span>
                            </div>
                        </div> -->

                    </div>
                </CardContent>
                <CardFooter>
                    <Button @click="saveEvent" variant="default" class="cursor-pointer" size="sm">Create Event</Button>
                </CardFooter>
            </Card>
        </div>
    </div>
    
</template>