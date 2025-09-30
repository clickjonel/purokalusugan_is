<script setup lang="ts">
import DOHLogo from '@/lib/imgs/doh_logo.png';
import DapayLogo from '@/lib/imgs/dapay_logo.png';
import { onMounted, ref, computed } from "vue";
import { toast } from 'vue-sonner';
import { useRoute } from "vue-router";
import axios from '@/axios/axios';
import { useAuthStore } from '@/store/authStore';

const route = useRoute();
const authStore = useAuthStore();

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
        barangays: BarangayItem[];
        programs: ProgramObject[];
        values: EventValueItem[]
    };
}

interface ProgramObject {
    indicators: IndicatorObject[],
    pivot: any[],
    program_code: string,
    program_id: number,
    program_name: string,
    program_status: number
}

interface IndicatorObject {
    disaggregations: DisaggregationObject[],
    indicator_code: string,
    indicator_description: string,
    indicator_id: number,
    indicator_name: string,
    indicator_scope: number,
    indicator_scope_name: string,
    indicator_status: number,
    indicator_status_name: string,
    program_id: number
}

interface DisaggregationObject {
    disaggregation_code: string,
    disaggregation_id: number,
    disaggregation_name: string,
    totalable: number
}
interface EventValueItem {
    barangay: BarangayItem,
    barangay_id: number,
    event_id: number,
    indicator_disaggregation: any,
    indicator_disaggregation_id: number,
    indicator_value_id: number,
    remarks: string,
    value: number
}

interface BarangayItem {
    barangay_id: number,
    barangay_name: string,
    municipality_id: number,
    province_id: number,
    region_id: number,
    uid: number
}

interface IndividualTotalObject {
    barangay_name: string,
    individual_total: number,
    total_males: number,
    total_females: number,
    total_not_indicated: number,
    total_4Ps: number,
    total_household: number
}

interface MunicipalityObject {
    municipality_id: number,
    municipality_name: string,
    province_id: number,
    region_id: number
}
interface ProvinceObject {
    province_id: number,
    province_name: string
}
interface EventResourcesObject{
    event_resources_id: number,
    name:string,
    type:number,
    beneficiary_count: number,
    amount:number
}
const eventData = ref<EventData | null>(null);
const printRef = ref<HTMLElement | null>(null);
const eventValues = ref<EventValueItem[]>([]);
const municipality = ref<MunicipalityObject | null>(null);
const province = ref<ProvinceObject | null>(null);
const event_resources = ref<EventResourcesObject[]>([]);

const printPage = (): void => {
    document.body.classList.add('print-root');
    const removeClass = () => {
        document.body.classList.remove('print-root');
        window.removeEventListener('afterprint', removeClass);
    };
    window.addEventListener('afterprint', removeClass);
    window.print();
}

function displayTotalOfDisaggregationValues(indicator: any) {
    let result = indicator.males + indicator.females + indicator.not_indicated + indicator.fourPs + indicator.household;
    return result;
}
function generateIndicatorData(indicators: IndicatorObject[], barangay_id: number) {
    //step 1: prepare result
    let result = [];
    //step 2: loop through all the indicators in this event, (this already involves which barangay the indicators are in)
    for (let i = 0; i < indicators.length; i++) {
        //step 3: prepare comparing variables
        const indicator_id = indicators[i].indicator_id;
        const indicator_name = indicators[i].indicator_name;
        //step 4: get values for the disaggregations (note: should only have one record in each array each)     
        const males = eventValues.value.filter(item => {
            if (item.barangay.barangay_id === barangay_id &&
                item.indicator_disaggregation.indicator_id === indicator_id &&
                item.indicator_disaggregation.disaggregation_id === 2
            ) {
                return item;
            }
        })
        const females = eventValues.value.filter(item => {
            if (item.barangay.barangay_id === barangay_id &&
                item.indicator_disaggregation.indicator_id === indicator_id &&
                item.indicator_disaggregation.disaggregation_id === 3
            ) {
                return item;
            }
        })
        const not_indicated = eventValues.value.filter(item => {
            if (item.barangay.barangay_id === barangay_id &&
                item.indicator_disaggregation.indicator_id === indicator_id &&
                item.indicator_disaggregation.disaggregation_id === 4
            ) {
                return item;
            }
        })
        const fourPs = eventValues.value.filter(item => {
            if (item.barangay.barangay_id === barangay_id &&
                item.indicator_disaggregation.indicator_id === indicator_id &&
                item.indicator_disaggregation.disaggregation_id === 5
            ) {
                return item;
            }
        })
        const household = eventValues.value.filter(item => {
            if (item.barangay.barangay_id === barangay_id &&
                item.indicator_disaggregation.indicator_id === indicator_id &&
                item.indicator_disaggregation.disaggregation_id === 6
            ) {
                return item;
            }
        })
        //step 5: push the results
        result.push({
            indicator_name: indicator_name,
            males: males.length > 0 ? males[0].value : 0,
            females: females.length > 0 ? females[0].value : 0,
            not_indicated: not_indicated.length > 0 ? not_indicated[0].value : 0,
            fourPs: fourPs.length > 0 ? fourPs[0].value : 0,
            household: household.length > 0 ? household[0].value : 0
        });
    }

    return result;
}
function generateIndividualServedPerPKSiteData(event_values: EventValueItem[]) {
    const barangay_ids = event_values.map(item => item.barangay.barangay_id);
    const unique_barangay_ids = [...new Set(barangay_ids)];
    const individualsPerBarangay = unique_barangay_ids.map(id =>
        event_values.filter(item => item.barangay.barangay_id == id)
    );
    let barangay_name = "";
    let result = [];
    for (let i = 0; i < individualsPerBarangay.length; i++) {
        let total_individuals = 0;
        let total_males = 0;
        let total_females = 0;
        let total_not_indicated = 0;
        let total_4Ps = 0;
        let total_household = 0;
        for (let j = 0; j < individualsPerBarangay[i].length; j++) {
            barangay_name = individualsPerBarangay[i][j].barangay.barangay_name;
            total_individuals += individualsPerBarangay[i][j].value;
            if (individualsPerBarangay[i][j].indicator_disaggregation.disaggregation_id == 2) {
                total_males += individualsPerBarangay[i][j].value;
            }
            if (individualsPerBarangay[i][j].indicator_disaggregation.disaggregation_id == 3) {
                total_females += individualsPerBarangay[i][j].value;
            }
            if (individualsPerBarangay[i][j].indicator_disaggregation.disaggregation_id == 4) {
                total_not_indicated += individualsPerBarangay[i][j].value;
            }
            if (individualsPerBarangay[i][j].indicator_disaggregation.disaggregation_id == 5) {
                total_4Ps += individualsPerBarangay[i][j].value;
            }
            if (individualsPerBarangay[i][j].indicator_disaggregation.disaggregation_id == 6) {
                total_household += individualsPerBarangay[i][j].value;
            }

        }
        result.push({
            barangay_name: barangay_name,
            individual_total: total_individuals,
            total_males: total_males,
            total_females: total_females,
            total_not_indicated: total_not_indicated,
            total_4Ps: total_4Ps,
            total_household: total_household
        })
    }
    return result;
}

function getOverAllTotalForIndividualServed(totals: IndividualTotalObject[]) {
    const result = totals.reduce((accumulator, current) => accumulator + current.individual_total, 0);
    return result;
}
function getOverAllTotalForMales(totals: IndividualTotalObject[]) {
    const result = totals.reduce((accumulator, current) => accumulator + current.total_males, 0);
    return result;
}
function getOverAllTotalForFemales(totals: IndividualTotalObject[]) {
    const result = totals.reduce((accumulator, current) => accumulator + current.total_females, 0);
    return result;
}
function getOverAllTotalForNotIndicated(totals: IndividualTotalObject[]) {
    const result = totals.reduce((accumulator, current) => accumulator + current.total_not_indicated, 0);
    return result;
}
function getOverAllTotalFor4Ps(totals: IndividualTotalObject[]) {
    const result = totals.reduce((accumulator, current) => accumulator + current.total_4Ps, 0);
    return result;
}
function getOverAllTotalForHousehold(totals: IndividualTotalObject[]) {
    const result = totals.reduce((accumulator, current) => accumulator + current.total_household, 0);
    return result;
}

function displayCurrentUserLoggedIn() {
    const first_name = authStore.user?.first_name;
    const middle_initial = authStore.user?.middle_name.substring(0, 1);
    const last_name = authStore.user?.last_name;
    const full_name = `${first_name} ${middle_initial}. ${last_name}`;
    return full_name;
}
function displayCurrentDate() {
    const current_date = new Date();
    return current_date;
}

function displayEventType(event_type: any) {
    if (event_type === 1) {
        return "Small Scale";
    }
    if (event_type === 2) {
        return "Large Scale";
    }
}

function getMunicipality(municipalityId: number) {    
    axios.get(`/municipality/find`, {
        params: {
            municipality_id: municipalityId
        }
    })
        .then((response) => {
            municipality.value = response.data.data;
            console.log("municipality here", municipality.value)
        })
        .catch((error) => {
            console.error("Error fetching municipality", error);
        })
}

function getProvince(provinceId: number) {
    axios.get(`/province/find`, {
        params: {
            province_id: provinceId
        }
    })
        .then((response) => {
            province.value = response.data.data;
            console.log("province here", province.value)
        })
        .catch((error) => {
            console.error("Error fetching province", error);
        })
}

function getEventResources(event_id:number){
    axios.get(`/event/resources/find`, {
        params: {
            event_id: event_id
        }
    })
        .then((response) => {
            event_resources.value = response.data.data;
            console.log("event resources here", event_resources.value)
        })
        .catch((error) => {
            console.error("Error fetching data", error);
        })
}

function displayEventResourceType(type:number){
    let result = "";
    switch(type){
        case 1: result = "Drug";break;
        case 2: result = "Logistic";break;
        default: break;
    }
    return result;
}
const individualsServedData = computed(() => {
    return generateIndividualServedPerPKSiteData(eventValues.value);
});

onMounted(() => {
    if (history.state.eventData) {
        eventData.value = history.state.eventData;
        console.log("Event data received:", eventData.value);
        if (eventData.value) {
            getMunicipality(eventData.value.event.barangays[0].municipality_id);
            getProvince(eventData.value.event.barangays[0].province_id);
        }
    } else {
        const eventId = route.params.id;
        if (eventId) {
            axios.get(`/event/fetch?event_id=${eventId}`)
                .then((response) => {
                    eventData.value = response.data;
                    eventValues.value = response.data.event.values;
                    console.log('eventData', eventData.value)
                    if (eventData.value) {
                        getMunicipality(eventData.value.event.barangays[0].municipality_id);
                        getProvince(eventData.value.event.barangays[0].province_id);
                        getEventResources(eventData.value.event.event_id);
                    }
                })
                .catch((error) => {
                    console.error("Failed to fetch event data:", error);
                    toast.error("Failed to load event data.");
                });
        }
    }
});



</script>
<template>
    <section ref="printRef" class="print-section font-poppins">
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
            
            <div class="mt-5 w-[100%] flex flex-col text-sm">
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Title</strong>
                    <strong class="text-sm">{{ eventData?.event?.event_name }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Type of Activity</strong>
                    <strong class="text-sm">{{ displayEventType(eventData?.event?.event_type) }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Date</strong>
                    <strong class="text-sm">{{ eventData?.event?.event_date_start }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Venue</strong>
                    <strong class="text-sm">{{ eventData?.event?.event_date_end }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Budget</strong>
                    <strong class="text-sm">{{ eventData?.event?.event_budget }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Actual Budget Utilized</strong>
                    <strong class="text-sm">{{ eventData?.event?.event_actual_budget }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Fund Source</strong>
                    <strong class="text-sm">{{ eventData?.event?.event_fund_source }}</strong>
                </div>
            </div>
            <div class="w-[100%] flex flex-col">
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Proponents</strong>
                    <strong class="text-sm">{{ eventData?.event?.event_proponents }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Partner Stakeholders</strong>
                    <strong class="text-sm">{{ eventData?.event?.event_partners }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Province</strong>
                    <strong class="text-sm">{{ province?.province_name }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Municipality/City</strong>
                    <strong class="text-sm">{{ municipality?.municipality_name }}</strong>
                </div>
                <div class="w-[100%] flex gap-1 items-center border-b">
                    <strong class="w-[30%] p-1 bg-green-700 text-slate-100 text-xs">Areas / Barangays Covered</strong>
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
            <h2 class="text-center text-sm text-white bg-slate-500"><strong>Details</strong></h2>
            <div class="w-[100%] mt-2" v-for="(barangay, barangay_index) in eventData?.event?.barangays"
                :key='barangay_index'>
                <div class="bg-green-700 text-white text-center text-xs">
                    <strong >
                        {{ barangay.barangay_name }}</strong>
                </div>

                <div class="hover:border-black" v-for="(program, progam_index) in eventData?.event?.programs"
                    :key="progam_index">
                    <div class="w-full bg-purple-500 text-white text-center text-xs">
                        <strong>
                            {{ program.program_name }}</strong>
                    </div>
                    <table class='w-full border'>
                        <thead>
                            <tr>
                                <th class='border-r border-b p-2 text-xs'>Indicator</th>
                                <th class='border-r border-b p-2 text-xs'>Male</th>
                                <th class='border-r border-b p-2 text-xs'>Female</th>
                                <th class='border-r border-b p-2 text-xs'>Not Indicated</th>
                                <th class='border-r border-b p-2 text-xs'>4Ps</th>
                                <th class='border-r border-b p-2 text-xs'>Household</th>
                                <th class='border-r border-b p-2 text-xs'>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-slate-100" v-for="(indicator, indicator_index) in
                                generateIndicatorData(program.indicators, barangay.barangay_id)"
                                :key="indicator_index">
                                <td class='border-r border-b p-2 text-xs'>{{ indicator.indicator_name }}</td>
                                <td class='border-r border-b p-2 text-center text-xs'>{{ indicator.males }}</td>
                                <td class='border-r border-b p-2 text-center text-xs'>{{ indicator.females }}</td>
                                <td class='border-r border-b p-2 text-center text-xs'>{{ indicator.not_indicated }}</td>
                                <td class='border-r border-b p-2 text-center text-xs'>{{ indicator.fourPs }}</td>
                                <td class='border-r border-b p-2 text-center text-xs'>{{ indicator.household }}</td>
                                <td class='border-r border-b p-2 text-center text-xs'><strong>{{
                                    displayTotalOfDisaggregationValues(indicator) }}</strong></td>
                            </tr>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>

        <!-- Individuals Served Summary -->
        <section class="mt-5 w-full">
            <h2 class="text-center text-sm text-white bg-slate-500"><strong>Number of individuals served per Barangay/PuroKalusugan
                    Sites:</strong></h2>
            <table class="w-full border">
                <thead>
                    <tr>
                        <th class='border-b border-r p-2 text-xs'>Barangay/Purok Kalugusan Site</th>
                        <th class='border-b border-r p-2 text-xs'>Total Number of Individuals Served</th>
                        <th class='border-b border-r p-2 text-xs'>Male</th>
                        <th class='border-b border-r p-2 text-xs'>Female</th>
                        <th class='border-b border-r p-2 text-xs'>Not Indicated</th>
                        <th class='border-b border-r p-2 text-xs'>4Ps</th>
                        <th class='border-b border-r p-2 text-xs'>Household</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in individualsServedData" :key="index">
                        <td class='border-b border-r p-2 text-xs'>{{ item.barangay_name }}</td>
                        <td class='border-b border-r p-2 text-center text-xs'>{{ item.individual_total }}</td>
                        <td class='border-b border-r p-2 text-center text-xs'>{{ item.total_males }}</td>
                        <td class='border-b border-r p-2 text-center text-xs'>{{ item.total_females }}</td>
                        <td class='border-b border-r p-2 text-center text-xs'>{{ item.total_not_indicated }}</td>
                        <td class='border-b border-r p-2 text-center text-xs'>{{ item.total_4Ps }}</td>
                        <td class='border-b border-r p-2 text-center text-xs'>{{ item.total_household }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class='border-b border-r p-2'>
                            <strong>Total</strong>
                        </td>
                        <td class='border-b border-r p-2 text-center'>
                            <strong>{{ getOverAllTotalForIndividualServed(individualsServedData) }}</strong>
                        </td>
                        <td class='border-b border-r p-2 text-center'>
                            <strong>{{ getOverAllTotalForMales(individualsServedData) }}</strong>
                        </td>
                        <td class='border-b border-r p-2 text-center'>
                            <strong>{{ getOverAllTotalForFemales(individualsServedData) }}</strong>
                        </td>
                        <td class='border-b border-r p-2 text-center'>
                            <strong>{{ getOverAllTotalForNotIndicated(individualsServedData) }}</strong>
                        </td>
                        <td class='border-b border-r p-2 text-center'>
                            <strong>{{ getOverAllTotalFor4Ps(individualsServedData) }}</strong>
                        </td>
                        <td class='border-b border-r p-2 text-center'>
                            <strong>{{ getOverAllTotalForHousehold(individualsServedData) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </section>

        <!-- Logistics or Drugs Dispensed -->
        <section class="mt-5 w-full border">
            <h2 class="text-center text-sm text-white bg-slate-500"><strong>Logistics or Drugs Dispensed</strong></h2>
            <table class="w-full">
                <thead>
                    <tr>
                        <th class='border-b border-r text-xs'>Item</th>
                        <th class='border-b border-r text-xs'>Type</th>
                        <th class='border-b border-r text-xs'>Beneficiary Count</th>
                        <th class='border-b border-r text-xs'>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in event_resources" :key="index">
                        <td class='border-b border-r p-2 text-xs'>{{ item.name }}</td>
                        <td class='border-b border-r p-2 text-center text-xs'>{{ displayEventResourceType(item.type) }}</td>
                        <td class='border-b border-r p-2 text-center text-xs'>{{ item.beneficiary_count }}</td>
                        <td class='border-b border-r p-2 text-center text-xs'>{{ item.amount }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Prepared and Reviewed by -->
        <section class="w-full">
            <div class="w-full flex justify-evenly">
                <div class="flex flex-col items-center">
                    <span>Prepared By:</span>
                    <strong class="mt-5 text-lg">{{ displayCurrentUserLoggedIn() }}</strong>
                    <span>Project Lead</span>
                </div>
                <div class="flex flex-col items-center">
                    <span>Reviewed By:</span>
                    <strong class="mt-10"></strong>
                    <span>MHO/PHO/DMO/Program Manager</span>
                </div>
            </div>
        </section>
        <!-- Footer -->
        <section class="mt-5">
            <div class="flex flex-col items-center">
                <span class="italic text-green-800">Note: Please furnish DOH-CAR a copy of this report through
                    C/PDOHO</span>
                <small class="italic text-slate-800">This is a system Generated Report. Acquired under the user
                    {{ displayCurrentUserLoggedIn() }} on {{ displayCurrentDate() }}</small>
            </div>
        </section>
    </section>

    <section class="w-full flex justify-center">
        <button @click="printPage" class="w-1/4 px-4 py-2 bg-green-700 text-white rounded m-2">
            Print
        </button>
    </section>
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