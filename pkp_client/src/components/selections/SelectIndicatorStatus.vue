<script setup lang="ts">
    import { ref } from 'vue';
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import { Button } from "@/components/ui/button";
    import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
    import { Label } from '@/components/ui/label'

    const status = ref<Status>({
        '0':'Deactivated',
        '1':'Active'
    })

    const model = defineModel({
        default: ''
    })

    interface Status {
        '0': string;
        '1': string;
    }



</script>

<template>
     <Popover>
        <PopoverTrigger as-child>
            <Button class="w-full font-poppins font-normal"variant="outline">
                <span class="text-left w-full" :class="!model ? 'text-gray-500' : '' ">{{ model ? status[model as keyof typeof status] : 'Status of Indicator' }}</span>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[500px] p-0">
            <div class="w-full flex flex-col justify-start items-center p-2 gap-1">
                <RadioGroup v-model="model" class="flex text-sm capitalize p-4 justify-center items-center gap-6">
                    <div class="flex items-center space-x-2">
                        <RadioGroupItem id="active" value="1" />
                        <Label for="active">Active</Label>
                    </div>
                        <div class="flex items-center space-x-2">
                        <RadioGroupItem id="deactivated" value="0" />
                        <Label for="deactivated">Deactivated</Label>
                    </div>
                </RadioGroup>
                <Button class="font-poppins font-normal" variant="outline">
                    <span @click="model = '' " class="text-left w-full text-xs">Clear</span>
                </Button>
            </div>
        </PopoverContent>
    </Popover>
</template>