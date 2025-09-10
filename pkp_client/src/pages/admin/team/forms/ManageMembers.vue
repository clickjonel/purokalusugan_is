<script setup lang="ts"> 
    import { onMounted, ref } from "vue";
    import { Button } from "@/components/ui/button";
    import axios from '@/axios/axios';
    import { useRoute } from 'vue-router';
    import { Table,TableBody,TableCell,TableHead,TableHeader,TableRow } from '@/components/ui/table'
    import { Popover,PopoverContent,PopoverTrigger } from '@/components/ui/popover'
    import { Input } from '@/components/ui/input'
    import { Select,SelectContent,SelectGroup,SelectItem,SelectLabel,SelectTrigger,SelectValue } from "@/components/ui/select"
    import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxList } from "@/components/ui/combobox"
    import { Search } from "lucide-vue-next"

    const route = useRoute()
    const team = ref(null)
    const hrhList = ref<HRH[]>([])

    const teamMember = ref<TeamMember>({
        hrh:{
            pk_user_id:0
        },
        team_id:Number(route.params.id),
        member_role:0
    })

    onMounted(()=>{
        fetchTeam()
        fetchHRHList()
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

    function fetchHRHList(){
        axios.get('/hrh/list')
        .then((response) => {
            hrhList.value = response.data.list
            console.log(hrhList.value)
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

    function saveMember(){
        axios.post('/team/member/create',{
            hrh_id:teamMember.value.hrh.pk_user_id,
            team_id:teamMember.value.team_id,
            member_role:teamMember.value.member_role
        })
         .then((response) => {
            // team.value = response.data.team
            console.log(response.data.message)
        })
        .catch((error) => {
            console.log(error)
        })
        .finally(() => {

        })
    }

    interface TeamMember {
        hrh:{
            pk_user_id:number
        }
        team_id:number
        member_role:number
    }

    interface HRH {
        pk_user_id:number
        full_name:number
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
                 <Popover>
                    <PopoverTrigger asChild>
                        <Button variant="default" size="sm" class="cursor-pointer">Add Member</Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-100 p-4">
                        <div class="flex flex-col justify-start items-start gap-4">
                            <Combobox v-model="teamMember.hrh" by="label" class="w-full">
                                <ComboboxAnchor class="w-full border rounded-md">
                                    <div class="relative w-full max-w-sm items-center">
                                        <ComboboxInput class="pl-9" :display-value="(hrh) => hrh?.full_name ?? ''" placeholder="Select HRH..." />
                                        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
                                        <Search class="size-4 text-muted-foreground" />
                                        </span>
                                    </div>
                                </ComboboxAnchor>

                                <ComboboxList>
                                <ComboboxEmpty>
                                    No HRH found.
                                </ComboboxEmpty>

                                <ComboboxGroup>
                                    <ComboboxItem
                                    v-for="hrh in hrhList"
                                    :key="hrh.pk_user_id"
                                    :value="hrh"
                                    >
                                    {{ hrh.full_name }}

                                    <ComboboxItemIndicator>
                                        <!-- <Check :class="cn('ml-auto h-4 w-4')" /> -->
                                    </ComboboxItemIndicator>
                                    </ComboboxItem>
                                </ComboboxGroup>
                                </ComboboxList>
                            </Combobox>

                            <Select v-model="teamMember.member_role">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select Member Role" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Member Roles</SelectLabel>
                                        <SelectItem value="1">Team Leader</SelectItem>
                                        <SelectItem value="2">Team Member</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>

                           <Button @click="saveMember" variant="default" size="sm" class="cursor-pointer hover:bg-green-600 bg-gray-600">Save Member</Button>
                        </div>
                    </PopoverContent>
                </Popover>
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