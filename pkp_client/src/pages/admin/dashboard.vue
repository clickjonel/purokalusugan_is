<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { Card,CardContent,CardDescription,CardFooter,CardHeader,CardTitle } from "@/components/ui/card";
    import { HousePlus,TriangleAlert,MapPinCheck,Users    } from 'lucide-vue-next';
    import { Table,TableBody,TableCell,TableHead,TableHeader,TableRow } from '@/components/ui/table';
    import axios from '@/axios/axios';

    const dashboardData = ref({
        programs_count:0,
        indicators:{},
        indicators_count:0
    })

    onMounted(()=>{
        getData()
    })

    function getData(){
        axios.get('/exec/dashboard',{})
        .then((response)=>{
            console.log(response.data.data)
            dashboardData.value = response.data.data
        })
    }
</script>

<template>
    <div class="w-full h-full flex flex-col justify-start items-start gap-2 p-2 overflow-y-scroll">
       
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
                    <span class="text-5xl font-black">{{ dashboardData.programs_count }}</span>
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
                    <span class="text-5xl font-black">{{ dashboardData.indicators_count }}</span>
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
                    <span class="text-5xl font-black">{{ dashboardData.pk_sites_count }}</span>
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
                    <span class="text-5xl font-black">{{ dashboardData.hrh_count }}</span>
                </CardContent>
            </Card>

        </div>

        <div class="w-full flex flex-col justify-start items-start p-4">
            <span class="text-lg uppercase font-semibold p-2 ml-3">Programs</span>
            <div class="w-full grid grid-cols-4 gap-4">
                <Card v-for="program in dashboardData.programs" class="w-full">
                    <CardHeader>
                        <CardTitle>
                            <div class="w-full flex justify-between items-center">
                                {{ program.program_name }}
                            </div>
                    </CardTitle>
                        <CardDescription>Indicators</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <span class="text-2xl font-semibold">{{ program.indicators_count }}</span>
                    </CardContent>
                </Card>
            </div>
        </div>

        <div class="w-full flex flex-col justify-start items-start">
            <span class="text-lg uppercase font-semibold p-2 ml-3">Program Indicators</span>
            <div class="w-full flex flex-col justify-start items-start p-4">
                <div class="w-full flex justify-start items-stretch divide-x border-t border-x divide-black border-black text-center uppercase font-semibold">
                    <span class="w-[15%] p-2">Program</span>
                    <span class="w-[40%] p-2">Indicator</span>
                    <span class="w-[15%] p-2">Scope</span>
                    <span class="w-[15%] p-2">Disaggregation</span>
                    <span class="w-[15%] p-2">Status</span>
                </div>
                <div v-for="indicator in dashboardData.indicators" class="w-full flex justify-start items-stretch divide-x border-t border-x divide-black border-black text-left text-sm font-light last:border-b">
                    <span class="p-2 w-[15%]">{{ indicator.program.program_name }}</span>
                    <span class="p-2 w-[40%]">{{ indicator.indicator_name }}</span>
                    <span class="p-2 w-[15%]">{{ indicator.indicator_scope === 1 ? 'Individual Scope' : 'Household Scope' }}</span>
                    <span class="p-2 w-[15%]">{{ indicator.disaggregation }}</span>
                    <span class="p-2 w-[15%]">{{ indicator.indicator_status === 1 ? 'Active' : 'Deactivated' }}</span>
                </div>
            </div>
        </div>
        

    </div>
</template>