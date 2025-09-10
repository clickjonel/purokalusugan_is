<script setup lang="ts">
import { onMounted, ref } from "vue"
import axios from '@/axios/axios';
import { Button } from "@/components/ui/button";
import { Input } from '@/components/ui/input'
import { Search,EllipsisVertical,UserRoundCog,UserLock } from "lucide-vue-next"
import { toast } from 'vue-sonner'
import { Popover,PopoverContent,PopoverTrigger } from '@/components/ui/popover'
import { Table,TableBody,TableCell,TableHead,TableHeader,TableRow } from '@/components/ui/table'
import { Dialog,DialogContent,DialogDescription,DialogFooter,DialogHeader,DialogTitle } from '@/components/ui/dialog'


var searchKeyword = ref('')

const teams = ref<Team[]>([]);

const team = ref<Team>({
    team_name:''
});

const popovers = ref({
    createPopover:{
        fields:{
            team_name:'',
            selectedMembers:[],
        },
    }
})

const modals = ref({
    manageMembers:{
        show:false,
        fields:{

        },
        team:{}
    }
})


onMounted(() => {
    fetchTeams()
})

function fetchTeams(){
    axios.get<{ teams: Team[] }>('/team/list')
    .then((response) => {
        teams.value = response.data.teams
        console.log(teams.value)
    })
    .catch((error) => {
        console.log(error)
    })
    .finally(() => {

    })
}

function saveTeam(){
    if(popovers.value.createPopover.fields.team_name !== ''){
        axios.post('/team/create', popovers.value.createPopover.fields)
        .then((response) => {
            toast('Action Success', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
            popovers.value.createPopover.fields.team_name = ''
            fetchTeams()
        })
        .catch((error) => {
            console.log(error)
            if (error.response) {
                toast('Failed With Errors', {
                    description: error.response.data.message,
                    action: {
                        label: 'Close',
                        onClick: () => toast.dismiss(),
                    },
                })
            }
        })
        .finally(() => {

        })
    }
    else{
         toast('Validation Error', {
            description: 'Team Name is Required',
            action: {
                label: 'Close',
                onClick: () => toast.dismiss(),
            },
        })
    }
}

interface Team {
    team_name:string,
}


</script>


<template>
    <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
        <!-- header -->
        <div class="w-full flex justify-between items-center p-2 border">
            <div class="relative items-center">
                <Input v-model="searchKeyword" id="search" type="text" placeholder="Search Keyword" class="pl-8"/>
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>

             <Popover>
                <PopoverTrigger asChild>
                    <Button variant="default" class="cursor-pointer" size="sm">Create</Button>
                </PopoverTrigger>
                <PopoverContent class="w-50 p-2">
                    <div class="flex flex-col gap-4">
                        <Input v-model="popovers.createPopover.fields.team_name" type="text" placeholder="Team Name"/>
                        <Button @click="saveTeam" variant="ghost" size="sm" class="justify-start text-xs bg-slate-300 hover:bg-green-300 cursor-pointer"> Save Team </Button>
                    </div>
                </PopoverContent>
            </Popover>
        </div>

        <!-- table -->
        <div class="w-full h-full flex flex-col jusitfy-start items-start border">
            <div class="w-full h-[640px] overflow-y-scroll">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Team Name</TableHead>
                            <TableHead>Team Scope</TableHead>
                            <TableHead>Team Members</TableHead>
                            <TableHead class="text-end">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="team in teams">
                            <TableCell>{{ team.team_name }}</TableCell>
                            <TableCell> </TableCell>
                            <TableCell> </TableCell>
                            <TableCell class="w-full flex justify-end items-center gap-2">
                                <Popover>
                                    <PopoverTrigger asChild>
                                        <Button variant="ghost" size="icon" class="cursor-pointer">
                                            <EllipsisVertical class="h-4 w-4" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-45 p-2">
                                        <div class="flex flex-col">
                                            <Button variant="ghost" size="sm" class="justify-start text-xs">
                                                <UserRoundCog/> 
                                               Edit
                                            </Button>
                                            <Button variant="ghost" size="sm" class="justify-start text-xs">
                                                <UserRoundCog/> 
                                                Manage Scopes
                                            </Button>
                                             <Button @click="modals.manageMembers.show = true" variant="ghost" size="sm" class="justify-start text-xs">
                                                <UserRoundCog/> 
                                                Manage Members
                                            </Button>
                                            <Button variant="ghost" size="sm" class="justify-start text-xs text-red-600">
                                                <UserLock /> 
                                                Disable
                                            </Button>
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- footer -->
        <div class="w-full h-[50px] flex justify-center items-center border p-2">
            <span>Pagination Here</span>
        </div>
    </div>

     <Dialog v-model:open="modals.manageMembers.show">
        <!-- <DialogTrigger /> -->
        <DialogContent class="font-poppins sm:max-w-[500px] md:max-w-[1000px] lg:max-w-[1200px]">

            <DialogHeader>
                <DialogTitle class="">Team Name: Manage Members</DialogTitle>
                <DialogDescription>This form is for managing the members of the team. Add/Remove Members of the Team as well as roles of each member</DialogDescription>
            </DialogHeader>

            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
                <div class="w-full flex flex-col justify-start items-center">
                    <span class="w-full font-semibold">Team Members</span>
                     <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Member Name</TableHead>
                                <TableHead>Member Role</TableHead>
                                <TableHead></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow>
                                <TableCell>Member 1</TableCell>
                                <TableCell>Team Leader</TableCell>
                                <TableCell class="w-full flex justify-end items-center gap-2">
                                   <Button variant="destructive" size="sm" class="justify-start text-xs">Remove</Button>
                                </TableCell>
                            </TableRow>
                            <TableRow>
                                <TableCell>Member 1</TableCell>
                                <TableCell>Team Leader</TableCell>
                                <TableCell class="w-full flex justify-end items-center gap-2">
                                   <Button variant="destructive" size="sm" class="justify-start text-xs">Remove</Button>
                                </TableCell>
                            </TableRow>
                            <TableRow>
                                <TableCell>Member 1</TableCell>
                                <TableCell>Team Leader</TableCell>
                                <TableCell class="w-full flex justify-end items-center gap-2">
                                   <Button variant="destructive" size="sm" class="justify-start text-xs">Remove</Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <DialogFooter>
                <!-- <Button @click="closeCreateHrhModal" variant="outline" class="cursor-pointer bg-red-500 text-white"
                    size="sm">Cancel</Button>
                <Button @click="createHrhUser" variant="outline" class="cursor-pointer bg-emerald-500 text-white"
                    size="sm">Create</Button> -->
            </DialogFooter>

        </DialogContent>
    </Dialog>
    

</template>