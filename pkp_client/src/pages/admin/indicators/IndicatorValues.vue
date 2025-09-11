<script setup lang="ts">
import { computed } from 'vue';
interface Props {
    eventRecord: Record<string, any>;
}
const props = defineProps<Props>();
const data = computed(() => {
    if (typeof props.eventRecord === 'object' && props.eventRecord !== null) {
        return { ...props.eventRecord };
    }
    return {};
});
const event={
    event_id:data.value.event_id,
    event_partner:data.value.event_partner,
    event_actual_budget: data.value.event_value,
    event_budget:data.value.event_budget,
    event_date:data.value.event_date,
    event_fund_source:data.value.event_fund_source,
    event_venue:data.value.event_venue,
    event_proponent:data.value.event_proponent,
    event_scope:data.value.event_scope,
    event_type:data.value.event_type,
    is_pk_site:data.value.is_pk_site
}

const dohCARLogo = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQRBmB7ueFlIGZ__ES7pCGHygeJHQBBlStvHw&s";
const dapayPKCARLogo = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTz5hW3nF0JRnTCPAgInbSO6xd62V2x-8fJZA&s";
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
                <strong>{{  }}</strong>
            </div>
            <div class="flex-1 flex gap-2 items-center">
                <small>Type</small>
                <strong>{{  event.event_type }}</strong>
            </div>
        </div>

        <div class="flex divide-black divide-x">
            <div class="flex-1 flex gap-2 items-center">
                <small>Date:</small>
                <strong>{{ event.event_date}}</strong>
            </div>
            <div class="flex-1 flex gap-2 items-center">
                <small>Venue:</small>
                <strong>{{ event.event_venue }}</strong>
            </div>
        </div>
        <div class="flex divide-black divide-x">
            <div class="flex-1 flex gap-2 items-center">
                <small>Budget Allocation:</small>
                <strong>{{ event.event_budget}}</strong>
            </div>
            <div class="flex-1 flex gap-2 items-center">
                <small>Actual Budget:</small>
                <strong>{{ event.event_actual_budget}}</strong>
            </div>
        </div>
        <div class="flex divide-black divide-x">
            <div class="flex-1 flex gap-2 items-center">
                <small>Source of Funding:</small>
                <strong>{{ event.event_fund_source}}</strong>
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
                <strong>{{ event.event_scope}}</strong>
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
</template>