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

const eventData = ref<EventData | null>(null);
const printRef = ref<HTMLElement | null>(null);
const printSection = ref(null);
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
                        <small class="p-1 bg-blue-500 rounded text-white">
                            {{ barangay.barangay_name }}
                        </small>
                    </strong>
                </div>
            </div>
        </section>
        <!-- Indicator with Values -->
        <section class="w-[100%] border">
            <div class="w-[100%]">
                <span>Barangay</span>
                <small class="p-1 bg-blue-500 rounded text-white">barangay</small>
            </div>
            <table class="w-[100%] border">
                <thead>
                    <tr>
                        <th>Program</th>
                        <th>Indicators</th>
                        <th>Disaggregations</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(program, pindex) in eventData?.event?.programs" :key="pindex">
                        <td>{{ program.program_name }}</td>
                        <td>
                            <span v-for="(indicator, iindex) in program.indicators" :key="iindex">
                                <small>{{ indicator.indicator_name }}</small>
                            </span>
                            
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </section>

    <button @click="printPage" class="px-4 py-2 bg-blue-600 text-white rounded m-2">
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