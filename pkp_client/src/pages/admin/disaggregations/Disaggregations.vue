<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "@/axios/axios";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Search, EllipsisVertical, Pencil, XCircle, CheckCircle2 } from "lucide-vue-next";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'
import { useRouter } from "vue-router";
import { toast } from "vue-sonner";

const router = useRouter();
const disaggregations = ref<Disaggregation[]>([]);
const currentList = ref<Disaggregation[]>([]);
let generatedCode = ref("");
let searchKeyword = ref("");
let isCreateModalOpen = ref(false);
let isEditModalOpen = ref(false);
let recordToEdit = ref<Disaggregation>({
    disaggregation_id: 0,
    disaggregation_code: "",
    disaggregation_name: "",
});

function search() {
    const searchTerm = searchKeyword.value.toLowerCase();
    const searchedData = disaggregations.value.filter(item => {
        const itemName = item.disaggregation_name.toLowerCase();
        return itemName.includes(searchTerm);
    });
    currentList.value = searchedData;
}
interface Disaggregation {
    disaggregation_id: number;
    disaggregation_code: any;
    disaggregation_name: string;
}

const disaggregation = ref<Disaggregation>({
    disaggregation_id: 0,
    disaggregation_code: "",
    disaggregation_name: ""
});

function generateCode() {
    const prefix = "DIS";
    const date = (new Date()).toISOString();
    const midfix = date.split('-')[0].substring(2, 4);
    const totalForTheCurrentYear = disaggregations.value.filter(item => {
        const toCompare = item.disaggregation_code.split('-')[1];
        if (midfix === toCompare) {
            return item;
        }
    })
    const counter = totalForTheCurrentYear.length + 1;
    const codeGiven = prefix + '-' + midfix + '-' + counter;
    generatedCode.value = codeGiven;
}
const fetchList = () => {
    axios
        .get("/disaggregation/list")
        .then((response) => {
            disaggregations.value = response.data.data;
            currentList.value = response.data.data;
            console.log("data", disaggregations.value);
        })
        .catch((error) => {
            console.error("Error fetching programs:", error);
        });
};

function handleCreate() {
    if (disaggregation.value.disaggregation_code == '') {
        disaggregation.value.disaggregation_code = generatedCode;
    }
    axios
        .post("/disaggregation/create", disaggregation.value)
        .then((response) => {
            console.log(response.data);
            isCreateModalOpen.value = false;
            router.push({ path: "/admin/disaggregations" });
            toast("record created successfully!", {
                description: response.data.message,
                action: {
                    label: "Close",
                    onClick: () => toast.dismiss(),
                },
            });
            fetchList();
        })
        .catch((error) => {
            console.error(error.response.data);
            if (error.response) {
                toast("Failed with errors", {
                    description: "Error! Please contact admin for further troubleshooting",
                    action: {
                        label: "Close",
                        onClick: () => toast.dismiss(),
                    },
                });
            }
        })
        .finally(() => { });
}

function handleClose() {
    isCreateModalOpen.value = false;
}

function handleEdit(record: Disaggregation) {    
    recordToEdit.value = { ...record };
    isEditModalOpen.value = true;
}

function confirmEdit() {
    axios
        .put("/disaggregation/update", {
            disaggregation_id: recordToEdit.value.disaggregation_id,
            disaggregation_code: recordToEdit.value.disaggregation_code,
            disaggregation_name: recordToEdit.value.disaggregation_name
        })
        .then((response) => {
            console.log(response.data);
            toast("record updated successfully!", {
                description: response.data.message,
                action: {
                    label: "Close",
                    onClick: () => toast.dismiss(),
                },
            });
            fetchList();
            isEditModalOpen.value = false;
        })
        .catch((error) => {
            console.error(error.response.data);
            if (error.response) {
                toast("Failed to update program", {
                    description: error.response.data.message,
                    action: {
                        label: "Close",
                        onClick: () => toast.dismiss(),
                    },
                });
            }
        });
}

onMounted(() => {
    fetchList();
});
</script>



<template>
    <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
        <!-- header -->
        <div class="w-full flex justify-between items-center p-2 border">
            <div class="relative items-center">
                <Input v-model="searchKeyword" id="search" type="text" placeholder="Search Record" class="pl-8"
                    @input="search" />
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>
            <Button @click="isCreateModalOpen = true" variant="default" class="cursor-pointer" size="sm">Create</Button>
        </div>
    </div>

    <!-- table -->
    <div class="w-full h-full flex flex-col jusitfy-start items-start border">
        <div class="w-full h-[600px] overflow-y-scroll">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Id</TableHead>
                        <TableHead>Code</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead class="text-right">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="record in currentList">
                        <TableCell>{{ record.disaggregation_id }}</TableCell>
                        <TableCell>{{ record.disaggregation_code }}</TableCell>
                        <TableCell>{{ record.disaggregation_name }}</TableCell>
                        <TableCell class="w-full flex justify-end items-center gap-2">
                            <Popover>
                                <PopoverTrigger>
                                    <Button variant="ghost" size="icon" class="cursor-pointer">
                                        <EllipsisVertical class="h-4 w-4" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent>
                                    <div class="flex flex-col gap-1">
                                        <Button variant="ghost" size="sm"
                                            class="cursor-pointer text-xs flex justify-start items-center gap-1"
                                            @click="handleEdit(record)">
                                            <Pencil class="h-[1rem] w-[1rem]" />
                                            Edit
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

    <Dialog v-model:open="isCreateModalOpen">
        <DialogTrigger />
        <DialogContent class="font-poppins w-[20rem] md:max-w-[20rem] lg:max-w-[50rem]">
            <DialogHeader>
                <DialogTitle class="">Create</DialogTitle>
                <DialogDescription>Here you can create a record. </DialogDescription>
            </DialogHeader>

            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
                <form @submit.prevent="handleCreate" class="flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="disaggregation_name">Name:</label>
                        <Input type="text" id="disaggregation_name" placeholder="Enter detail here"
                            @input="generateCode()" v-model="disaggregation.disaggregation_name" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="disaggregation_code">Code:</label>
                        <Input type="text" id="disaggregation_code" v-model="disaggregation.disaggregation_code" />
                    </div>
                </form>
            </div>

            <DialogFooter>
                <Button type="submit" class="cursor-pointer hover:bg-red-500" @click="handleClose()">Cancel</Button>
                <Button type="submit" class="cursor-pointer hover:bg-emerald-500 hover:text-black"
                    @click="handleCreate">Submit</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="isEditModalOpen">
        <DialogContent class="font-poppins w-[20rem] md:max-w-[20rem] lg:max-w-[50rem]">
            <DialogHeader>
                <DialogTitle>Edit</DialogTitle>
                <DialogDescription>Apply edit here</DialogDescription>
            </DialogHeader>
            <div class="w-full flex flex-col justify-start items-start gap-2 p-2 border">
                <form @submit.prevent="confirmEdit" class="flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="disaggregation_code">Program Code:</label>
                        <Input type="text" class="bg-slate-200" id="disaggregation_code" placeholder="NP-0001"
                            v-model="recordToEdit.disaggregation_code" readonly />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="disaggregation_name">Disaggregation Name:</label>
                        <Input type="text" id="disaggregation_name" placeholder="Enter here..."
                            v-model="recordToEdit.disaggregation_name" required />
                    </div>
                    <!-- Add more fields as needed -->
                </form>
            </div>
            <DialogFooter>
                <Button type="button" class="cursor-pointer" @click="isEditModalOpen = false">Cancel</Button>
                <Button type="button" class="cursor-pointer bg-blue-500 hover:bg-blue-700 text-white"
                    @click="confirmEdit">Save</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>