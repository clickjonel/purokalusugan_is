<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import { Button } from "@/components/ui/button";
    import axios from '@/axios/axios';


    onMounted(()=>{
       
    })

   


</script>

<template>
     <Popover>
        <PopoverTrigger as-child>
            <Button class="w-full font-poppins font-normal" 
                variant="outline">
                <span>Select Barangay</span>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[500px] p-0">
            <div class="w-full flex flex-col justify-start items-center p-2 gap-1">
                
            </div>
        </PopoverContent>
    </Popover>
</template>