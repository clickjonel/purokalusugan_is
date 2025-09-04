<script setup lang="ts">
import { ref } from "vue"
import axios from 'axios';
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useRouter } from "vue-router";

const router = useRouter()

function handleCreateIndicator() {
    axios.post('http://127.0.0.1:8000/api/indicator/create', indicator.value)
        .then((response) => {
            console.log(response.data);
            // Handle success response, e.g., redirect to indicator list or show success message
            router.push({ path: '/indicators' });
        })
        .catch((error) => {
            console.error(error.response.data);
            // Handle error response, e.g., show error message
        })
        .finally(() => {
            // Any final actions, e.g., reset form or loading state
        });
}
interface Indicator {
    program_id: string;
    indicator_code: string;
    indicator_name: string;
    indicator_description: string;
    indicator_status: number;
    indicator_scope: number;
}
const indicator = ref < Indicator > ({
    program_id: '',
    indicator_code: '',
    indicator_name: '',
    indicator_description: '',
    indicator_status: 0,
    indicator_scope: 0,
});

</script>
<template>
    <div class="w-full h-full flex flex-col justify-start items-start gap-2 p-2">
        <form @submit.prevent="handleCreateIndicator" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <label for="program_id">Program ID:</label>
                <Input type="number" id="program_id" v-model="indicator.program_id" required />
            </div>
            <div class="flex flex-col gap-2">
                <label for="indicator_code">Indicator Code:</label>
                <Input type="text" id="indicator_code" v-model="indicator.indicator_code" required />
            </div>
            <div class="flex flex-col gap-2">
                <label for="indicator_name">Indicator Name:</label>
                <Input type="text" id="indicator_name" v-model="indicator.indicator_name" required />
            </div>
            <div class="flex flex-col gap-2">
                <label for="indicator_description">Indicator Description:</label>
                <textarea id="indicator_description" v-model="indicator.indicator_description" required
                    class="w-full p-2 border border-gray-300 rounded-md"></textarea>
            </div>
            <div class="flex flex-col gap-2">
                <label for="indicator_status">Indicator Status:</label>
                <Input type="number" id="indicator_status" v-model="indicator.indicator_status" required />
            </div>
            <div class="flex flex-col gap-2">
                <label for="indicator_scope">Indicator Scope:</label>
                <Input type="number" id="indicator_scope" v-model="indicator.indicator_scope" required />
            </div>
            <Button type="submit">Create Indicator</Button>
        </form>
    </div>
</template>