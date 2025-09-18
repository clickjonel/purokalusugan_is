<script setup lang="ts">
    import { onMounted, ref } from "vue"
    import { Card,CardContent,CardDescription,CardFooter,CardHeader,CardTitle } from '@/components/ui/card'
    import { Button } from "@/components/ui/button";
    import { Input } from '@/components/ui/input'
    import SelectIndicatorScope from "@/components/selections/SelectIndicatorScope.vue";
    import SelectIndicatorStatus from "@/components/selections/SelectIndicatorStatus.vue";
    import SelectProgram from "@/components/selections/SelectProgram.vue";
    import { useRouter,useRoute } from "vue-router";
    import axios from '@/axios/axios';
    import { toast } from 'vue-sonner'
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import MultipleSelectDisaggregations from "@/components/selections/MultipleSelectDisaggregations.vue";


    const router = useRouter()
    const route = useRoute()
    const indicator = ref()
    const selectedDisaggregations = ref<Disaggregation[]>([])
    const showDisaggregationSelection = ref(false)
    const multipleSelectDisaggregationsComponentKey = ref(0);

    onMounted(()=>{
        fetchIndicator()
    })

    function fetchIndicator(){
         axios.get('/indicator/find',{
            params:{
                indicator_id:route.params.id
            }
         })
        .then((response) => {
            indicator.value = response.data.data
            multipleSelectDisaggregationsComponentKey.value++
            console.log(indicator.value)
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

    function saveUpdate(){
        const formattedIndicator = formatIndicator()
        axios.post('/indicator/update',formattedIndicator)
        .then((response) => {
            toast('Action Successfull', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
            // router.push({path:'/admin/indicators'})
        })
        .catch((error) => {
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

    function formatIndicator(){
        const formattedIndicator = {
            indicator_id:route.params.id,
            program_id: indicator.value.program.program_id,
            indicator_name: indicator.value.indicator_name,
            indicator_scope: indicator.value.indicator_scope,
            indicator_description: indicator.value.indicator_description,
            indicator_status: indicator.value.indicator_status,
            indicator_code: indicator.value.indicator_code
        }

        return formattedIndicator;
    }

    function deleteDisaggregation(id:number){
         axios.delete('/indicator/disaggregation/remove',{
            data:{
                indicator_disaggregation_id:id
            }
         })
        .then((response) => {
            toast('Action Successfull', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
           fetchIndicator()
        })
        .catch((error) => {
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

    function saveIndicatorDisaggregations(){
        const selectedDisaggregationIds:number[] = selectedDisaggregations.value.map(d => d.disaggregation_id);
         axios.post('/indicator/disaggregation/add',{
            indicator_id:route.params.id,
            disaggregation_ids:selectedDisaggregationIds
         })
        .then((response) => {
            toast('Action Successfull', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
            selectedDisaggregations.value = []
            fetchIndicator()
        })
        .catch((error) => {
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

    interface Disaggregation {
        disaggregation_id: number;
        disaggregation_code: string;
        disaggregation_name: string;
    }

</script>

<template>
    <div class="w-full flex justify-start items-start p-4 gap-4">
        <Card class="w-1/2">
            <CardHeader>
                <CardTitle>Update Indicator</CardTitle>
                <CardDescription>Update Indicator Details and Relations</CardDescription>
            </CardHeader>

            <CardContent>
                <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">
                   <div v-if="indicator" class="w-full flex flex-col justify-between items-center gap-4">
                        <Input v-model="indicator.indicator_name" type="text" placeholder="Indicator Name" class=""/>
                        <Input v-model="indicator.indicator_description" type="text" placeholder="Indicator Description" class=""/>
                        <Input v-model="indicator.indicator_code" type="text" placeholder="Indicator Code" class=""/>
                        <SelectIndicatorScope v-model="indicator.indicator_scope"/>
                        <SelectIndicatorStatus v-model="indicator.indicator_status"/>
                        <SelectProgram v-model="indicator.program"/>
                   </div>

                </div>
            </CardContent>

            <CardFooter>
                <Button @click="saveUpdate" variant="default" class="cursor-pointer" size="sm">Update Indicator</Button>
            </CardFooter>
        </Card>

         <Card class="w-1/2">
            <CardHeader>
                <CardTitle>Manage</CardTitle>
                <CardDescription>Manage Indicator Disaggregations</CardDescription>
            </CardHeader>

            <CardContent>
                <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">

                    <Card v-for="d in indicator?.disaggregations" class="w-full">
                        <CardHeader>
                            <CardTitle>
                                <div class="w-full flex justify-between items-center">
                                    <span>{{ d.disaggregation_code }}</span>
                                    
                                    <Popover>
                                        <PopoverTrigger as-child>
                                            <Button class="font-poppins font-normal" variant="outline">
                                                Delete
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent class="w-[200px] p-0">
                                            <div class="w-full flex flex-col justify-start items-center p-4 gap-4">
                                                <span class="text-sm font-medium uppercase">Confirm Delete?</span>
                                                <div class="w-full flex justify-end items-center gap-2">
                                                    <Button @click="deleteDisaggregation(d.pivot.indicator_disaggregation_id)" class="font-poppins font-normal cursor-pointer text-xs" size="sm" variant="destructive">Delete</Button>
                                                </div>
                                            </div>
                                        </PopoverContent>
                                    </Popover>

                                </div>
                            </CardTitle>
                            <CardDescription></CardDescription>
                        </CardHeader>

                        <CardContent>
                            <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">
                                  <span class="w-full text-lg uppercase font-semibold">{{ d.disaggregation_name }}</span>
                            </div>
                        </CardContent>

                    </Card>

                    <MultipleSelectDisaggregations v-if="showDisaggregationSelection" v-model="selectedDisaggregations" :remove="indicator.disaggregations" :key="multipleSelectDisaggregationsComponentKey"/>
                    <Button v-if="showDisaggregationSelection && selectedDisaggregations.length > 0" @click="saveIndicatorDisaggregations" class="font-poppins font-normal cursor-pointer text-xs" size="sm" variant="default">Save Selected</Button>
                    <Button v-if="!showDisaggregationSelection" @click="showDisaggregationSelection = true" class="font-poppins font-normal cursor-pointer text-xs" size="sm" variant="default">Add Disaggregation/s</Button>

                </div>
            </CardContent>

            <CardFooter>
                <!-- <Button @click="saveUpdate" variant="default" class="cursor-pointer" size="sm">Add Indicator</Button> -->
            </CardFooter>
        </Card>
    </div>
</template>