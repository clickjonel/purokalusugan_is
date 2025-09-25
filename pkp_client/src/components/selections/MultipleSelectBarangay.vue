<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import { Button } from "@/components/ui/button";
    import axios from '@/axios/axios';
    import { Select,SelectContent,SelectGroup,SelectItem,SelectLabel,SelectTrigger,SelectValue } from '@/components/ui/select'

    const model = defineModel<Barangay[]>({
      default: []
    })

    const provinces = ref<Province[]>([])
    const municipalities = ref<Municipality[]>([])
    const barangays = ref<Barangay[]>([])

    const selectedProvince = ref()
    const selectedMunicipality = ref()

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

    function push(barangay: Barangay) {
        if (!model.value.some(p => p.barangay_id === barangay.barangay_id)) {
            model.value = [...model.value, barangay];
        }
    }

    function splice(barangay: Barangay, event: MouseEvent) {
        event.stopPropagation();
        model.value = model.value.filter(p => p.barangay_id !== barangay.barangay_id);
    }

    function isSelected(barangay: Barangay) {
      return model.value.some(p => p.barangay_id === barangay.barangay_id);
    }


    interface Province {
        province_name:string
        province_id:number
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
     <Popover>
        <PopoverTrigger as-child>
             <Button class="w-full font-poppins font-normal h-auto" 
                variant="outline">
                <span v-if="model.length === 0">Select Barangays</span>
                <div class="w-full flex flex-wrap justify-start items-center gap-2" >
                    <span v-for="barangay in model" @click="splice(barangay,$event)" class="rounded-full text-xs bg-gray-200 px-2 cursor-pointer hover:bg-red-200">{{ barangay.barangay_name }}</span>
                </div>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[500px] h-[500px] overflow-y-scroll">
            <div class="w-full flex flex-col justify-start items-center p-2 gap-4">
                <Select v-model="selectedProvince" @update:model-value="fetchMunicipalities">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select Province" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Provinces</SelectLabel>
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

                 <div class="w-full flex flex-col justify-start items-center p-2 gap-1 text-sm">
                    <span class="w-full text-left font-medium">Barangays</span>
                    <span @click="push(barangay)" v-for="barangay in barangays" class="w-full px-2 py-1 cursor-pointer hover:bg-gray-100 rounded-md" :class="isSelected(barangay) ? 'bg-gray-100' : 'bg-white' ">{{ barangay.barangay_name }}</span>
                </div>
                
            </div>
        </PopoverContent>
    </Popover>
</template>