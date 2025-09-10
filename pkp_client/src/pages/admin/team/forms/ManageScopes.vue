<script setup lang="ts"> 
    import { onMounted, ref } from "vue";
    import { Button } from "@/components/ui/button";
    import axios from '@/axios/axios';
    import { useRoute } from 'vue-router';
    import { Table,TableBody,TableCell,TableHead,TableHeader,TableRow } from '@/components/ui/table'
    import { Popover,PopoverContent,PopoverTrigger } from '@/components/ui/popover'
    import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxList } from "@/components/ui/combobox"
    import { Search } from "lucide-vue-next"
    import { toast } from 'vue-sonner'

    const route = useRoute()
    const team = ref<Team>({
        scopes: [],
        team_name: '',
        team_scope_id: 0,
        barangay: {
            barangay_id: 0,
            barangay_name: '',
            municipality: {
                municipality_id: 0,
                municipality_name: '',
                province: {
                    province_id: 0,
                    province_name: ''
                }
            },
            province: {
                province_id: 0,
                province_name: ''
            }
        }
    })
    const provinces = ref<Barangay['province'][]>([])
    const municipalities = ref<Barangay['municipality'][]>([])
    const barangays = ref<Barangay[]>([])

    const selectedProvince = ref<Barangay['province'] | null>(null)
    const selectedMunicipality = ref<Barangay['municipality'] | null>(null)
    const selectedBarangay = ref<Barangay | null>(null)


    onMounted(()=>{
        fetchTeam()
        fetchProvinces()
    })

    function fetchTeam(){
        axios.get('/team/find',{
            params:{
                team_id:route.params.id
            }
        })
        .then((response) => {
            team.value = response.data.team
            console.log(team.value)
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

    function fetchProvinces(){
        axios.get('/province/list')
        .then((response) => {
            provinces.value = response.data.data
            // console.log(response.data)
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
                province_id:selectedProvince.value?.province_id
            }
        })
        .then((response) => {
            municipalities.value = response.data.data
            // console.log(response.data)
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
                municipality_id:selectedMunicipality.value?.municipality_id
            }
        })
        .then((response) => {
            barangays.value = response.data.data
            // console.log(response.data)
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

    function saveScope(){
         axios.post('/team/scope/create',{
            barangay_id:selectedBarangay.value?.barangay_id,
            team_id:route.params.id
        })
         .then((response) => {
           toast('Successful Action', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
            selectedProvince.value = null
            selectedMunicipality.value = null
            selectedBarangay.value = null
            fetchTeam()
        })
        .catch((error) => {
             toast('Fields are Required', {
                description: error.response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
        })
        .finally(() => {

        })
    }

    function removeMember(id:number){
       axios.delete('/team/scope/remove',{
          data:{
            team_scope_id:id
          }
        })
         .then((response) => {
           toast('Successful Action', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
            fetchTeam()
        })
        .catch((error) => {
             toast('Failed', {
                description: error.response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
        })
        .finally(() => {

        })
    }

    interface Barangay {
        barangay_id: number
        barangay_name: string
        municipality: {
            municipality_id: number
            municipality_name: string
            province: {
                province_id: number
                province_name: string
            }
        }
        province: {
            province_id: number
            province_name: string
        }
    }

    interface Scope {
        team_scope_id: number
        barangay: Barangay
    }

    interface Team {
        scopes: Scope[],
        team_name: string
        team_scope_id: number
        barangay: Barangay
    }

</script>

<template>
    <div class="w-full h-screen flex flex-col justify-start items-start gap-4 p-4 font-poppins">
        <div class="w-full flex justify-between items-center">
            <span class="text-2xl font-bold">Manage Team Scope</span>
        </div>
        <div class="w-full h-full flex flex-col justify-start items-start gap-2 p-4 border">
            <div class="w-full flex justify-between items-center">
                <span class="text-xl"></span>
                <Popover>
                    <PopoverTrigger asChild>
                        <Button variant="default" size="sm" class="cursor-pointer">Add Scope</Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-100 p-4">
                        <div class="flex flex-col justify-start items-start gap-4">
                            <!-- province selection -->
                            <Combobox by="label" class="w-full" v-model="selectedProvince" @update:model-value="fetchMunicipalities">
                                <ComboboxAnchor class="w-full border rounded-md">
                                    <div class="relative w-full max-w-sm items-center">
                                        <ComboboxInput class="pl-9" :display-value="(province) => province?.province_name ?? ''" placeholder="Select Province..." />
                                        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
                                            <Search class="size-4 text-muted-foreground" />
                                        </span>
                                    </div>
                                </ComboboxAnchor>

                                <ComboboxList>
                                    <ComboboxEmpty>No Province found.</ComboboxEmpty>

                                    <ComboboxGroup>
                                        <ComboboxItem v-for="province in provinces" :key="province.province_id" :value="province">{{ province.province_name }}</ComboboxItem>
                                    </ComboboxGroup>
                                </ComboboxList>
                            </Combobox>

                            <!-- municipality selection -->
                            <Combobox v-model="selectedMunicipality" by="label" class="w-full" @update:model-value="fetchBarangays">
                                <ComboboxAnchor class="w-full border rounded-md">
                                    <div class="relative w-full max-w-sm items-center">
                                        <ComboboxInput class="pl-9" :display-value="(municipality) => municipality?.municipality_name ?? ''" placeholder="Select Municipality..." />
                                        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
                                            <Search class="size-4 text-muted-foreground" />
                                        </span>
                                    </div>
                                </ComboboxAnchor>

                                <ComboboxList>
                                    <ComboboxEmpty>No Province found.</ComboboxEmpty>

                                    <ComboboxGroup>
                                        <ComboboxItem v-for="municipality in municipalities" :key="municipality.municipality_id" :value="municipality">{{ municipality.municipality_name }}</ComboboxItem>
                                    </ComboboxGroup>
                                </ComboboxList>
                            </Combobox>

                            <!-- barangay selection -->
                            <Combobox v-model="selectedBarangay" by="label" class="w-full">
                                <ComboboxAnchor class="w-full border rounded-md">
                                    <div class="relative w-full max-w-sm items-center">
                                        <ComboboxInput class="pl-9" :display-value="(barangay) => barangay?.barangay_name ?? ''" placeholder="Select Barangay..." />
                                        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
                                            <Search class="size-4 text-muted-foreground" />
                                        </span>
                                    </div>
                                </ComboboxAnchor>

                                <ComboboxList>
                                    <ComboboxEmpty>No Province found.</ComboboxEmpty>

                                    <ComboboxGroup>
                                        <ComboboxItem v-for="barangay in barangays" :key="barangay.barangay_id" :value="barangay">{{ barangay.barangay_name }}</ComboboxItem>
                                    </ComboboxGroup>
                                </ComboboxList>
                            </Combobox>

                            <Button @click="saveScope" variant="default" size="sm" class="cursor-pointer hover:bg-green-600 bg-gray-600">Save Scope</Button>
                        </div>
                    </PopoverContent>
                </Popover>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Province</TableHead>
                        <TableHead>Municipality</TableHead>
                        <TableHead>Barangay</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="scope in team?.scopes">
                        <TableCell>{{ scope.barangay.province.province_name}}</TableCell>
                        <TableCell>{{ scope.barangay.municipality.municipality_name}}</TableCell>
                        <TableCell>{{ scope.barangay.barangay_name}}</TableCell>
                        <TableCell class="w-full flex justify-end items-center gap-2">
                            <Popover>
                                <PopoverTrigger asChild>
                                    <Button variant="destructive" size="sm" class="justify-start text-xs cursor-pointer">Remove</Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-100 p-4">
                                     <span>Are you sure you want to remove this Member?</span>
                                    <div class="flex justify-end items-start gap-4 p-4">
                                        <Button @click="removeMember(scope.team_scope_id)" variant="destructive" size="sm" class="justify-start text-xs cursor-pointer">Remove</Button>
                                    </div>
                                </PopoverContent>
                            </Popover>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>