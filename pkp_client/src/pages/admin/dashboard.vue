<script setup lang="ts">
    import SelectBarangay from '@/components/selections/SelectBarangay.vue';
    import { ref } from 'vue';
    import Button from '@/components/ui/button/Button.vue';

    import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxList } from "@/components/ui/combobox"
    const frameworks = [
        { value: "next.js", label: "Next.js" },
        { value: "sveltekit", label: "SvelteKit" },
        { value: "nuxt", label: "Nuxt" },
        { value: "remix", label: "Remix" },
        { value: "astro", label: "Astro" },
    ]

    const isSelectBarangayDrawerOpen = ref(false);
    const selectedBarangay = ref(null)

    function test(){
        console.log('asdasd')
    }
</script>

<template>
    <div class="w-full h-full flex flex-col justify-start items-start gap-2 p-2">
        <Button @click="isSelectBarangayDrawerOpen = true">Select Barangay</Button>
         <SelectBarangay v-model="selectedBarangay" v-model:open="isSelectBarangayDrawerOpen"/>

            <!-- <Combobox by="label" @update:model-value="test">
                <ComboboxAnchor>
                    <div class="relative w-full max-w-sm items-center">
                        <ComboboxInput class="pl-9" :display-value="(val) => val?.label ?? ''" placeholder="Select framework..." />
                        <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
                        <Search class="size-4 text-muted-foreground" />
                        </span>
                    </div>
                </ComboboxAnchor>

                <ComboboxList>
                <ComboboxEmpty>
                    No framework found.
                </ComboboxEmpty>

                <ComboboxGroup>
                    <ComboboxItem
                    v-for="framework in frameworks"
                    :key="framework.value"
                    :value="framework"
                    >
                    {{ framework.label }}
                    </ComboboxItem>
                </ComboboxGroup>
                </ComboboxList>
            </Combobox> -->
    </div>
</template>