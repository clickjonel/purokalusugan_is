<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { HousePlus,TriangleAlert,MapPinCheck,Users    } from 'lucide-vue-next';
    import axios from '@/axios/axios';
    import { Card,CardContent,CardDescription,CardHeader,CardTitle } from "@/components/ui/card"
    import { Select,SelectContent,SelectGroup,SelectItem,SelectLabel,SelectTrigger,SelectValue } from '@/components/ui/select'

    const dashboardData = ref<DashboardData>()

    const selectedProgram = ref()

    onMounted(()=>{
        getData()
    })

    function getData(){
        axios.get('/exec/dashboard',{})
        .then((response)=>{
            // console.log(response.data.data)
            dashboardData.value = response.data.data
            console.log(response.data.data)
        })
    }

    function setSelected(programID:any){
        axios.get('/dashboard/program/find',{
            params:{
                program_id:programID
            }
        })
        .then((response)=>{
            console.log(response.data.program)
            selectedProgram.value = response.data.program
        })
    }

    interface DashboardData {
        programs: ProgramSummary[];
        programs_count: number;
        indicators_count: number;
        pk_sites_count: number;
        hrh_count: number;
        eventData:EventData
    }

    interface ProgramSummary {
        program_id: number;
        program_name: string;
    }

    interface EventData {
        total:number
        total_budget_spent:number
        total_barangays:number
        total_small_scale:number
        total_large_scale:number
    }


</script>

<template>
    <div class="w-full h-full flex flex-col justify-start items-start gap-4 p-4 overflow-y-scroll">

        <div class="w-full grid grid-cols-4 gap-4 p-2">
            <Card class="w-full">
                <CardHeader>
                    <CardTitle>
                        <div class="w-full flex justify-between items-center">
                            <span>Programs</span>
                            <HousePlus :size="14"/>
                        </div>
                </CardTitle>
                    <CardDescription>Current total of PK Programs</CardDescription>
                </CardHeader>
                <CardContent>
                    <span class="text-5xl font-black">{{ dashboardData?.programs_count }}</span>
                </CardContent>
            </Card>

            <Card class="w-full">
                <CardHeader>
                    <CardTitle>
                        <div class="w-full flex justify-between items-center">
                            <span>Indicators</span>
                            <TriangleAlert  :size="14"/>
                        </div>
                </CardTitle>
                    <CardDescription>Total number of Indicators for PK</CardDescription>
                </CardHeader>
                <CardContent>
                    <span class="text-5xl font-black">{{ dashboardData?.indicators_count }}</span>
                </CardContent>
            </Card>

            <Card class="w-full">
                <CardHeader>
                    <CardTitle>
                        <div class="w-full flex justify-between items-center">
                            <span>PK Sites</span>
                            <MapPinCheck  :size="14"/>
                        </div>
                </CardTitle>
                    <CardDescription>Total PK Sites</CardDescription>
                </CardHeader>
                <CardContent>
                    <span class="text-5xl font-black">{{ dashboardData?.pk_sites_count }}</span>
                </CardContent>
            </Card>

            <Card class="w-full">
                <CardHeader>
                    <CardTitle>
                        <div class="w-full flex justify-between items-center">
                            <span>HRH</span>
                           <Users  :size="14"/>
                        </div>
                </CardTitle>
                    <CardDescription>Total HRH</CardDescription>
                </CardHeader>
                <CardContent>
                    <span class="text-5xl font-black">{{ dashboardData?.hrh_count }}</span>
                </CardContent>
            </Card>

        </div>

        <div class="w-full flex flex-col justify-start items-start p-2">
            <Select @update:model-value="setSelected">
                <SelectTrigger class="w-1/3">
                    <SelectValue placeholder="Select Program to Show Indicator Data" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Programs</SelectLabel>
                        <SelectItem v-for="program in dashboardData?.programs" :value="program.program_id">{{ program.program_name }}</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
            <!-- <span class="p-2 text-sm font-medium">Accumulated Data for this month:</span> -->
        </div>

        <div v-if="selectedProgram" class="w-full grid grid-cols-2 gap-2">
            <Card v-for="indicator in selectedProgram?.indicators" class="w-full">
                <CardHeader>
                    <CardTitle>
                        <div class="w-full flex justify-between items-center">
                            <span class="text-sm capitalize font-light">{{ indicator.indicator_name }}</span>
                            <!-- <HousePlus :size="14"/> -->
                        </div>
                    </CardTitle>
                    <!-- <CardDescription>Current total of PK Programs</CardDescription> -->
                </CardHeader>
                <CardContent>
                   <div class="w-full flex justify-between items-center">
                        <span class="text-5xl font-bold">{{ indicator.total_sum }}</span>
                        <!-- <span>Rise/Fall</span> -->
                   </div>
                   <div class="w-full flex flex-col justify-start items-start">
                        <span class="text-5xl font-black">{{ indicator.sum_of_values }}</span>
                   </div>
                </CardContent>
            </Card>
        </div>

        <Card class="w-full">
            <CardHeader>
                <CardTitle>
                    <div class="w-full flex justify-between items-center">
                        <span>PuroKalusugan Events</span>
                        <Users  :size="14"/>
                    </div>
            </CardTitle>
                <CardDescription>PuroKaulusugan Events Data</CardDescription>
            </CardHeader>
            <CardContent>
                <span class="text-7xl font-black p-4">{{ dashboardData?.eventData?.total }}</span>
                <div class="w-full grid grid-cols-3 gap-4 p-4">

                    <Card class="w-full">
                        <CardHeader>
                            <CardTitle>
                                <div class="w-full flex justify-between items-center">
                                    <span>Total Spent Budget</span>
                                    <!-- <Users  :size="14"/> -->
                                </div>
                        </CardTitle>
                            <CardDescription>Total Budget Spent on Events</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <span class="text-5xl font-black">{{ dashboardData?.eventData?.total_budget_spent }}</span>
                        </CardContent>
                    </Card>

                     <Card class="w-full">
                        <CardHeader>
                            <CardTitle>
                                <div class="w-full flex justify-between items-center">
                                    <span>Barangays</span>
                                    <!-- <Users  :size="14"/> -->
                                </div>
                        </CardTitle>
                            <CardDescription> Total Number of Barangay Participants on PK Events</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <span class="text-5xl font-black">{{ dashboardData?.eventData?.total_barangays }}</span>
                        </CardContent>
                    </Card>

                    <Card class="w-full">
                        <CardHeader>
                            <CardTitle>
                                <div class="w-full flex justify-between items-center">
                                    <span>Event Scales</span>
                                    <!-- <Users  :size="14"/> -->
                                </div>
                        </CardTitle>
                            <CardDescription>Event Breakdown Small Scale vs Large Scale</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <span class="text-5xl font-black">{{ `${dashboardData?.eventData?.total_small_scale} - ${dashboardData?.eventData?.total_large_scale}` }}</span>
                        </CardContent>
                    </Card>
                </div>
            </CardContent>
        </Card>
       

    </div>
</template>