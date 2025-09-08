<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "@/axios/axios";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Search } from "lucide-vue-next";
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
import { useRouter } from "vue-router";
import { toast } from "vue-sonner";



const router = useRouter();
const programs = ref([]);
const currentList=ref([]);
let searchKeyword = ref("");
let errorDetail = ref("");
let isCreateModalOpen = ref(false);
let isDeleteModalOpen = ref(false);
let programIdToDelete = ref(0);
let isEditModalOpen = ref(false);
let programToEdit = ref<Program>({
  program_name: "",
  program_code: "",
  program_status: true,
});

interface Program {
  program_name: string;
  program_code: string;
  program_status: boolean;
}

const program = ref<Program>({
  program_name: "",
  program_code: "",
  program_status: true,
});

function search() {
  const searchTerm = searchKeyword.value.toLowerCase();  
  const searchedData = programs.value.filter(program => {
    const programName = program.program_name.toLowerCase();
    return programName.includes(searchTerm);
  });
  currentList.value = searchedData;  
}

function handleClose() {
  isCreateModalOpen.value = false;
}

function explainError(error) {
  if (error.includes("Integrity constraint violation")) {
    errorDetail.value =
      "The program code you entered already exists. Please enter another!";
  }
  return errorDetail;
}

function handleCreate() {
  axios
    .post("/program/create", program.value)
    .then((response) => {
      console.log(response.data);
      isCreateModalOpen.value = false;
      router.push({ path: "/programs" });
      toast("Program created successfully!", {
        description: response.data.message,
        action: {
          label: "Close",
          onClick: () => toast.dismiss(),
        },
      });
      fetchPrograms();
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
    .finally(() => {});
}

function handleDelete(programId: number) {
  programIdToDelete.value = programId;
  isDeleteModalOpen.value = true;
}

function confirmDelete() {
  axios
    .delete("/program/delete", {
      data: {
        program_id: programIdToDelete.value,
      },
    })
    .then((response) => {
      console.log(response.data);
      toast("Program deleted successfully!", {
        description: response.data.message,
        action: {
          label: "Close",
          onClick: () => toast.dismiss(),
        },
      });
      fetchPrograms();
      isDeleteModalOpen.value = false;
    })
    .catch((error) => {
      console.error(error.response.data);
      if (error.response) {
        toast("Failed to delete program", {
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

function handleEdit(program: Program) {
  programToEdit.value = { ...program };
  isEditModalOpen.value = true;
}

function confirmEdit() {
  axios
    .put("/program/update", {
      program_id: programToEdit.value.program_id,
      program_name: programToEdit.value.program_name,
      program_code: programToEdit.value.program_code,
      program_status: programToEdit.value.program_status,
    })
    .then((response) => {
      console.log(response.data);
      toast("Program updated successfully!", {
        description: response.data.message,
        action: {
          label: "Close",
          onClick: () => toast.dismiss(),
        },
      });
      fetchPrograms();
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

const fetchPrograms = () => {
  axios
    .get("/program/list")
    .then((response) => {
      programs.value = response.data.data;
      currentList.value = response.data.data;
      console.log("programs", programs.value);
    })
    .catch((error) => {
      console.error("Error fetching programs:", error);
    });
};

onMounted(() => {
  fetchPrograms();
});

</script>
<template>
  <div class="w-full h-full flex flex-col justify-between items-start gap-2 p-2">
    <!-- header -->
    <div class="w-full flex justify-between items-center p-2 border">
      <div class="relative items-center">
        <Input
          v-model="searchKeyword"
          id="search"
          type="text"
          placeholder="Search Program Name"
          class="pl-8"
          @input="search"
        />
        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
          <Search class="size-4 text-muted-foreground" />
        </span>
      </div>
      <Button
        @click="isCreateModalOpen = true"
        variant="default"
        class="cursor-pointer"
        size="sm"
        >Create</Button
      >
    </div>

    <!-- table -->
    <div class="w-full h-full flex flex-col jusitfy-start items-start border">
      <div class="w-full h-[600px] overflow-y-scroll">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Program Id</TableHead>
              <TableHead>Program Code</TableHead>
              <TableHead>Program Name</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="program in currentList">
              <TableCell>{{ program.program_id }}</TableCell>
              <TableCell>{{ program.program_code }}</TableCell>
              <TableCell>{{ program.program_name }}</TableCell>
              <TableCell class="w-full flex justify-start items-center gap-2">
                <Button variant="outline" size="sm" class="cursor-pointer text-xs" @click="handleEdit(program)">Edit</Button>
                <Button
                  variant="outline"
                  size="sm"
                  class="cursor-pointer text-xs"
                  @click="handleDelete(program.program_id)"
                  >Delete</Button
                >
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
    <DialogContent class="font-poppins w-[20rem] md:max-w-[20rem] lg:max-w-[50rem]">
      <DialogHeader>
        <DialogTitle class="">Create an Indicator</DialogTitle>
        <DialogDescription>This is the Indicators management. </DialogDescription>
      </DialogHeader>

      <div class="w-full flex flex-col justify-start items-start gap-2 p-2 border">
        <form @submit.prevent="handleCreate" class="flex flex-col gap-4">
          <div class="flex flex-col gap-2">
            <label for="indicator_code">Program Code:</label>
            <Input
              type="text"
              id="indicator_code"
              placeholder="NP-0001"
              v-model="program.program_code"
              required
            />
          </div>
          <div class="flex flex-col gap-2">
            <label for="program_id">Program Name:</label>
            <Input
              type="text"
              id="program_id"
              placeholder="example: Nutrition"
              v-model="program.program_name"
              required
            />
          </div>
        </form>
      </div>

      <DialogFooter>
        <Button
          type="submit"
          class="cursor-pointer hover:bg-red-500"
          @click="handleClose()"
          >Cancel</Button
        >
        <Button
          type="submit"
          class="cursor-pointer hover:bg-emerald-500 hover:text-black"
          @click="handleCreate"
          >Submit</Button
        >
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
      <Button type="button" class="cursor-pointer bg-red-500 hover:bg-red-700 text-white" @click="confirmDelete">Delete</Button>
    </DialogFooter>
  </DialogContent>
</Dialog>

<Dialog v-model:open="isEditModalOpen">
  <DialogContent class="font-poppins w-[20rem] md:max-w-[20rem] lg:max-w-[50rem]">
    <DialogHeader>
      <DialogTitle>Edit Program</DialogTitle>
      <DialogDescription>Update the program details.</DialogDescription>
    </DialogHeader>
    <div class="w-full flex flex-col justify-start items-start gap-2 p-2 border">
      <form @submit.prevent="confirmEdit" class="flex flex-col gap-4">
        <div class="flex flex-col gap-2">
          <label for="program_code">Program Code:</label>
          <Input type="text" class="bg-slate-200"id="program_code" placeholder="NP-0001" v-model="programToEdit.program_code" readonly />
        </div>
        <div class="flex flex-col gap-2">
          <label for="program_name">Program Name:</label>
          <Input type="text" id="program_name" placeholder="example: Nutrition" v-model="programToEdit.program_name" required />
        </div>
        <!-- Add more fields as needed -->
      </form>
    </div>
    <DialogFooter>
      <Button type="button" class="cursor-pointer" @click="isEditModalOpen = false">Cancel</Button>
      <Button type="button" class="cursor-pointer bg-blue-500 hover:bg-blue-700 text-white" @click="confirmEdit">Save</Button>
    </DialogFooter>
  </DialogContent>
</Dialog>
</template>
