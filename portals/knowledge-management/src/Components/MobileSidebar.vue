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
    <div class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/80"></div>
        <div class="fixed inset-0 flex">
            <div class="relative mr-16 flex w-full max-w-xs flex-1">
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" class="-m-2.5 p-2.5" @click="$emit('sidebarClosed')">
                        <span class="sr-only">Close sidebar</span>
                        <svg
                            class="h-6 w-6 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

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
        </div>
    </div>
</template>
