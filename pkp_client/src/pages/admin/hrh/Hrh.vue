<script setup lang="ts">
import { onMounted, ref } from "vue"
import axios from '@/axios/axios';
import { Button } from "@/components/ui/button";
import { Input } from '@/components/ui/input'
import { Search } from "lucide-vue-next"
import { toast } from 'vue-sonner'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"

interface Hrh {
    user_level: string;
    first_name: string;
    middle_name: string;
    last_name: string;
    suffix?: string;
    nickname: string;
    [key: string]: any
}


var searchKeyword = ref('')
var isCreateHrhModalOpen = ref(false)

const hrhList = ref<Hrh[]>([]);
const hrh = ref<Hrh>({
    user_level: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    suffix: '',
    nickname: '',
})

onMounted(() => {
    fetchHrhList()
})

function fetchHrhList() {
    axios.get<{ list: Hrh[] }>('/hrh/list')
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

function search() {
    console.log(searchKeyword.value)
}

function createHrhUser() {
    axios.post('/hrh/create', hrh.value)
        .then((response) => {
            closeCreateHrhModal()
            toast('Successfull Create of HRH', {
                description: response.data.message,
                action: {
                    label: 'Close',
                    onClick: () => toast.dismiss(),
                },
            })
            fetchHrhList()
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

function resetCreateHrhForm() {
    hrh.value = {
        prefix: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        suffix: '',
        nickname: '',
        user_level: ''
    }
}

function closeCreateHrhModal() {
    isCreateHrhModalOpen.value = false
    resetCreateHrhForm()
}

</script>


<template>
    <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
        <!-- header -->
        <div class="w-full flex justify-between items-center p-2 border">
            <div class="relative items-center">
                <Input v-model="searchKeyword" id="search" type="text" placeholder="Search Keyword" class="pl-8"
                    @change="search" />
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>
            <Button @click="isCreateHrhModalOpen = true" variant="default" class="cursor-pointer" size="sm">Create
                Hrh</Button>
        </div>

        <!-- table -->
        <div class="w-full h-full flex flex-col jusitfy-start items-start border">
            <div class="w-full h-[640px] overflow-y-scroll">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>First Name</TableHead>
                            <TableHead>Middle Name</TableHead>
                            <TableHead>Lastname</TableHead>
                            <TableHead>Nickname</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="hrh in hrhList">
                            <TableCell>{{ hrh.first_name }}</TableCell>
                            <TableCell>{{ hrh.middle_name }}</TableCell>
                            <TableCell>{{ hrh.last_name }}</TableCell>
                            <TableCell>{{ hrh.nickname }}</TableCell>
                            <TableCell class="w-full flex justify-start items-center gap-2">
                                <Button variant="outline" size="sm" class="cursor-pointer text-xs">Assign</Button>
                                <Button variant="outline" size="sm" class="cursor-pointer text-xs">Deactivate</Button>
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

    <Dialog v-model:open="isCreateHrhModalOpen">
        <DialogTrigger />
        <DialogContent class="font-poppins sm:max-w-[500px] md:max-w-[1000px] lg:max-w-[1200px]">

            <DialogHeader>
                <DialogTitle class="">Create HRH User</DialogTitle>
                <DialogDescription>Form for creating a new HRH Data for storage and serve also as credentials when
                    accessing the system</DialogDescription>
            </DialogHeader>

            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
                <div class="w-full flex flex-col justify-start items-center gap-4">
                    <div class="w-full flex justify-start items-start relative gap-4">
                        <Input v-model="hrh.first_name" type="text" placeholder="First Name" />
                        <Input v-model="hrh.middle_name" type="text" placeholder="Middle Name" />
                        <Input v-model="hrh.last_name" type="text" placeholder="Last Name" />
                    </div>
                    <div class="w-full flex justify-start items-start relative gap-4">
                        <Input v-model="hrh.prefix" type="text" placeholder="Prefix" />
                        <Input v-model="hrh.suffix" type="text" placeholder="Suffix" />
                        <Input v-model="hrh.nickname" type="text" placeholder="Nickname" />
                    </div>
                    <div class="w-full flex justify-start items-start relative gap-4">
                        <Input v-model="hrh.email_address" type="email" placeholder="Email" />
                        <Input v-model="hrh.contact_no" type="text" placeholder="Contact Number" />
                        <Select v-model="hrh.user_level">
                            <SelectTrigger class="w-[180px]">
                                <SelectValue placeholder="Select User Access Level" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Access Levels</SelectLabel>
                                    <SelectItem value="1">Administrator</SelectItem>
                                    <SelectItem value="2">PK Committee Member</SelectItem>
                                    <SelectItem value="3">Program Head</SelectItem>
                                    <SelectItem value="4">C/PDOHO</SelectItem>
                                    <SelectItem value="5">HRH</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button @click="closeCreateHrhModal" variant="outline" class="cursor-pointer bg-red-500 text-white"
                    size="sm">Cancel</Button>
                <Button @click="createHrhUser" variant="outline" class="cursor-pointer bg-emerald-500 text-white"
                    size="sm">Create</Button>
            </DialogFooter>

        </DialogContent>
    </Dialog>

</template>