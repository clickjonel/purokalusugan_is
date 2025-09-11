<script setup lang="ts">
    import { ref } from 'vue';
    import { Card,CardContent,CardDescription,CardFooter,CardHeader,CardTitle } from "@/components/ui/card";
    import { HousePlus,TriangleAlert  } from 'lucide-vue-next';
    import { Table,TableBody,TableCell,TableHead,TableHeader,TableRow } from '@/components/ui/table'

    function test(){
        console.log('asdasd')
    }
</script>

<template>
    <div class="w-full h-full flex flex-col justify-start items-start gap-2 p-2">
       
        <div class="w-full grid grid-cols-4 gap-4 p-2">
            <Card class="w-full">
                <CardHeader>
                    <CardTitle>
                        <div class="w-full flex justify-between items-center">
                            <span>Programs</span>
                            <HousePlus :size="14"/>
                        </div>
                </CardTitle>
                    <CardDescription>Current total of PK Programs</CardDescription>
                </CardHeader>
                <CardContent>
                    Content
                </CardContent>
            </Card>

            <Card class="w-full">
                <CardHeader>
                    <CardTitle>
                        <div class="w-full flex justify-between items-center">
                            <span>Indicators</span>
                            <TriangleAlert  :size="14"/>
                        </div>
                </CardTitle>
                    <CardDescription>Total number of Indicators for PK</CardDescription>
                </CardHeader>
                <CardContent>
                    Content
                </CardContent>
            </Card>

            <Card class="w-full">
                <CardHeader>
                    <CardTitle>
                        <div class="w-full flex justify-between items-center">
                            <span>PK Sites</span>
                            <span>icon</span>
                        </div>
                </CardTitle>
                    <CardDescription>Total PK Sites</CardDescription>
                </CardHeader>
                <CardContent>
                    Content
                </CardContent>
            </Card>

            <Card class="w-full">
                <CardHeader>
                    <CardTitle>
                        <div class="w-full flex justify-between items-center">
                            <span>HRH</span>
                            <span>icon</span>
                        </div>
                </CardTitle>
                    <CardDescription>Total HRH</CardDescription>
                </CardHeader>
                <CardContent>
                    Content
                </CardContent>
            </Card>
        </div>

        <div class="w-full flex flex-col justify-start items-start">
            <span class="text-lg uppercase font-semibold p-2">Program Indicators</span>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Indicator</TableHead>
                        <TableHead>Program</TableHead>
                        <TableHead>Scope</TableHead>
                        <TableHead>Disaggregation</TableHead>
                        <TableHead>Status</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <!-- <TableRow v-for="hrh in hrhList">
                        <TableCell>{{ hrh.first_name }}</TableCell>
                        <TableCell>{{ hrh.middle_name }}</TableCell>
                        <TableCell>{{ hrh.last_name }}</TableCell>
                        <TableCell>{{ hrh.user_level_name }}</TableCell>
                        <TableCell class="w-full flex justify-end items-center gap-2">
                            <Popover>
                                <PopoverTrigger asChild>
                                    <Button variant="ghost" size="icon" class="cursor-pointer">
                                        <EllipsisVertical class="h-4 w-4" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-45 p-2">
                                    <div class="flex flex-col">
                                        <Button @click="openAssignHrhModal(hrh)" variant="ghost" size="sm" class="justify-start text-xs">
                                            <UserRoundCog/> 
                                            Assign Team
                                        </Button>
                                        <Button @click="openAssignHrhModal(hrh)" variant="ghost" size="sm" class="justify-start text-xs">
                                            <UserRoundCog/> 
                                            Update Details
                                        </Button>
                                            <Button @click="openAssignHrhModal(hrh)" variant="ghost" size="sm" class="justify-start text-xs">
                                            <UserRoundCog/> 
                                            Update Credentials
                                        </Button>
                                        <Button @click="confirmDeactivateHrh(hrh.hrh_user_id)" variant="ghost" size="sm" class="justify-start text-xs text-red-600">
                                            <UserLock /> 
                                            Deactivate
                                        </Button>
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