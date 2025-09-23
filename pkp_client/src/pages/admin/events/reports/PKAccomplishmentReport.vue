<script setup lang="ts">
import DOHLogo from '@/lib/imgs/doh_logo.png';
import DapayLogo from '@/lib/imgs/dapay_logo.png';
import { onMounted, ref } from "vue";
import { toast } from 'vue-sonner';
import { useRouter, useRoute } from "vue-router";
import axios from '@/axios/axios';

const router = useRouter();
const route = useRoute();
interface EventData {
    event: {
        event_name: string;
        event_type: number;
        event_actual_budget: number;
        event_budget: number;
        event_type_name: string;
        event_venue: string;
        event_fund_source: string;
        event_id: number;
        is_populated: number;
        event_date_start: string;
        event_date_end: string;
        event_proponents: string;
        event_partners: string;
        barangays: any[];
        programs: any[]
    };
}
interface EventValueItem {
    barangay: object,
    barangay_id: number,
    event_id: number,
    indicator_disaggregation:any,
    indicator_disaggregation_id:number,
    indicator_value_id:number,
    remarks:string,
    value: number

}
const eventData = ref<EventData | null>(null);
const printRef = ref<HTMLElement | null>(null);
const eventValues = ref<EventValueItem[]>([]);
let indicatorCollectedDisaggregationValues: number[] = [];

onMounted(() => {
    if (history.state.eventData) {
        eventData.value = history.state.eventData;
        console.log("Event data received:", eventData.value);
    } else {
        const eventId = route.params.id;
        if (eventId) {
            axios.get(`/event/fetch?event_id=${eventId}`)
                .then((response) => {
                    eventData.value = response.data;
                    eventValues.value = response.data.event.values;
                    console.log('eventData', eventData.value)
                })
                .catch((error) => {
                    console.error("Failed to fetch event data:", error);
                    toast.error("Failed to load event data.");
                });
        }
    }
});
const printPage = (): void => {
    document.body.classList.add('print-root');
    const removeClass = () => {
        document.body.classList.remove('print-root');
        window.removeEventListener('afterprint', removeClass);
    };
    window.addEventListener('afterprint', removeClass);
    window.print();
}

function getIndicatorDisaggregationValue(event_id: any, barangay_id: number, disaggregation_id: number) {
    console.log('list of values per array', eventValues)
    const result = eventValues.value.filter(item => {
        if(item.event_id === event_id && 
            item.barangay_id === barangay_id &&
            item.indicator_disaggregation_id === disaggregation_id
        ){
            return item.value;
        }  
    })
    return result[0].value;
    
}

function getSumOfValuesForCurrentIndicatorShown(list:number[]){    
    const sum = list.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
    return sum;
}
</script>
<template>
    <section ref="printRef" class="print-section">
        <!-- Header -->
        <section class="w-[100%] flex flex-col border">
            <div class="w-[100%] flex justify-center gap-1">
                <img :src="DOHLogo" class="h-[5rem] w-[5rem]" />
                <div class="flex flex-col justify-center items-center">
                    <strong>Purok Kalusugan Accomplishment</strong>
                    <strong>Information System Interface</strong>
                </div>
                <img :src="DapayLogo" class="h-[5rem] w-[5rem]" />
            </div>
            <div class="w-[100%] flex flex-col">
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">TITLE OF ACTIVITY</strong>
                    <strong>{{ eventData?.event?.event_name }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">TYPE OF ACTIVITY</strong>
                    <strong>{{ eventData?.event?.event_type }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">DATE OF ACTIVITY</strong>
                    <strong>{{ eventData?.event?.event_date_start }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">VENUE OF ACTIVITY</strong>
                    <strong>{{ eventData?.event?.event_date_end }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">BUDGET OF ACTIVITY</strong>
                    <strong>{{ eventData?.event?.event_budget }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">ACTUAL BUDGET UTILIZED</strong>
                    <strong>{{ eventData?.event?.event_actual_budget }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">SOURCE OF FUNDING</strong>
                    <strong>{{ eventData?.event?.event_fund_source }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">NAME OF PROPONENT</strong>
                    <strong>{{ eventData?.event?.event_proponents }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">PARTNER STAKEHOLDERS</strong>
                    <strong>{{ eventData?.event?.event_partners }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">AREAS COVERED</strong>
                    <strong>{{ eventData?.event?.event_name }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">PROVINCE/CITY</strong>
                    <strong>{{ eventData?.event?.event_name }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">MUNICIPALITY</strong>
                    <strong>{{ eventData?.event?.event_name }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100">BARANGAYS</strong>
                    <strong v-for="(barangay, index) in eventData?.event?.barangays" :key="index">
                        <small class="p-1 bg-green-700 rounded text-white">
                            {{ barangay.barangay_name }}
                        </small>
                    </strong>
                </div>
            </div>
        </section>
        <!-- Indicator with Values -->
        <section class="w-[100%] border">
            <div class="w-[100%] mt-2" v-for="(barangay, index) in eventData?.event?.barangays" :key='index'>
                <strong class="p-1 bg-green-700 text-white">
                    {{ barangay.barangay_name }}</strong>
                <table class="w-[100%] border">
                    <thead>
                        <tr>
                            <th class='border-b border-r'>Program</th>
                            <th class='border-b border-r'>Indicators</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(program, progam_index) in eventData?.event?.programs" :key="progam_index">
                            <td class='border-b border-r'>{{ program.program_name }}</td>
                            <td class='border-b border-r'>
                                <div class='flex flex-col'>
                                    <div v-for="(indicator, indicator_index) in program.indicators"
                                        :key="indicator_index">
                                        <div>
                                            <span class='border-b'>{{ indicator.indicator_name}}</span>
                                            <div class='ml-2'
                                                v-for="(disaggregation, disaggregation_index) in indicator.disaggregations"
                                                :key="disaggregation_index">{{ disaggregation.disaggregation_name
                                                }}
                                                <span class='text-blue-800 text-lg underline'>{{ getIndicatorDisaggregationValue(eventData?.event.event_id,
                                                    barangay.barangay_id,
                                                    disaggregation.disaggregation_id) }}</span>
                                                <span hidden>
                                                {{ indicatorCollectedDisaggregationValues.push(
                                                    getIndicatorDisaggregationValue(eventData?.event.event_id,
                                                    barangay.barangay_id,
                                                    disaggregation.disaggregation_id)
                                                ) }}
                                                </span>    
                                            </div>                                           
                                            <strong>
                                                Sum: {{getSumOfValuesForCurrentIndicatorShown(
                                                    indicatorCollectedDisaggregationValues.splice(0, 
                                                    indicatorCollectedDisaggregationValues.length))}}
                                            </strong>
                                        </div>
                                    </div>

                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </section>

    <button @click="printPage" class="px-4 py-2 bg-green-700 text-white rounded m-2">
        Print
    </button>
</template>

<style>
/* Layout for the "report" rows (desktop & printed) */
.report-row {
    display: flex;
    align-items: center;
    border-bottom: 1px solid #e5e7eb;
    margin: 0;
    padding: 8px 0;
}

.label {
    width: 30%;
    padding: 8px;
    background: #047857;
    /* green */
    color: white;
    font-weight: 700;
}

.value {
    width: 70%;
    padding: 8px;
    font-weight: 600;
}

/* small tag for barangays */
.tag {
    display: inline-block;
    margin-right: 6px;
    padding: 4px 8px;
    background: #1f2937;
    color: #fff;
    border-radius: 6px;
    font-size: 0.85rem;
}

/* Hide the print button in printed output */
.no-print {
    display: inline-block;
}

/* ---------- PRINT-SPECIFIC ---------- */
/* We scope the print rules to when body has class print-root,
   so normal prints from other pages or prints stay unaffected.  */
@media print {

    /* force browsers to try and keep background colors */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* hide everything first */
    body.print-root * {
        visibility: hidden !important;
    }

    /* show only the print section and its children */
    body.print-root .print-section,
    body.print-root .print-section * {
        visibility: visible !important;
    }

    /* make the print-section appear at the top-left of the printed page */
    body.print-root .print-section {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    /* hide elements marked 'no-print' */
    body.print-root .no-print {
        display: none !important;
    }
}
</style>