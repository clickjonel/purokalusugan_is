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

    const router = useRouter();
    const route = useRoute();
    const event = ref<Event | null>(null);
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
        console.log(formData.value)
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
                            remarks: ""
                        });
                    });

                    programObj.indicators.push(indicatorObj);
                });

                barangayObj.programs.push(programObj);
            });

            barangays.push(barangayObj);
        });

        formData.value.barangays = barangays;
    }

    async function saveData(){
        console.log(formData.value);
         axios.post('/event/populate', formData.value)
        .then((response) => {
            toast('Action Successfull', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
            router.push({path:'/admin/events'})
          
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
                                                <div class="w-3/4 flex flex-col justify-start items-start gap-2 font-light">
                                                    <span v-for="proponent in event.event_proponent_array">{{ proponent }}</span>
                                                </div>
                                           </div>
                                           <div class="w-1/2 flex justify-start items-start gap-2">
                                                <span class="w-1/4 font-medium">Partners:</span>
                                                <div class="w-3/4 flex flex-col justify-start items-start gap-2 font-light">
                                                    <span v-for="partner in event.event_partner_array">{{ partner }}</span>
                                                </div>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Event barangays with programs -->
                        <Card v-if="formData.barangays.length > 0" v-for="barangay in formData.barangays" :key="barangay.barangay_id" class="w-full">
                            <CardHeader>
                                <CardTitle>{{ barangay.barangay_name }}</CardTitle>
                                <CardDescription>Fill up the program indicators according to program services served.</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div class="w-full flex flex-col justify-start items-start font-poppins border border-black divide-y divide-black">
                                    <div class="w-full flex justify-start items-center divide-x divide-black">
                                        <span class="w-1/4 p-2 font-medium uppercase text-left text-sm">Program</span>
                                        <span class="w-3/4 p-2 font-medium uppercase text-center text-sm">Indicators</span>
                                    </div>
                                    <div v-for="program in barangay.programs" :key="program.program_id" class="w-full flex justify-start items-stretch divide-x divide-black text-light text-sm">
                                        <span class="w-1/4 p-2">{{ program.program_name }}</span>
                                        <div class="w-3/4 flex flex-col justify-start items-stretch divide-y divide-black">
                                            <div v-for="indicator in program.indicators" :key="indicator.indicator_id" class="w-full flex flex-col justify-start items-start gap-2 p-2">
                                                <span class="text-xs">{{ indicator.indicator_name }}</span>
                                                <div v-if="indicator.disaggregations.length > 0" class="w-full flex flex-col gap-4">
                                                    <div v-for="disaggregate in indicator.disaggregations" :key="disaggregate.disaggregation_id" class="w-full flex flex-col gap-2 px-2 py-4 bg-sky-50 rounded-md shadow-md">
                                                        <Input
                                                            type="number"
                                                            :placeholder="disaggregate.disaggregation_name"
                                                            v-model="disaggregate.value"
                                                            class="w-1/4 bg-white"
                                                        />
                                                        <Input
                                                            type="text"
                                                            placeholder="Remarks (optional)"
                                                            v-model="disaggregate.remarks"
                                                            class="w-full bg-white"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </CardContent>
                <CardFooter>
                    <Button variant="default" class="cursor-pointer" size="sm" @click="saveData">Save Data</Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
