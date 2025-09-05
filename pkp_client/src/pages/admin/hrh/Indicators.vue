<script setup lang="ts">
import { ref } from "vue"
import axios from '@/axios/axios';
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useRouter } from "vue-router";

const isModalOpen = ref(false);
const router = useRouter()

function openModal() {
  isModalOpen.value = true;
}

function closeModal() {
  isModalOpen.value = false;
}

function handleCreateIndicator() {
    axios.post('/indicator/create', indicator.value)
        .then((response) => {
            console.log(response.data);
            // Close the modal and then redirect
            closeModal();
            router.push({ path: '/indicators' });
        })
        .catch((error) => {
            console.error(error.response.data);
        })
        .finally(() => { });
}

interface Indicator {
    program_id: string;
    indicator_code: string;
    indicator_name: string;
    indicator_description: string;
    indicator_status: number;
    indicator_scope: number;
}

const indicator = ref<Indicator>({
    program_id: '',
    indicator_code: '',
    indicator_name: '',
    indicator_description: '',
    indicator_status: 0,
    indicator_scope: 0,
});

</script>
<template>
    <div class="flex flex-col">
        <div class='flex justify-center border'>
            <h2 class="text-xl font-bold">Indicators</h2>
        </div>

        <div class="flex justify-end">
            <Button @click="openModal">Create</Button>
        </div>
        
        <div v-if="isModalOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50" @click.self="closeModal">            
            <div class="bg-white rounded-lg p-6 shadow-xl w-full max-w-lg">
                <div class="flex justify-end">
                    <Button variant="ghost" @click="closeModal" class="p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </Button>
                </div>
                <h3 class="text-xl font-semibold mb-4 text-center">Create New Indicator</h3>

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
                    <Button type="submit">Submit</Button>
                </form>
            </div>
        </div>

        <div>
            <table>
                <thead>

                </thead>
                <tbody>

                </tbody>
                <tfoot>
                    
                </tfoot>
            </table>
        </div>
    </div>
</template>
