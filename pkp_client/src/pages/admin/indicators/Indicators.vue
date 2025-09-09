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
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
} from '@/components/ui/combobox'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { useRouter } from "vue-router";
import { toast } from "vue-sonner";

const router = useRouter();
const indicators = ref<Indicator[]>([]);
const currentList = ref<Indicator[]>([]);
const programs = ref<Program[]>([]);
const selectedProgram = ref<Program | undefined>()
const indicatorCode = ref();
let searchKeyword = ref("");
let errorDetail = ref("");
let isCreateModalOpen = ref(false);
let isDeleteModalOpen = ref(false);
let idToDelete = ref(0);
let recordStatus = ref("");
let isEditModalOpen = ref(false);
let isStatusModalOpen = ref(false);
let toEdit = ref<Indicator>({
    indicator_id: 0,
    program_id: 0,
    indicator_code: "",
    indicator_name: "",
    indicator_description: "",
    indicator_status: true,
    indicator_scope: 0
});

interface Indicator {
    indicator_id: number,
    program_id: number,
    indicator_code: any,
    indicator_name: string,
    indicator_description: string,
    indicator_status: boolean,
    indicator_scope: number
}

const indicator = ref<Indicator>({
    indicator_id: 0,
    program_id: 0,
    indicator_code: "",
    indicator_name: "",
    indicator_description: "",
    indicator_status: true,
    indicator_scope: 0
})

interface Program {
    program_id: number;
    program_name: string;
    program_code: any;
    program_status: boolean;
}

const program = ref<Program>({
    program_id: 0,
    program_name: "",
    program_code: "",
    program_status: true,
});
function search() {
    const searchTerm = searchKeyword.value.toLowerCase();
    const searchedData = indicators.value.filter(indicator => {
        const name = indicator.indicator_name.toLowerCase();
        return name.includes(searchTerm);
    });
    currentList.value = searchedData;
}
function handleClose() {
    isCreateModalOpen.value = false;
}

function explainError(error: string) {
    if (error.includes("Integrity constraint violation")) {
        errorDetail.value =
            "The program code you entered already exists. Please enter another!";
    }
    return errorDetail;
}
function showStatusLabel(status: boolean) {
    if (status == true) {
        return "Active"
    }
    if (status == false) {
        return "Inactive"
    }
    return "Unknown"
}
function determineStatusColor(status: boolean) {
    if (status == true) {
        return "text-green-700"
    }
    if (status == false) {
        return "text-red-800"
    }
    return "Unknown"
}
function displayProgramName(id: number) {
    const obtainedProgram=programs.value.filter(program=>id==program.program_id);
    return obtainedProgram[0].program_name;
}
function showIndicatorScope(scope:number){
    let result = "";
    switch(scope){
        case 1: result = "Individual";break;
        case 2: result = "Household";break;
        default: result = "Undefined";break;
    }
    return result;
}
function confirmDelete() {
    axios
        .delete("/indicator/delete", {
            data: {
                program_id: idToDelete.value,
            },
        })
        .then((response) => {
            console.log(response.data);
            toast("record deleted successfully!", {
                description: response.data.message,
                action: {
                    label: "Close",
                    onClick: () => toast.dismiss(),
                },
            });
            fetchList();
            isDeleteModalOpen.value = false;
        })
        .catch((error) => {
            console.error(error.response.data);
            if (error.response) {
                toast("Failed to delete record", {
                    description: error.response.data.message,
                    action: {
                        label: "Close",
                        onClick: () => toast.dismiss(),
                    },
                });
            }
            isDeleteModalOpen.value = false;
        });
}

function confirmEdit() {
    console.log('toEdit', toEdit)
    axios
        .put("/indicator/update", {
            indicator_id: toEdit.value.indicator_id,
            program_id: toEdit.value.program_id,
            indicator_code: toEdit.value.indicator_code,
            indicator_name: toEdit.value.indicator_name,
            indicator_description: toEdit.value.indicator_description,
            indicator_status: toEdit.value.indicator_status,
            indicator_scope: toEdit.value.indicator_scope,
        })
        .then((response) => {
            console.log(response.data);
            toast("Record updated successfully!", {
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
function generateIndicatorCode() {    
    const prefix = selectedProgram.value?.program_code;
    const midFix = "IND";
    const dash = "-";
    const date = new Date();
    const year = date.getFullYear().toString().slice(2);
    let postfix = "";
    let totalIndicatorsPlusOne=indicators.value.length+1;
    postfix+=String(totalIndicatorsPlusOne).padStart(4,'0');
    const generatedCode = prefix+dash+midFix+dash+year+dash+postfix;
    indicatorCode.value=generatedCode;
}
function handleCreate() {
    if (indicator.value.indicator_code == '') {
        indicator.value.indicator_code = indicatorCode;
    }
    if (selectedProgram.value) {
        indicator.value.program_id = selectedProgram.value.program_id;
        axios
            .post("/indicator/create", indicator.value)
            .then((response) => {
                console.log(response.data);
                isCreateModalOpen.value = false;
                router.push({ path: "/indicators" });
                toast("Record created successfully!", {
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
                        description: explainError(error.response.data.message),
                        action: {
                            label: "Close",
                            onClick: () => toast.dismiss(),
                        },
                    });
                }
            })
            .finally(() => { });
    } else {
        toast("Failed with errors", {
            description: "Please select a program",
            action: {
                label: "Close",
                onClick: () => toast.dismiss(),
            },
        });
    }


}
function handleEdit(record: Indicator) {
    toEdit.value = { ...record };
    isEditModalOpen.value = true;
}
function handleStatus(record: Indicator, status: string) {
    if (status == "deactivate") {
        toEdit.value = { ...record, indicator_status: false }
        recordStatus.value = "Deactivate";
    }
    if (status == "activate") {
        toEdit.value = { ...record, indicator_status: true }
        recordStatus.value = "Activate";
    }
    isStatusModalOpen.value = true;
    console.log('details here', toEdit)
}
function confirmStatusUpdate() {
    axios
        .put("/indicator/status", {
            indicator_id: toEdit.value.indicator_id,
            indicator_status: toEdit.value.indicator_status,
        })
        .then((response) => {
            console.log(response.data);
            toast("Record updated successfully!", {
                description: response.data.message,
                action: {
                    label: "Close",
                    onClick: () => toast.dismiss(),
                },
            });
            fetchList();
            isStatusModalOpen.value = false;
        })
        .catch((error) => {
            console.error(error.response.data);
            if (error.response) {
                toast("Failed to update record", {
                    description: error.response.data.message,
                    action: {
                        label: "Close",
                        onClick: () => toast.dismiss(),
                    },
                });
            }
        });
}
const fetchList = () => {
    axios
        .get("/indicator/list")
        .then((response) => {
            indicators.value = response.data.data;
            currentList.value = response.data.data;
            console.log("indicators", indicators.value);
        })
        .catch((error) => {
            console.error("Error fetching indicators:", error);
        });
};

const fetchPrograms = () => {
    axios
        .get("/program/list")
        .then((response) => {
            programs.value = response.data.data;
            console.log("programs here", programs.value);
        })
        .catch((error) => {
            console.error("Error fetching programs:", error);
        });
};
onMounted(() => {
    fetchList();
    fetchPrograms();
});
</script>
<template>
    <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
        <!-- header -->
        <div class="w-full flex justify-between items-center p-2 border">
            <div class="relative items-center">
                <Input v-model="searchKeyword" id="search" type="text" placeholder="Search Indicator Name" class="pl-8"
                    @input="search" />
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>
            <Button @click="isCreateModalOpen = true" variant="default" class="cursor-pointer" size="sm">Create</Button>
        </div>

        <!-- table -->
        <div class="w-full h-full flex flex-col jusitfy-start items-start border">
            <div class="w-full h-[600px] overflow-y-scroll">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Id</TableHead>
                            <TableHead>Program Id</TableHead>
                            <TableHead>Indicator Code</TableHead>
                            <TableHead>Indicator Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Scope</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Action</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="indicator in currentList">
                            <TableCell>{{ indicator.indicator_id }}</TableCell>
                            <TableCell>{{ indicator.program_id }}</TableCell>
                            <TableCell>{{ indicator.indicator_code }}</TableCell>
                            <TableCell>{{ indicator.indicator_name }}</TableCell>
                            <TableCell>{{ indicator.indicator_description }}</TableCell>
                            <TableCell>{{ showIndicatorScope(indicator.indicator_scope) }}</TableCell>
                            <TableCell :class=determineStatusColor(indicator.indicator_status)>{{
                                showStatusLabel(indicator.indicator_status) }}</TableCell>
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
                                                @click="handleEdit(indicator)">
                                                <Pencil class="h-[1rem] w-[1rem]" />
                                                Edit
                                            </Button>
                                            <div class="ml-2 flex items-center justify-start">
                                                <XCircle v-if="indicator.indicator_status" class="h-[1rem] w-[1rem]" />
                                                <CheckCircle2 v-else class="h-[1rem] w-[1rem]" />
                                                <Button variant="ghost" size="sm"
                                                    :class="determineStatusColor(!indicator.indicator_status)"
                                                    @click="handleStatus(indicator, indicator.indicator_status ? 'deactivate' : 'activate')">
                                                    {{ indicator.indicator_status ? 'Deactivate' : 'Activate' }}
                                                </Button>
                                            </div>
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
    <Dialog v-model:open="isCreateModalOpen">
        <DialogTrigger />
        <DialogContent class="font-poppins w-[20rem] md:max-w-[50rem] lg:max-w-[50rem]">
            <DialogHeader>
                <DialogTitle class="">Create an Indicator</DialogTitle>
                <DialogDescription>You can create an indicator here. </DialogDescription>
            </DialogHeader>

            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
                <form @submit.prevent="handleCreate" class="flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="indicator_name">Select a Program</label>
                        <Combobox v-model="selectedProgram" by="program_id">
                            <ComboboxAnchor>
                                <div class="relative w-full items-center">
                                    <ComboboxInput class="pl-1 max-w-xl"
                                        :display-value="(val) => val?.program_name ?? ''"
                                        placeholder="Select program..." />
                                </div>
                            </ComboboxAnchor>

                            <ComboboxList>
                                <ComboboxEmpty>No program found.</ComboboxEmpty>
                                <ComboboxGroup class="h-60 overflow-y-scroll">
                                    <ComboboxItem v-for="program in programs" :key="program.program_id"
                                        :value="program">
                                        {{ program.program_name }}
                                        <ComboboxItemIndicator>
                                            <Check class="ml-auto size-4" />
                                        </ComboboxItemIndicator>
                                    </ComboboxItem>
                                </ComboboxGroup>
                            </ComboboxList>
                        </Combobox>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="indicator_name">Indicator Name:</label>
                        <Input type="text" id="indicator_name" placeholder="example: Nutrition"
                            @input="generateIndicatorCode()" v-model="indicator.indicator_name" required />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="indicator_description">Indicator Description:</label>
                        <Input type="text" id="indicator_description" placeholder="Description"
                            v-model="indicator.indicator_description" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="indicator_scope">Indicator Scope:</label>
                        <RadioGroup default-value="1" id="indicator_scope" v-model="indicator.indicator_scope">
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r2" value="1" />
                                <Label for="1">Individual</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r3" value="2" />
                                <Label for="2">Household</Label>
                            </div>
                        </RadioGroup>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="indicator_code">Indicator Code:</label>
                        <Input type="text" id="indicator_code" placeholder="leave blank to auto generated"
                            v-model="indicator.indicator_code" />
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

    <Dialog v-model:open="isDeleteModalOpen">
        <DialogContent class="font-poppins w-[20rem] md:max-w-[20rem] lg:max-w-[50rem]">
            <DialogHeader>
                <DialogTitle>Confirm Deletion</DialogTitle>
                <DialogDescription>Are you sure you want to delete this record?</DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button type="button" class="cursor-pointer" @click="isDeleteModalOpen = false">Cancel</Button>
                <Button type="button" class="cursor-pointer bg-red-500 hover:bg-red-700 text-white"
                    @click="confirmDelete">Delete</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="isEditModalOpen">
        <DialogContent class="font-poppins w-[20rem] md:max-w-[20rem] lg:max-w-[50rem]">
            <DialogHeader>
                <DialogTitle>Edit</DialogTitle>
                <DialogDescription>Update details.</DialogDescription>
            </DialogHeader>
            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
                <form @submit.prevent="confirmEdit" class="flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="indicator_code">Indicator Code:</label>
                        <Input type="text" class="bg-slate-200" id="indicator_code" placeholder="NP-0001"
                            v-model="toEdit.indicator_code" hidden />
                        <strong>{{ toEdit.indicator_code }}</strong>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="program_id">Program:</label>
                        <Input type="number" class="bg-slate-200" id="program_id" v-model="toEdit.program_id" hidden />
                        <strong>{{ displayProgramName(toEdit.program_id) }}</strong>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="indicator_name">Indicator Name:</label>
                        <Input type="text" id="indicator_name" placeholder="example: Nutrition"
                            v-model="toEdit.indicator_name" required />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="indicator_description">Indicator Description:</label>
                        <Input type="text" id="indicator_description" placeholder="Description"
                            v-model="toEdit.indicator_description" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="indicator_scope">Indicator Scope:</label>
                        <RadioGroup id="indicator_scope" v-model="toEdit.indicator_scope">
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r2" :value="1" />
                                <Label for="r2">Individual</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r3" :value="2" />
                                <Label for="r3">Household</Label>
                            </div>
                        </RadioGroup>
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

    <Dialog v-model:open="isStatusModalOpen">
        <DialogContent class="font-poppins w-[20rem] md:max-w-[20rem] lg:max-w-[50rem]">
            <DialogHeader>
                <DialogTitle>Confirm Changes</DialogTitle>
                <DialogDescription>Are you sure you want to {{ recordStatus }} this program?</DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button type="button" class="cursor-pointer" @click="isStatusModalOpen = false">Cancel</Button>
                <Button type="button" class="cursor-pointer text-white" @click="confirmStatusUpdate">{{ recordStatus
                    }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>