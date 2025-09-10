<script setup lang="ts"> 
    import { onMounted, ref } from "vue";
    import { Button } from "@/components/ui/button";
    import axios from '@/axios/axios';
    import { useRoute } from 'vue-router';
    import { Table,TableBody,TableCell,TableHead,TableHeader,TableRow } from '@/components/ui/table'
    import { Popover,PopoverContent,PopoverTrigger } from '@/components/ui/popover'

    const route = useRoute()
    const team = ref(null)

    onMounted(()=>{
        fetchTeam()
    })

    function fetchTeam(){
        axios.get('/team/find',{
            params:{
                team_id:route.params.id
            }
        })
        .then((response) => {
            team.value = response.data.team
            console.log(team.value)
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

</script>

<template>
    <div class="w-full h-screen flex flex-col justify-start items-start gap-4 p-4 font-poppins">
        <div class="w-full flex justify-between items-center">
            <span class="text-2xl font-bold">Manage Team</span>
        </div>
        <div class="w-full h-full flex flex-col justify-start items-start gap-2 p-4 border">
            <div class="w-full flex justify-between items-center">
                <span class="text-xl"></span>
                <Button variant="default" class="cursor-pointer" size="sm">Add Member</Button>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Member Name</TableHead>
                        <TableHead>Member Role</TableHead>
                        <TableHead></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <!-- <TableRow v-for="team in teams">
                        <TableCell>{{ team.team_name }}</TableCell>
                        <TableCell> </TableCell>
                        <TableCell> </TableCell>
                        <TableCell class="w-full flex justify-end items-center gap-2">
                            <Popover>
                                <PopoverTrigger asChild>
                                    <Button variant="ghost" size="icon" class="cursor-pointer">
                                        asd
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-45 p-2">
                                    <div class="flex flex-col">
                                        <Button variant="ghost" size="sm" class="justify-start text-xs">Remove</Button>
                                    </div>
                                </PopoverContent>
                            </Popover>
                        </TableCell>
                    </TableRow> -->
                </TableBody>
            </Table>
        </div>
    </div>
</template>