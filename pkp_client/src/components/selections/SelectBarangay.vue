<script setup lang="ts">
    import { ref } from 'vue';
    import {
        Drawer,
        DrawerClose,
        DrawerContent,
        DrawerDescription,
        DrawerFooter,
        DrawerHeader,
        DrawerTitle,
    } from '@/components/ui/drawer'
    import Button from '../ui/button/Button.vue';

    const isDrawerOpen = defineModel<boolean>('open');
    const model = defineModel();

</script>


<template>
    <Drawer v-model:open="isDrawerOpen">
        <DrawerContent>
            <DrawerHeader>
                <DrawerTitle>Select Barangay</DrawerTitle>
                <DrawerDescription>Select Barangay</DrawerDescription>
            </DrawerHeader>
            <DrawerFooter>
                <Button>Submit</Button>
                <DrawerClose>
                    <Button variant="outline">
                        Cancel
                    </Button>
                </DrawerClose>
            </DrawerFooter>
        </DrawerContent>
    </Drawer>
</template>