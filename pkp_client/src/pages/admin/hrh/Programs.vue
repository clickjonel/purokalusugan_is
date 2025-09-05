<script setup lang="ts">
    import { ref } from "vue"
    import axios from '@/axios/axios';
    import { Button } from "@/components/ui/button";
    import { Input } from '@/components/ui/input'
    import { Search } from "lucide-vue-next"
    import {
        Dialog,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogHeader,
        DialogTitle,
        DialogTrigger,
    } from '@/components/ui/dialog'

    let searchKeyword = ref('');
    let isCreateModalOpen = ref(false)
    function search(){
        console.log(searchKeyword.value);
    }
</script>
<template>
    <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
        <!-- header -->
         <div class="w-full flex justify-center items-center p-2 border">
            <h2 class="text-xl font-bold">Programs</h2>
         </div>
        <div class="w-full flex justify-between items-center p-2 border">
            <div class="relative items-center">
                <Input v-model="searchKeyword" id="search" type="text" placeholder="Search Keyword" class="pl-8" @change="search"/>
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>
            <Button @click="isCreateModalOpen = true" variant="default" class="cursor-pointer" size="sm">Create</Button>
        </div>

        <!-- table -->
        <div class="w-full h-full flex flex-col jusitfy-start items-start border overflow-y-scroll">
            <div class="w-full h-[500px]">

            </div>
        </div>

        <!-- footer -->
         <div class="w-full h-[50px] flex justify-center items-center border p-2">
            <span>Pagination Here</span>
         </div>
    </div>
    <Dialog v-model:open="isCreateModalOpen">
         <DialogTrigger />
        <DialogContent class="font-poppins sm:max-w-[500px] md:max-w-[1000px] lg:max-w-[1200px]">

            <DialogHeader>
                <DialogTitle class="">Create a Program</DialogTitle>
                 <DialogDescription>This is the Programs management. Only the administrators are allowed to Create, Edit and Delete a record.</DialogDescription>
            </DialogHeader>

            <div class="w-full flex flex-col justify-start items-start gap-2 p-2 border">
                asdf
            </div>

            <DialogFooter>
                <Button variant="outline" class="cursor-pointer bg-red-500 text-white" size="sm">Cancel</Button>
                <Button variant="outline" class="cursor-pointer bg-emerald-500 text-white" size="sm">Create</Button>
            </DialogFooter>

        </DialogContent>
    </Dialog>
</template>
