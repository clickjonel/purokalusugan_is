<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { onMounted, ref } from "vue";
import axios from '@/axios/axios';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from 'vue-sonner';
import { format } from 'date-fns';
import { useRouter, useRoute } from "vue-router";

// TypeScript Interfaces
interface EventDisaggregation {
    disaggregation_id: number;
    disaggregation_code: string;
    disaggregation_name: string;
    pivot: {
        indicator_disaggregation_id: number;
    };
}

interface EventIndicator {
    indicator_id: number;
    indicator_name: string;
    disaggregations: EventDisaggregation[];
}

interface EventProgram {
    program_id: number;
    program_name: string;
    indicators: EventIndicator[];
}

interface EventBarangay {
    barangay_id: number;
    barangay_name: string;
}

interface Event {
    event_id: number;
    event_name: string;
    event_venue: string;
    event_budget: number;
    event_actual_budget: number;
    event_proponent_array: string[];
    event_partner_array: string[];
    barangays: EventBarangay[];
    programs: EventProgram[];
}

// Form Data Interfaces
interface FormDisaggregation {
    disaggregation_id: number;
    disaggregation_code: string;
    disaggregation_name: string;
    value: number | undefined;
    remarks: string;
    indicator_disaggregation_id: number;
}

interface FormIndicator {
    indicator_id: number;
    indicator_name: string;
    disaggregations: FormDisaggregation[];
}

interface FormProgram {
    program_id: number;
    program_name: string;
    indicators: FormIndicator[];
}

interface FormBarangay {
    barangay_id: number;
    barangay_name: string;
    programs: FormProgram[];
}

interface EventFormData {
    event_id: number | null;
    barangays: FormBarangay[];
}

// API Response Types
interface FetchEventResponse {
    event: Event;
}

interface SaveEventResponse {
    success: boolean;
    message: string;
}

interface EventResources {
    name: string;
    type: string;
    beneficiary_count: number;
    amount: number;
    event_id: number;
}

const router = useRouter();
const route = useRoute();
const event = ref<Event | null>(null);
const elements = ref<EventResources[]>([]);
const formData = ref<EventFormData>({
    event_id: null,
    barangays: []
});

onMounted(() => {
    fetchEvent();
});

async function fetchEvent(): Promise<void> {
    try {
        const response = await axios.get<FetchEventResponse>('/event/fetch', {
            params: {
                event_id: route.params.id
            }
        });

        event.value = response.data.event;
        setFormData();
    } catch (error) {
        console.error('Error fetching event:', error);
        toast.error('Failed to load event data');
    }
}

function setFormData(): void {
    if (!event.value) return;

    formData.value = {
        event_id: event.value.event_id,
        barangays: []
    };
    setFormDataBarangays();
    // console.log(formData.value)
}

function setFormDataBarangays(): void {
    if (!event.value) return;

    const barangays: FormBarangay[] = [];

    event.value.barangays.forEach(barangay => {
        const barangayObj: FormBarangay = {
            barangay_id: barangay.barangay_id,
            barangay_name: barangay.barangay_name,
            programs: []
        };

        event.value!.programs.forEach(program => {
            const programObj: FormProgram = {
                program_id: program.program_id,
                program_name: program.program_name,
                indicators: []
            };

            program.indicators.forEach(indicator => {
                const indicatorObj: FormIndicator = {
                    indicator_id: indicator.indicator_id,
                    indicator_name: indicator.indicator_name,
                    disaggregations: []
                };

                indicator.disaggregations.forEach(disaggregation => {

                    indicatorObj.disaggregations.push({
                        disaggregation_id: disaggregation.disaggregation_id,
                        disaggregation_code: disaggregation.disaggregation_code,
                        disaggregation_name: disaggregation.disaggregation_name,
                        value: undefined,
                        remarks: "",
                        indicator_disaggregation_id: disaggregation.pivot.indicator_disaggregation_id
                    });

                });
                programObj.indicators.push(indicatorObj);
            });

            barangayObj.programs.push(programObj);
        });

        barangays.push(barangayObj);
    });

    formData.value.barangays = barangays;
    // console.log(formData.value)
}

const createElement = () => {
    // Add a new object conforming to the EventResources interface
    elements.value.push({
        name: '',
        type: '0', // Default value is '-Select-'
        beneficiary_count: 0,
        amount: 0,
        event_id: event.value!.event_id
    });
};

async function saveData() {
    console.log(formData.value);
    const countOfItemsWithoutType = elements.value.filter(item => item.type == '0');

    if (countOfItemsWithoutType.length > 0) {
        toast("Program created successfully!", {
            description: `${countOfItemsWithoutType.length} item(s) are missing its type!. Make sure to put the type of event resource before saving!`,
            action: {
                label: "Close",
                onClick: () => toast.dismiss(),
            },
        });
    } else {
        saveElements();
        axios.post('/event/populate', formData.value)
            .then((response) => {
                toast('Action Successfull', {
                    description: response.data.message,
                    action: {
                        label: 'Close',
                        onClick: () => toast.dismiss(),
                    },
                })
                router.push({ path: '/admin/events' })

            })
            .catch((error) => {
                console.log(error)
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

}

const saveElements = () => {
    const dataToSave: EventResources[] = elements.value.filter(item => item.type !== '0');
    console.log('API Payload (Typed Array):', dataToSave);
    console.log('dataToSave length', dataToSave)
    for (let i = 0; i < dataToSave.length; i++) {
        axios.post('/event/resources/create', dataToSave[i])
            .then((response) => {
                toast('Action Successfull', {
                    description: `${response.data.message}. Event resources recorded`,
                    action: {
                        label: 'Close',
                        onClick: () => toast.dismiss(),
                    },
                })
                // router.push({ path: '/admin/events' })
            })
            .catch((error) => {
                console.log(error)
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

};

</script>

<template>
    <div class="w-full flex justify-center items-center">
        <div class="w-full p-4 flex justify-center items-center">
            <Card class="w-full">
                <CardHeader>
                    <CardTitle>Event Population Form</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="w-full flex flex-col justify-start items-start gap-4 font-poppins">

                        <!-- Event details -->
                        <Card v-if="event" class="w-full">
                            <CardHeader>
                                <CardTitle>{{ event.event_name }}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="w-full flex flex-col justify-start items-start gap-4 font-poppins">
                                    <div class="w-full flex flex-col justify-start items-start gap-2 text-sm">
                                        <div class="w-full flex justify-start items-center gap-4">
                                            <span class="font-medium">Venue: </span>
                                            <span class="font-light">{{ event.event_venue }}</span>
                                        </div>
                                        <div class="w-full flex justify-start items-center gap-4">
                                            <span class="font-medium">Budget: </span>
                                            <span class="font-light">{{ event.event_budget }}</span>
                                        </div>
                                        <div class="w-full flex justify-start items-center gap-4">
                                            <span class="font-medium">Actual Budget: </span>
                                            <span class="font-light">{{ event.event_actual_budget }}</span>
                                        </div>
                                        <div class="w-full flex justify-start items-center gap-4">
                                            <div class="w-1/2 flex justify-start items-start gap-2">
                                                <span class="w-1/4 font-medium">Proponents:</span>
                                                <div
                                                    class="w-3/4 flex flex-col justify-start items-start gap-2 font-light">
                                                    <span v-for="proponent in event.event_proponent_array">{{ proponent
                                                        }}</span>
                                                </div>
                                            </div>
                                            <div class="w-1/2 flex justify-start items-start gap-2">
                                                <span class="w-1/4 font-medium">Partners:</span>
                                                <div
                                                    class="w-3/4 flex flex-col justify-start items-start gap-2 font-light">
                                                    <span v-for="partner in event.event_partner_array">{{ partner
                                                        }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Event barangays with programs -->
                        <Card v-if="formData.barangays.length > 0" v-for="barangay in formData.barangays"
                            :key="barangay.barangay_id" class="w-full">
                            <CardHeader>
                                <CardTitle>{{ barangay.barangay_name }}</CardTitle>
                                <CardDescription>Fill up the program indicators according to program services served.
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div
                                    class="w-full flex flex-col justify-start items-start font-poppins border border-black divide-y divide-black">
                                    <div class="w-full flex justify-start items-center divide-x divide-black">
                                        <span class="w-1/4 p-2 font-medium uppercase text-left text-sm">Program</span>
                                        <span
                                            class="w-3/4 p-2 font-medium uppercase text-center text-sm">Indicators</span>
                                    </div>
                                    <div v-for="program in barangay.programs" :key="program.program_id"
                                        class="w-full flex justify-start items-stretch divide-x divide-black text-light text-sm">
                                        <span class="w-1/4 p-2">{{ program.program_name }}</span>
                                        <div
                                            class="w-3/4 flex flex-col justify-start items-stretch divide-y divide-black">
                                            <div v-for="indicator in program.indicators" :key="indicator.indicator_id"
                                                class="w-full flex flex-col justify-start items-start gap-2 p-2">
                                                <span class="text-xs">{{ indicator.indicator_name }}</span>
                                                <div v-if="indicator.disaggregations.length > 0"
                                                    class="w-full flex flex-col gap-4">
                                                    <div v-for="disaggregate in indicator.disaggregations"
                                                        :key="disaggregate.disaggregation_id"
                                                        class="w-full flex flex-col gap-2 px-2 py-4 bg-sky-50 rounded-md shadow-md">
                                                        <Input type="number"
                                                            :placeholder="disaggregate.disaggregation_name"
                                                            v-model="disaggregate.value" class="w-1/4 bg-white" />
                                                        <Input type="text" placeholder="Remarks (optional)"
                                                            v-model="disaggregate.remarks" class="w-full bg-white" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                    </div>

                    <Card class="mt-2">
                        <CardHeader>
                            <CardTitle class="text-center text-lg font-bold">Event Resources Dispensed</CardTitle>
                            <CardDescription class="text-center">This is where you will record items such logistics or
                                drugs
                                provided to the PK site</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="flex justify-end">
                                <Button @click="createElement">Insert</Button>
                            </div>
                            <div v-for="(element, index) in elements" :key="index"
                                class="flex justify-evenly items-center element-container">
                                <div>
                                    <strong>Item #{{ index + 1 }}: </strong>
                                </div>

                                <div>
                                    <strong>Name</strong>
                                    <Input type='text' v-model="element.name" placeholder="Enter Item Name...." />
                                </div>
                                <div class="flex flex-col">
                                    <strong>Type</strong>
                                    <select class='border p-1 border w-[10rem]' v-model="element.type">
                                        <option value='0'>-Select-</option>
                                        <option value='1'>Drug</option>
                                        <option value='2'>Logistics</option>
                                    </select>
                                </div>

                                <div>
                                    <strong>Beneficiary count</strong>
                                    <Input type='number' v-model.number="element.beneficiary_count" />
                                </div>

                                <div>
                                    <strong>Amount</strong>
                                    <Input type='number' v-model.number="element.amount" />
                                </div>

                                <Button @click="elements.splice(index, 1)"
                                    class="p-2 remove-button bg-red-700">X</Button>
                            </div>
                        </CardContent>
                    </Card>
                </CardContent>

                <CardFooter class="w-full">
                    <Button variant="default" class="w-full cursor-pointer" size="lg" @click="saveData">Save
                        Data</Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
