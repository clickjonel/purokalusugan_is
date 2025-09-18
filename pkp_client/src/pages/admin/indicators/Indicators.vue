<script setup lang="ts">
import { onMounted, ref } from "vue"
import axios from '@/axios/axios';
import { Button } from "@/components/ui/button";
import { Input } from '@/components/ui/input'
import { Search,EllipsisVertical,UserRoundCog,UserLock } from "lucide-vue-next"
import { toast } from 'vue-sonner'
import { Popover,PopoverContent,PopoverTrigger } from '@/components/ui/popover'
import { Table,TableBody,TableCell,TableHead,TableHeader,TableRow } from '@/components/ui/table'
import { Dialog,DialogContent,DialogDescription,DialogFooter,DialogHeader,DialogTitle,DialogTrigger } from '@/components/ui/dialog'
import { Card,CardContent,CardDescription,CardFooter,CardHeader,CardTitle } from '@/components/ui/card'
import SelectProgram from "@/components/selections/SelectProgram.vue";
import { useRouter } from "vue-router";
import SelectIndicatorScope from "@/components/selections/SelectIndicatorScope.vue";

const router = useRouter()
var searchKeyword = ref('')

const indicators = ref<Indicator[]>([]);

const modal = ref({
    create:{
        show:false
    }
})


const indicator = ref<{
    program: Program | null;
    indicator_name: string;
    indicator_scope: string;
    indicator_description: string;
    indicator_status: string | number;
    indicator_code: string;
}>({
    program: null,
    indicator_name: '',
    indicator_scope: '',
    indicator_description: '',
    indicator_status: '',
    indicator_code: ''
});


onMounted(() => {
    fetchIndicators()
})

function fetchIndicators(){
    axios.get('/indicator/list')
    .then((response) => {
        indicators.value = response.data.data
        console.log(indicators.value)
    })
    .catch((error) => {
        console.log(error)
    })
    .finally(() => {

    })
}

function saveIndicator(){
    const formattedIndicator = formatIndicator()
    axios.post('/indicator/create',formattedIndicator)
    .then((response) => {
        toast('Action Successfull', {
            description: response.data.message,
            action: {
                label: 'Close',
                onClick: () => toast.dismiss(),
            },
        })
        fetchIndicators()
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
    const formattedIndicator:IndicatorFormatted = {
        program_id:indicator.value.program?.program_id,
        indicator_name:indicator.value.indicator_name,
        indicator_scope:Number(indicator.value.indicator_scope),
        indicator_description:indicator.value.indicator_description,
        indicator_status:Number(indicator.value.indicator_status),
        indicator_code:indicator.value.indicator_code
    }

    return formattedIndicator;
}


interface Indicator {
    program:Program|null
    indicator_id:number
    disaggregations:Disaggregation[]
    indicator_name:string
    indicator_scope:number|string
    indicator_description:string
    indicator_status:number
    indicator_code:string
    indicator_scope_name:string
    indicator_status_name:string
}

interface IndicatorFormatted {
    program_id:number|undefined
    indicator_name:string
    indicator_scope:number
    indicator_description:string
    indicator_status:number
    indicator_code:string
}

interface Program {
    program_name:string
    program_id:number
}

interface Disaggregation {
    disaggregation_name:string
    disaggregation_id:number
}


</script>


<template>
    <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
        <!-- header -->
        <div class="w-full flex justify-between items-center p-2 border">
            <div class="relative items-center">
                <Input v-model="searchKeyword" type="text" placeholder="Search Keyword" class="pl-8"/>
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>

            <Button @click="modal.create.show = true" variant="default" size="sm" class="cursor-pointer"> Create Indicator </Button>
        </div>

        <!-- table -->
        <div class="w-full h-full flex flex-col jusitfy-start items-start border">
            <div class="w-full h-[640px] overflow-y-scroll">
                <Table class="w-full">
                    <TableHeader>
                        <TableRow class="divide-x">
                            <TableHead>Indicator Name</TableHead>
                            <TableHead>Program</TableHead>
                            <TableHead>Indicator Scope</TableHead>
                            <TableHead>Indicator Status</TableHead>
                            <TableHead>Disaggregations</TableHead>
                            <TableHead class="text-left">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="indicator in indicators" class="text-xs divide-x">
                            <TableCell class="max-w-[100px] whitespace-normal break-words">{{indicator.indicator_name }}</TableCell>
                            <TableCell class="max-w-[100px] whitespace-normal break-words">{{indicator.program?.program_name }}</TableCell>
                            <TableCell class="max-w-[100px] whitespace-normal break-words">{{indicator.indicator_scope_name }}</TableCell>
                            <TableCell class="max-w-[100px] whitespace-normal break-words">{{indicator.indicator_status_name }}</TableCell>
                            <TableCell class="max-w-[100px] whitespace-normal break-words">
                                <div class="flex flex-col gap-1">
                                    <span
                                        v-for="disaggregation in indicator.disaggregations"
                                        :key="disaggregation.disaggregation_id"
                                        class="break-all text-xs"
                                    >
                                        {{ disaggregation.disaggregation_name }}
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell class="flex justify-start">
                               <Button @click="router.push({path:`/indicator/update/${indicator.indicator_id}`})" variant="outline" size="sm" class="justify-start text-xs text-center cursor-pointer hover:bg-sky-200">Edit Indicator</Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- footer -->
        <div class="w-full h-[50px] flex justify-center items-center border p-2">
            <span>Pagination Here</span>
        </div>
    </div>

    <Dialog v-model:open="modal.create.show">
        <DialogContent class="font-poppins sm:max-w-[500px] md:max-w-[1000px] lg:max-w-[1000px]">
            <DialogHeader>
                <DialogTitle class=""></DialogTitle>
                <DialogDescription></DialogDescription>
            </DialogHeader>

            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
                <Card class="w-full">
                    <CardHeader>
                        <CardTitle>Create Indicator</CardTitle>
                        <CardDescription>Fill up the form then click Save Indicator. All fields are required.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">

                            <SelectProgram v-model="indicator.program"/>

                            <div class="w-full flex flex-col justify-start items-start gap-4">
                                <Input v-model="indicator.indicator_name" type="text" placeholder="Indicator Name" class=""/>
                                <Input v-model="indicator.indicator_description" type="text" placeholder="Indicator Description" class=""/>
                            </div>

                            <div class="w-full flex flex-col justify-start items-start gap-4">
                                <SelectIndicatorScope v-model="indicator.indicator_scope"/>
                            </div>

                        </div>
                    </CardContent>
                    <CardFooter>
                        <Button @click="saveIndicator" variant="default" class="cursor-pointer" size="sm">Create Indicator</Button>
                    </CardFooter>
                </Card>
            </div>

        </DialogContent>
    </Dialog>
    

</template>