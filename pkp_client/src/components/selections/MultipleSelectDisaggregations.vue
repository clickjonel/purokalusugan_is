<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
    import { Button } from "@/components/ui/button";
    import axios from '@/axios/axios';
    import { useRouter } from 'vue-router';

    const router = useRouter()
    const disaggregations = ref<Disaggregation[]>([])
    const model = defineModel<Disaggregation[]>({
      default: []
    })

const props = defineProps<{
  remove: Disaggregation[];
}>();

    onMounted(()=>{
        fetchDisaggregations()
    })

    function fetchDisaggregations(){
        axios.get('/disaggregation/list')
        .then((response) => {
            disaggregations.value = response.data.data.filter(
                (d: Disaggregation) => !props.remove.some(r => r.disaggregation_id === d.disaggregation_id)
            );
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

   function push(disaggregation: Disaggregation) {
        if (!model.value.some(d => d.disaggregation_id === disaggregation.disaggregation_id)) {
            model.value = [...model.value, disaggregation];
        }
    }

    function splice(disaggregation: Disaggregation, event: MouseEvent) {
        event.stopPropagation();
        model.value = model.value.filter(d=> d.disaggregation_id !== disaggregation.disaggregation_id);
    }

    function isSelected(disaggregation: Disaggregation) {
      return model.value.some(d => d.disaggregation_id === disaggregation.disaggregation_id);
    }

    // function inRemove(disaggregation: Disaggregation) {
    //    return props.remove.some(d => d.disaggregation_id === disaggregation.disaggregation_id);
    // }

    interface Disaggregation {
        disaggregation_id:number
        disaggregation_code:string        
        disaggregation_name:string
        // 'is_totalable'
    }

</script>

<template>
     <Popover>
        <PopoverTrigger as-child>
            <Button class="w-full font-poppins font-normal" 
                variant="outline">
                <span v-if="model.length === 0">Select Disaggregation</span>
                <div class="w-full flex justify-start items-center gap-2" >
                    <span v-for="disaggregation in model" v-bind:key="disaggregation.disaggregation_id" @click="splice(disaggregation,$event)" class="rounded-full text-xs bg-gray-200 px-2 cursor-pointer hover:bg-red-200">{{ disaggregation.disaggregation_name }}</span>
                </div>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[500px] p-0">
            <div class="w-full flex flex-col justify-start items-center p-2 gap-1">
                <span @click="push(disaggregation)" v-for="disaggregation in disaggregations" class="w-full px-2 py-1 cursor-pointer hover:bg-gray-100 rounded-md" :class="isSelected(disaggregation) ? 'bg-gray-100' : 'bg-white' ">{{ disaggregation.disaggregation_name }}</span>
                <Button @click="router.push({path:'/admin/disaggregations'})" class="w-full font-poppins font-normal" variant="outline">Add New</Button>
                <span class="text-xs w-full text-justify text-amber-600 font-light">#Note: If not in list, click "Add New" Button and you will be redirected to Disaggregations Page then Click Create. Be sure to save selected disaggregations before clicking the "Add New Button".</span>
            </div>
        </PopoverContent>
    </Popover>
</template>