<script setup>
import SidebarContent from './SidebarContent.vue';
import { defineProps } from 'vue';

defineProps({
    categories: {
        type: Object,
        default: {},
    },
});
</script>

<template>
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-primary-700 px-6 pb-4">
            <div class="flex h-16 shrink-0 items-center">
                <img
                    class="h-8 w-auto"
                    src="https://tailwindui.com/img/logos/mark.svg?color=white"
                    alt="Your Company"
                />
            </div>

            <SidebarContent :categories="categories"></SidebarContent>
        </div>
    </div>
</template>
