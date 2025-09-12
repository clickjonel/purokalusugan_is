<script setup lang="ts">
    import { Button } from "@/components/ui/button";
    import { Input } from "@/components/ui/input";
    import { CalendarIcon } from "lucide-vue-next"
    import { onMounted, ref } from "vue"
    import { Calendar } from "@/components/ui/calendar"
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import { Select,SelectContent,SelectGroup,SelectItem,SelectLabel,SelectTrigger,SelectValue } from '@/components/ui/select'
    import axios from '@/axios/axios';

    const event = ref<Event>({
        event_date:undefined,
        programs:[],
        barangays:[],
        province_venue:0
    })

    const provinces = ref<Province[]>([])
    const municipalities = ref<Municipality[]>([])
    const barangays = ref<Barangay[]>([])
    const programs = ref<Program[]>([])

    const selectedProgram = ref<Program>({
        program_name:'',
        program_id:0
    })

    const selectedProvince = ref()
    const selectedMunicipality = ref()
    const selectedBarangay = ref()

    onMounted(()=>{
        fetchProvinces()
        fetchPrograms()
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

    function fetchPrograms(){
        axios.get('/program/list')
        .then((response) => {
            programs.value = response.data.data
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

    function pushToPrograms(){
        event.value.programs.push(selectedProgram.value)
        selectedProgram.value = { program_name: '', program_id: 0 }
    }

     function pushToBarangays(){
        event.value.barangays.push(selectedBarangay.value)
        selectedBarangay.value = { barangay_name: '', barangay_id: 0 }
    }

    interface Event {
        programs:Program[]
        event_date:undefined
        barangays:Barangay[]
        province_venue:number
        // participants:
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
    <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">
        <div class="w-full flex justify-between items-center">
            <span class="font-bold text-xl">Create Event</span>
            <Button variant="default" class="cursor-pointer" size="sm">Create Event</Button>
        </div>
        <div class="w-full flex justify-start items-start gap-4">
            <Input type="text" placeholder="Event Name" class=""/>
            <Input type="text" placeholder="Event Venue" class=""/>
            <Input type="text" placeholder="Event Budget" class=""/>
            <Input type="text" placeholder="Event Actual Budget" class=""/>
            <Input type="text" placeholder="Fund Source" class=""/>
        </div>
        <div class="w-full flex flex-col justify-start items-start gap-4">
            <Input type="text" placeholder="Event Proponents(if multiple separate by commas)" class=""/>
            <Input type="text" placeholder="Event Partners(if multiple separate by commas)" class=""/>
        </div>
        
        <div class="w-full flex justify-between items-center gap-4">
            
            <Select>
                <SelectTrigger class="w-1/2">
                    <SelectValue placeholder="Select Event Type" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Event Types</SelectLabel>
                        <SelectItem value="1">Small Scale</SelectItem>
                        <SelectItem value="2">Large Event</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>

            <Popover>
                <PopoverTrigger as-child>
                    <Button class="w-1/2"
                        variant="outline">
                        <CalendarIcon class="mr-2 h-4 w-4" />
                        {{ event.event_date ?? 'Event Date' }}
                    </Button>
                </PopoverTrigger>
                <PopoverContent class="w-auto p-0">
                    <Calendar v-model="event.event_date" initial-focus />
                </PopoverContent>
            </Popover>
        </div>

        <div class="w-full flex flex-col justify-start items-start">
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
        </div>

        <div class="w-full flex flex-col justify-start items-start">
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
        </div>

    </div>
</template>