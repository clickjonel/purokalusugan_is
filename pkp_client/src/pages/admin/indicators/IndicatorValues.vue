<script setup lang="ts">
import { computed } from 'vue';
import axios from "@/axios/axios";
import { ref, onMounted } from "vue";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
interface Props {
    eventRecord: Record<string, any>;
    handleCloseIndicatorValue: () => void;
}
const props = defineProps<Props>();
const data = computed(() => {
    if (typeof props.eventRecord === 'object' && props.eventRecord !== null) {
        return { ...props.eventRecord };
    }
    return {};
});
const event = {
    event_id: data.value.event_id,
    event_partner: data.value.event_partner,
    event_actual_budget: data.value.event_value,
    event_budget: data.value.event_budget,
    event_date: data.value.event_date,
    event_fund_source: data.value.event_fund_source,
    event_venue: data.value.event_venue,
    event_proponent: data.value.event_proponent,
    event_scope: data.value.event_scope,
    event_type: data.value.event_type,
    is_pk_site: data.value.is_pk_site
}



const dohCARLogo = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQRBmB7ueFlIGZ__ES7pCGHygeJHQBBlStvHw&s";
const dapayPKCARLogo = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTz5hW3nF0JRnTCPAgInbSO6xd62V2x-8fJZA&s";
const programs = ref<Program[]>([]);
const indicators = ref<Indicator[]>([]);
let  indicatorValueToSave = ref();

interface Program {
    program_id: number;
    program_name: string;
    program_code: any;
    program_status: boolean;
}
interface Indicator {
    indicator_id: number,
    program_id: number,
    indicator_code: any,
    indicator_name: string,
    indicator_description: string,
    indicator_status: boolean,
    indicator_scope: number
}
const fetchPrograms = () => {
    axios
        .get("/program/list")
        .then((response) => {
            programs.value = response.data.data;
            console.log("programs", programs.value);
        })
        .catch((error) => {
            console.error("Error fetching programs:", error);
        });
};
const fetchIndicators = () => {
    axios
        .get("/indicator/list")
        .then((response) => {
            indicators.value = response.data.data;
            console.log("indicators", indicators.value);
        })
        .catch((error) => {
            console.error("Error fetching indicators:", error);
        });
};

onMounted(() => {
    fetchPrograms();
    fetchIndicators();
});

</script>
<template>
    <div class="flex gap-2 justify-center">
        <img :src="dohCARLogo" class="h-[5rem] w-[5rem]" />
        <div class="flex flex-col justify-center items-center">
            <strong>Purok Kalusugan Accomplishment</strong>
            <strong>Information System Interface</strong>
        </div>
        <img :src="dapayPKCARLogo" class="h-[5rem] w-[5rem]" />
    </div>
    <div class="flex flex-col divide-y divide-black outline outline-black">
        <div class="flex divide-black divide-x">
            <div class="flex-1 flex gap-2 items-center">
                <small>Title:</small>
                <strong>{{ }}</strong>
            </div>
            <div class="flex-1 flex gap-2 items-center">
                <small>Type</small>
                <strong>{{ event.event_type }}</strong>
            </div>
        </div>

        <div class="flex divide-black divide-x">
            <div class="flex-1 flex gap-2 items-center">
                <small>Date:</small>
                <strong>{{ event.event_date }}</strong>
            </div>
            <div class="flex-1 flex gap-2 items-center">
                <small>Venue:</small>
                <strong>{{ event.event_venue }}</strong>
            </div>
        </div>
        <div class="flex divide-black divide-x">
            <div class="flex-1 flex gap-2 items-center">
                <small>Budget Allocation:</small>
                <strong>{{ event.event_budget }}</strong>
            </div>
            <div class="flex-1 flex gap-2 items-center">
                <small>Actual Budget:</small>
                <strong>{{ event.event_actual_budget }}</strong>
            </div>
        </div>
        <div class="flex divide-black divide-x">
            <div class="flex-1 flex gap-2 items-center">
                <small>Source of Funding:</small>
                <strong>{{ event.event_fund_source }}</strong>
            </div>
            <div class="flex-1 flex gap-2 items-center">
                <small>Name of Proponent:</small>
                <strong>{{ event.event_proponent }}</strong>
            </div>
        </div>
        <div class="flex divide-black divide-x">
            <div class="flex-1 flex gap-2 items-center">
                <small>Partner Stakeholders:</small>
                <strong>{{ }}</strong>
            </div>
            <div class="flex-1 flex gap-2 items-center">
                <small>Areas Covered:</small>
                <strong>{{ event.event_scope }}</strong>
            </div>
        </div>
        <div class="flex divide-black divide-x">
            <div class="flex-1 flex gap-2 items-center">
                <small>Province/ City:</small>
                <strong>{{ }}</strong>
            </div>
            <div class="flex-1 flex gap-2 items-center">
                <small>Municipality:</small>
                <strong>{{ }}</strong>
            </div>
        </div>
    </div>
    <!-- Entry Fields -->
    <div>
        <!-- loop through the programs -->
        <div v-for="program in programs" :key="program.program_id">
            <strong class="text-lg">{{ program.program_name }}</strong>
            <!-- loop through the indicators based on programs -->
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[60%]">Indicator</TableHead>
                        <TableHead class="w-[10%]">Male</TableHead>
                        <TableHead class="w-[10%]">Female</TableHead>
                        <TableHead class="w-[10%]">Not Indicated</TableHead>
                        <TableHead class="w-[10%]">Total</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="indicator in indicators.filter(ind => ind.program_id === program.program_id)"
                        :key="indicator.indicator_id">
                        <TableCell>{{ indicator.indicator_name }}</TableCell>
                        <TableCell><Input type="number" v-model="indicator.indicator_id"/></TableCell><!--Count of Males-->
                        <TableCell><Input type="number" /></TableCell><!--Count of Females-->
                        <TableCell><Input type="number" /></TableCell><!--Count of Not Indicated-->
                        <TableCell><Input type="number" /></TableCell><!--Total of Male, femal and not indicated-->
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <div class="flex gap-1 justify-end">
            <Button type="submit" class="cursor-pointer bg-red-500 hover:bg-red-300 hover:text-black"@click="props.handleCloseIndicatorValue()">Close</Button>
            <Button type="submit" class="cursor-pointer bg-emerald-500 hover:bg-emerald-300 hover:text-black"@click="">Submit</Button>
        </div>
    </div>
</template>