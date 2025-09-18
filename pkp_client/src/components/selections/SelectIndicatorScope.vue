<script setup lang="ts">
    import { ref } from 'vue';
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import { Button } from "@/components/ui/button";
    import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
    import { Label } from '@/components/ui/label'

    const scopes = ref<Scope>({
        '1':'Individual',
        '2':'Household'
    })

    const model = defineModel({
        default: ''
    })

    interface Scope {
        '1': string;
        '2': string;
    }



</script>

<template>
     <Popover>
        <PopoverTrigger as-child>
            <Button class="w-full font-poppins font-normal"variant="outline">
                <span class="text-left w-full" :class="!model ? 'text-gray-500' : '' ">{{ model ? scopes[model as keyof typeof scopes] : 'Scope of Indicator' }}</span>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[500px] p-0">
            <div class="w-full flex flex-col justify-start items-center p-2 gap-1">
                <RadioGroup v-model="model" class="flex text-sm capitalize p-4 justify-center items-center gap-6">
                    <div class="flex items-center space-x-2">
                        <RadioGroupItem id="individual" value="1" />
                        <Label for="individual">Individual</Label>
                    </div>
                        <div class="flex items-center space-x-2">
                        <RadioGroupItem id="household" value="2" />
                        <Label for="household">Household</Label>
                    </div>
                </RadioGroup>
                <Button class="font-poppins font-normal" variant="outline">
                    <span @click="model = '' " class="text-left w-full text-xs">Clear</span>
                </Button>
            </div>
        </PopoverContent>
    </Popover>
</template>