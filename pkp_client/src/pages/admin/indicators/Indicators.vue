<script setup lang = "ts">
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
const indicators = ref<Indicator[]>([]);
const currentList = ref<Indicator[]>([]);
const programs = ref<Program[]>([]);
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

interface Indicator{
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

function generateIndicatorCode(event: any) {
  const userInput = event.target.value;
  const splittedUserInput = userInput.split(' ');
  let suggestedProgramCode = "";
  for (let i = 0; i < splittedUserInput.length; i++) {
    let word = splittedUserInput[i];
    let firstLetter = word.substring(0, 1).toUpperCase();
    suggestedProgramCode += firstLetter;
  }
  indicatorCode.value = suggestedProgramCode;
  indicatorCode.value = suggestedProgramCode;
}
function handleCreate() {
  if (indicator.value.indicator_code == '') {
    indicator.value.indicator_code = indicatorCode;
  }
  axios
    .post("/indicator/create", indicator.value)
    .then((response) => {
      console.log(response.data);
      isCreateModalOpen.value = false;
      router.push({ path: "/admin/indicators" });
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
              <TableCell>{{ indicator.indicator_code }}</TableCell>
              <TableCell>{{ indicator.indicator_name }}</TableCell>
              <TableCell>{{ indicator.indicator_description }}</TableCell>  
              <TableCell>{{ indicator.indicator_scope }}</TableCell>  
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
            <Input type="number" id="program_id" v-model="indicator.program_id" required />
          </div>
          <div class="flex flex-col gap-2">
            <label for="indicator_name">Indicator Name:</label>
            <Input type="text" id="indicator_name" placeholder="example: Nutrition" @input="generateIndicatorCode($event)"
              v-model="indicator.indicator_name" required />
          </div>
          <div class="flex flex-col gap-2">
            <label for="indicator_description">Indicator Description:</label>
            <Input type="text" id="indicator_description" placeholder="Description"
              v-model="indicator.indicator_description" />
          </div>
          <div class="flex flex-col gap-2">
            <label for="indicator_scope">Indicator Scope:</label>
            <Input type="text" id="indicator_scope" placeholder="Scope"
              v-model="indicator.indicator_scope" />
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
      <div class="w-full flex flex-col justify-start items-start gap-2 p-2 border">
        <form @submit.prevent="confirmEdit" class="flex flex-col gap-4">
          <div class="flex flex-col gap-2">
            <label for="indicator_code">Indicator Code:</label>
            <Input type="text" class="bg-slate-200" id="indicator_code" placeholder="NP-0001" v-model="toEdit.indicator_code" readonly />
          </div>
          <div class="flex flex-col gap-2">
            <label for="program_id">Program Id:</label>
            <Input type="number" id="program_id" v-model="toEdit.program_id" readonly />
          </div>
          <div class="flex flex-col gap-2">
            <label for="indicator_name">Indicator Name:</label>
            <Input type="text" id="indicator_name" placeholder="example: Nutrition" v-model="toEdit.indicator_name"
              required />
          </div>
          <div class="flex flex-col gap-2">
            <label for="indicator_description">Indicator Description:</label>
            <Input type="text" id="indicator_description" placeholder="Description" v-model="toEdit.indicator_description" />
          </div>
          <div class="flex flex-col gap-2">
            <label for="indicator_scope">Indicator Scope:</label>
            <Input type="text" id="indicator_scope" placeholder="Scope" v-model="toEdit.indicator_scope" />
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