<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "@/axios/axios";
import IndicatorValues from '@/pages/admin/indicators/IndicatorValues.vue'
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Search, EllipsisVertical, Pencil,PlusCircle  } from "lucide-vue-next";
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
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { useRouter } from "vue-router";
import { toast } from "vue-sonner";

const router = useRouter();
const events = ref<Event[]>([]);
const currentList = ref<Event[]>([]);

let searchKeyword = ref("");
let errorDetail = ref("");
let idToDelete = ref(0);
let isCreateModalOpen = ref(false);
let isDeleteModalOpen = ref(false);
let isEditModalOpen = ref(false);
let isEntryForIndicatorValuesModalOpen = ref(false);
let selectedEventRecord=ref({});
let toEdit = ref<Event>({
    event_id: 0,
    event_type: 0,
    event_date: '',
    event_venue: '',
    event_budget: 0,
    event_actual_budget: 0,
    event_fund_source: '',
    event_proponent: '',
    event_partner: '',
    event_scope: '',
    is_pk_site: 1,
});
interface Event {
    event_id: number,
    event_type: number,
    event_date: string,
    event_venue: string,
    event_budget: number,
    event_actual_budget: number,
    event_fund_source: string,
    event_proponent: string,
    event_partner: string,
    event_scope: string,
    is_pk_site: number
}

const event = ref<Event>({
    event_id: 0,
    event_type: 0,
    event_date: '',
    event_venue: '',
    event_budget: 0,
    event_actual_budget: 0,
    event_fund_source: '',
    event_proponent: '',
    event_partner: '',
    event_scope: '',
    is_pk_site: 1,
})
function showYesOrNo(option: number) {

    if (option) {
        return "Yes"
    }
    return "No";
}
function showColor(option: number) {
    if (option) {
        return "text-green-500";
    }
    return "text-red-500";
}
function search() {
    const searchTerm = searchKeyword.value.toLowerCase();
    const searchedData = events.value.filter(event => {
        const name = event.event_venue.toLowerCase();
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
function onEnterIndicatorValues(record:object){
    isEntryForIndicatorValuesModalOpen.value = true;
    selectedEventRecord.value = record;
}

function confirmDelete() {
    axios
        .delete("/event/delete", {
            data: {
                event_id: idToDelete.value,
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
function handleCreate() {
    axios
        .post("/event/create", event.value)
        .then((response) => {
            console.log(response.data);
            isCreateModalOpen.value = false;
            router.push({ path: "/admin/events" });
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
}
function handleEdit(record: Event) {
    toEdit.value = { ...record };
    isEditModalOpen.value = true;
}
function confirmEdit() {
    console.log('toEdit', toEdit)
    axios
        .put("/event/update", {
            event_id: toEdit.value.event_id,
            event_date: toEdit.value.event_date,
            event_venue: toEdit.value.event_venue,
            event_budget: toEdit.value.event_budget,
            event_actual_budget: toEdit.value.event_actual_budget,
            event_fund_source: toEdit.value.event_fund_source,
            event_proponent: toEdit.value.event_proponent,
            event_partner: toEdit.value.event_partner,
            event_scope: toEdit.value.event_scope,
            is_pk_site: toEdit.value.is_pk_site,
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
const fetchList = () => {
    axios
        .get("/event/list")
        .then((response) => {
            events.value = response.data.data;
            currentList.value = response.data.data;
            console.log("data", events.value);
        })
        .catch((error) => {
            console.error("Error fetching indicators:", error);
        });
};
onMounted(() => {
    fetchList();
});
</script>
<template>
    <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
        <!-- header -->
        <div class="w-full flex justify-between items-center p-2 border">
            <div class="relative items-center">
                <Input v-model="searchKeyword" id="search" type="text" placeholder="Search Venue..." class="pl-8"
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
                            <TableHead>Event Type</TableHead>
                            <TableHead>Event Date</TableHead>
                            <TableHead>Venue</TableHead>
                            <TableHead>Budget</TableHead>
                            <TableHead>Actual Budget</TableHead>
                            <TableHead>Fund Source</TableHead>
                            <TableHead>Proponent</TableHead>
                            <TableHead>Partner</TableHead>
                            <TableHead>Scope</TableHead>
                            <TableHead>Is Pk Site</TableHead>
                            <TableHead class="text-right">Action</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="record in currentList">
                            <TableCell>{{ record.event_id }}</TableCell>
                            <TableCell>{{ record.event_type }}</TableCell>
                            <TableCell>{{ record.event_date }}</TableCell>
                            <TableCell>{{ record.event_venue }}</TableCell>
                            <TableCell>{{ record.event_budget }}</TableCell>
                            <TableCell>{{ record.event_actual_budget }}</TableCell>
                            <TableCell>{{ record.event_fund_source }}</TableCell>
                            <TableCell>{{ record.event_proponent }}</TableCell>
                            <TableCell>{{ record.event_partner }}</TableCell>
                            <TableCell>{{ record.event_scope }}</TableCell>
                            <TableCell :class="showColor(record.is_pk_site)">
                                {{ showYesOrNo(record.is_pk_site) }}</TableCell>
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
                                            <Button variant="ghost" size="sm"
                                                class="cursor-pointer text-xs flex justify-start items-center gap-1"
                                                @click="onEnterIndicatorValues(record)">
                                                <PlusCircle class="h-[1rem] w-[1rem]" />
                                                Indicator Values
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
    <Dialog v-model:open="isCreateModalOpen">
        <DialogTrigger />
        <DialogContent class="font-poppins">
            <DialogHeader>
                <DialogTitle class="">Create an Event</DialogTitle>
                <DialogDescription>Enter event details </DialogDescription>
            </DialogHeader>

            <div class="flex flex-col justify-start items-start gap-2 p-2">
                <form @submit.prevent="handleCreate" class="flex flex-col gap-4">
                    <div class="w-100 flex">
                        <div class="flex-1 flex-col gap-2">
                            <label for="event_type">Event Type Scope:</label>
                            <RadioGroup id="event_type" v-model="event.event_type">
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="r1" :value="1" />
                                    <Label for="r1">Small Scale</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="r2" :value="2" />
                                    <Label for="r2">Large Scale</Label>
                                </div>
                            </RadioGroup>
                        </div>
                        <div class="flex-1 flex flex-col gap-2">
                            <label for="event_date">Event Date:</label>
                            <Input type="date" id="event_date" v-model="event.event_date" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="event_venue">Venue:</label>
                        <Input type="text" id="event_venue" placeholder="Where is the place?"
                            v-model="event.event_venue" />
                    </div>
                    <div class="flex">
                        <div class="flex-1 flex flex-col gap-2">
                            <label for="event_budget">Budget:</label>
                            <Input type="number" step="any" id="event_budget" v-model="event.event_budget" />
                        </div>
                        <div class="flex-1 flex flex-col gap-2">
                            <label for="event_actual_budget">Actual Budget:</label>
                            <Input type="number" step="any" id="event_actual_budget"
                                v-model="event.event_actual_budget" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="event_fund_source">Fund Source:</label>
                        <Input type="text" id="event_fund_source" placeholder="Where did the fund come frome?"
                            v-model="event.event_fund_source" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="event_proponent">Proponent(s):</label>
                        <Input type="text" id="event_proponent" placeholder="Whos is/are the proponent(s)?"
                            v-model="event.event_proponent" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="event_partner">Partner:</label>
                        <Input type="text" id="event_partner" placeholder="Whos is/are the proponent(s)?"
                            v-model="event.event_partner" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="event_scope">Scope:</label>
                        <Input type="text" id="event_scope" placeholder="Area(s) covered" v-model="event.event_scope" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="is_pk_site">Is this a PK Site?:</label>
                        <RadioGroup id="is_pk_site" v-model="event.is_pk_site">
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r1" :value="1" />
                                <Label for="r1">Yes</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r2" :value="0" />
                                <Label for="r2">No</Label>
                            </div>
                        </RadioGroup>
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
        <DialogContent class="font-poppins w-full">
            <DialogHeader>
                <DialogTitle>Edit</DialogTitle>
                <DialogDescription>Update details.</DialogDescription>
            </DialogHeader>
            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
                <form @submit.prevent="confirmEdit" class="flex flex-col gap-4">
                    <div class="w-100 flex">
                        <div class="flex-1 flex-col gap-2">
                            <label for="event_type">Event Type Scope:</label>
                            <RadioGroup id="event_type" v-model="toEdit.event_type">
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="r1" :value="1" />
                                    <Label for="r1">Small Scale</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="r2" :value="2" />
                                    <Label for="r2">Large Scale</Label>
                                </div>
                            </RadioGroup>
                        </div>
                        <div class="flex-1 flex flex-col gap-2">
                            <label for="event_date">Event Date:</label>
                            <Input type="date" id="event_date" v-model="toEdit.event_date" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="event_venue">Venue:</label>
                        <Input type="text" id="event_venue" placeholder="Where is the place?"
                            v-model="event.event_venue" />
                    </div>
                    <div class="flex">
                        <div class="flex-1 flex flex-col gap-2">
                            <label for="event_budget">Budget:</label>
                            <Input type="number" step="any" id="event_budget" v-model="toEdit.event_budget" />
                        </div>
                        <div class="flex-1 flex flex-col gap-2">
                            <label for="event_actual_budget">Actual Budget:</label>
                            <Input type="number" step="any" id="event_actual_budget"
                                v-model="toEdit.event_actual_budget" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="event_fund_source">Fund Source:</label>
                        <Input type="text" id="event_fund_source" placeholder="Where did the fund come frome?"
                            v-model="toEdit.event_fund_source" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="event_proponent">Proponent(s):</label>
                        <Input type="text" id="event_proponent" placeholder="Whos is/are the proponent(s)?"
                            v-model="toEdit.event_proponent" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="event_partner">Partner:</label>
                        <Input type="text" id="event_partner" placeholder="Whos is/are the proponent(s)?"
                            v-model="toEdit.event_partner" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="event_scope">Scope:</label>
                        <Input type="text" id="event_scope" placeholder="Area(s) covered"
                            v-model="toEdit.event_scope" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="is_pk_site">Is this a PK Site?:</label>
                        <RadioGroup id="is_pk_site" v-model="toEdit.is_pk_site">
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r1" :value="1" />
                                <Label for="r1">Yes</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r2" :value="0" />
                                <Label for="r2">No</Label>
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
    <Dialog v-model:open="isEntryForIndicatorValuesModalOpen">
        <DialogTrigger />
        <DialogContent class="sm:max-w-screen-xl w-full h-full overflow-y-scroll">
            <DialogHeader>
                <DialogTitle class="text-center">Event and Indicator Values</DialogTitle>
                <DialogDescription class="text-center">Event and Indicator Values here</DialogDescription>
            </DialogHeader>

            <IndicatorValues :eventRecord="selectedEventRecord"/>

            <DialogFooter>
                <Button type="submit" class="cursor-pointer hover:bg-red-500" @click="handleClose()">Cancel</Button>
                <Button type="submit" class="cursor-pointer hover:bg-emerald-500 hover:text-black"
                    @click="handleCreate">Submit</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>