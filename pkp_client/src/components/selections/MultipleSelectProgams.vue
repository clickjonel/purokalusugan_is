<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import { Button } from "@/components/ui/button";
    import axios from '@/axios/axios';

    const programs = ref<Program[]>([])
    const model = defineModel<Program[]>({
      default: []
    })

    onMounted(()=>{
        fetchPrograms()
    })

    function fetchPrograms(){
        axios.get('/program/list')
        .then((response) => {
            programs.value = response.data.data
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

   function push(program: Program) {
        if (!model.value.some(p => p.program_id === program.program_id)) {
            model.value = [...model.value, program];
        }
    }

    function splice(program: Program, event: MouseEvent) {
        event.stopPropagation();
        model.value = model.value.filter(p => p.program_id !== program.program_id);
    }

    function isSelected(program: Program) {
      return model.value.some(p => p.program_id === program.program_id);
    }

    interface Program {
        program_name:string
        program_id:number
    }

</script>

<template>
     <Popover>
        <PopoverTrigger as-child>
            <Button class="w-full font-poppins font-normal" 
                variant="outline">
                <span v-if="model.length === 0">Select Programs</span>
                <div class="w-full flex justify-start items-center gap-2" >
                    <span v-for="program in model" @click="splice(program,$event)" class="rounded-full text-xs bg-gray-200 px-2 cursor-pointer hover:bg-red-200">{{ program.program_name }}</span>
                </div>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[500px] p-0">
            <div class="w-full flex flex-col justify-start items-center p-2 gap-1">
                <span @click="push(program)" v-for="program in programs" class="w-full px-2 py-1 cursor-pointer hover:bg-gray-100 rounded-md" :class="isSelected(program) ? 'bg-gray-100' : 'bg-white' ">{{ program.program_name }}</span>
            </div>
        </PopoverContent>
    </Popover>
</template>