<script setup lang="ts">
    import { Button } from "@/components/ui/button";
    import { Input } from "@/components/ui/input";
    import { CalendarIcon } from "lucide-vue-next"
    import { onMounted, ref, computed } from "vue"
    import { Calendar } from "@/components/ui/calendar"
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import { Select,SelectContent,SelectGroup,SelectItem,SelectLabel,SelectTrigger,SelectValue } from '@/components/ui/select'
    import axios from '@/axios/axios';
    import { Card,CardContent,CardDescription,CardFooter,CardHeader,CardTitle } from '@/components/ui/card'
    import { toast } from 'vue-sonner'
    import { useRouter,useRoute } from "vue-router";
    import MultipleSelectProgams from "@/components/selections/MultipleSelectProgams.vue";
    import MultipleSelectBarangay from "@/components/selections/MultipleSelectBarangay.vue";
    import CalendarPopover from "@/components/CalendarPopover.vue";
    import type { DateValue } from "@internationalized/date"
    import { CalendarDate, parseDate } from "@internationalized/date"

    const router = useRouter()
    const route = useRoute()

    const event = ref<EventFormatted>({
        event_id: undefined,
        event_name: '',
        event_venue: '',
        event_budget:0,
        event_actual_budget: 0,
        event_fund_source: '',
        event_proponents: '',
        event_partners: '',
        event_type: '',
        event_date_start: '',
        event_date_end: '',
        programs: [],
        barangays: [],
        // values: [],
    })

    // Create computed properties to handle date conversion
    const eventDateStart = computed({
        get: (): DateValue | undefined => {
            if (!event.value.event_date_start) return undefined;
            try {
                // If it's already a DateValue, return it
                if (typeof event.value.event_date_start === 'object') {
                    return event.value.event_date_start as DateValue;
                }
                // Convert string to DateValue
                return parseDate(event.value.event_date_start);
            } catch (error) {
                console.warn('Invalid date format:', event.value.event_date_start);
                return undefined;
            }
        },
        set: (value: DateValue | undefined) => {
            event.value.event_date_start = value ? value.toString() : '';
        }
    });

    const eventDateEnd = computed({
        get: (): DateValue | undefined => {
            if (!event.value.event_date_end) return undefined;
            try {
                // If it's already a DateValue, return it
                if (typeof event.value.event_date_end === 'object') {
                    return event.value.event_date_end as DateValue;
                }
                // Convert string to DateValue
                return parseDate(event.value.event_date_end);
            } catch (error) {
                console.warn('Invalid date format:', event.value.event_date_end);
                return undefined;
            }
        },
        set: (value: DateValue | undefined) => {
            event.value.event_date_end = value ? value.toString() : '';
        }
    });

    // Helper function to format date for display
    const formatDateForDisplay = (dateValue: DateValue | string | undefined): string => {
        if (!dateValue) return 'Select Date';
        
        try {
            if (typeof dateValue === 'string') {
                const parsed = parseDate(dateValue);
                return parsed.toString();
            }
            return dateValue.toString();
        } catch (error) {
            return 'Select Date';
        }
    };

    onMounted(()=>{
        fetchEvent()
    })

    async function fetchEvent(): Promise<void> {
        try {
            const response = await axios.get('/event/fetch', {
                params: {
                    event_id: route.params.id
                }
            });

            const eventFetched = response.data.event;   

            event.value = {
                event_id: eventFetched.event_id,
                event_name: eventFetched.event_name,
                event_venue: eventFetched.event_venue,
                event_budget: eventFetched.event_budget,
                event_actual_budget: eventFetched.event_actual_budget,
                event_fund_source: eventFetched.event_fund_source,
                event_proponents: eventFetched.event_proponents,
                event_partners: eventFetched.event_partners,
                event_type: eventFetched.event_type,
                event_date_start: eventFetched.event_date_start,
                event_date_end: eventFetched.event_date_end,
                programs: eventFetched.programs,
                barangays: eventFetched.barangays,
                values: eventFetched.values,
            }

            console.log(event.value)
        } catch (error) {
            console.error('Error fetching event:', error);
            toast.error('Failed to load event data');
        }
    }

    function updateEventDetails(){
        const details = {
             event_id: event.value.event_id,
            event_name: event.value.event_name,
            event_venue: event.value.event_venue,
            event_budget: event.value.event_budget,
            event_actual_budget: event.value.event_actual_budget,
            event_fund_source: event.value.event_fund_source,
            event_proponents: event.value.event_proponents,
            event_partners: event.value.event_partners,
            event_type: event.value.event_type,
            event_date_start: event.value.event_date_start,
            event_date_end: event.value.event_date_end,
            // programs: event.value.programs.map(program => program.program_id),
            // barangays: event.value.barangays.map(barangay => barangay.barangay_id),
        }
        console.log(details)

         axios.post('/event/update/details', details)
        .then((response) => {
            toast('Action Successfull', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
            fetchEvent()
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

    // Function to update individual event values
    function updateEventValue(valueItem: EventValue) {
        const value = {
            indicator_value_id: valueItem.indicator_value_id,
            value: valueItem.value,
            remarks: valueItem.remarks
        };

        axios.put('/indicator/value/update', value)
            .then((response) => {
                toast('Action Successfull', {
                    description: response.data.message,
                    action: {
                        label: 'Close',
                        onClick: () => toast.dismiss(),
                    },
                })
                fetchEvent()
            })
            .catch((error) => {
                console.log(error)
                if (error.response) {
                    toast('Action Failed', {
                        description: error.response.data.message,
                        action: {
                            label: 'Close',
                            onClick: () => toast.dismiss(),
                        },
                    })
                }
            });
    }


export interface EventFormatted {
  event_id?: number;
  event_name: string;
  event_venue: string;
  event_budget: number;
  event_actual_budget: number;
  event_fund_source: string;
  event_proponents: string;
  event_partners: string;
  event_type: string | number;
  event_date_start: string | DateValue;
  event_date_end: string | DateValue;
  programs: Program[];
  barangays: Barangay[];
  values?: EventValue[];
}

export interface Program {
  program_id: number;
  program_name: string;
}

export interface Barangay {
  barangay_id: number;
  barangay_name: string;
}

export interface EventValue {
  indicator_value_id: number;
  value: string | number;
  remarks: string;
  barangay: Barangay;
  indicator_disaggregation: IndicatorDisaggregation;
}

export interface IndicatorDisaggregation {
  indicator_disaggregation_id: number;
  indicator: Indicator;
}

export interface Indicator {
  indicator_id: number;
  indicator_name: string;
}

</script>

<template>
   
    <div class="w-full flex justify-center items-center p-4">
        <div class="w-full flex flex-col justify-center items-center gap-4">
            <Card class="w-full">
                <CardHeader>
                    <CardTitle>Update Event Details</CardTitle>
                    <CardDescription>Update Event Details and other data related to event</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">
                        <div class="w-full flex justify-start items-start gap-4">
                            <Input v-model="event.event_name" type="text" placeholder="Event Name" class=""/>
                            <Input v-model="event.event_venue" type="text" placeholder="Event Venue" class=""/>
                            <Input v-model="event.event_budget" type="number" placeholder="Event Budget" class=""/>
                            <Input v-model="event.event_actual_budget" type="number" placeholder="Event Actual Budget" class=""/>
                            <Input v-model="event.event_fund_source" type="text" placeholder="Fund Source" class=""/>
                        </div>
                        <div class="w-full flex flex-col justify-start items-start gap-4">
                            <Input v-model="event.event_proponents" type="text" placeholder="Event Proponents(if multiple separate by commas)" class=""/>
                            <Input v-model="event.event_partners" type="text" placeholder="Event Partners(if multiple separate by commas)" class=""/>
                        </div>
                        
                        <div class="w-full flex justify-between items-center gap-4">
                            
                            <Select v-model="event.event_type">
                                <SelectTrigger class="w-1/3">
                                    <SelectValue placeholder="Select Event Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Event Types</SelectLabel>
                                        <SelectItem value="1">Small Scale</SelectItem>
                                        <SelectItem value="2">Large Scale</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>

                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button class="w-1/3"
                                        variant="outline">
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        {{ formatDateForDisplay(eventDateStart) || 'Event Date Start' }}
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-auto p-0">
                                    <Calendar v-model="eventDateStart" initial-focus />
                                </PopoverContent>
                            </Popover>

                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button class="w-1/3"
                                        variant="outline">
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        {{ formatDateForDisplay(eventDateEnd) || 'Event Date End' }}
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-auto p-0">
                                    <Calendar v-model="eventDateEnd" initial-focus />
                                </PopoverContent>
                            </Popover>

                        </div>

                        <!-- <MultipleSelectProgams v-model="event.programs"/>
                        <MultipleSelectBarangay v-model="event.barangays"/> -->

                    </div>
                </CardContent>
                <CardFooter>
                    <Button @click="updateEventDetails" variant="default" class="cursor-pointer" size="sm">Update Event Details</Button>
                </CardFooter>
            </Card>

             <Card class="w-full">
                <CardHeader>
                    <CardTitle>Update Event Values</CardTitle>
                    <CardDescription>Update Event Details and other data related to event</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">
                        <div v-for="value in event.values" :key="value.indicator_value_id" class="w-full flex flex-col justify-start items-center gap-2 bg-sky-50 rounded-md shadow-md p-2">
                            <span class="w-full text-left uppercase font-medium">Barangay {{ value.barangay.barangay_name }}</span>
                            <span class="w-full text-left uppercase text-sm font-light">{{ value.indicator_disaggregation.indicator.indicator_name }}</span>
                           <Input v-model="value.value" type="text" placeholder="Value" class="bg-white"/>
                           <Input v-model="value.remarks" type="text" placeholder="Remarks" class="bg-white"/>
                           <div class="w-full flex justify-start items-start gap-2">
                                <Button @click="updateEventValue(value)" variant="default" class="cursor-pointer" size="sm">Update</Button>
                           </div>
                        </div>
                    </div>
                </CardContent>
                <CardFooter>
                    <Button variant="default" class="cursor-pointer" size="sm">Update All Event Values</Button>
                </CardFooter>
            </Card>

        </div>
    </div>
    
</template>