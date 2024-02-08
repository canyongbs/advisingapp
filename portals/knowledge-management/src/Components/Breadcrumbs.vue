<script setup>
import { ChevronRightIcon } from '@heroicons/vue/24/outline';
import { defineProps } from 'vue';

defineProps({
    currentCrumb: {
        type: String,
        required: true,
    },
    breadcrumbs: {
        type: Object,
        default: {},
    },
});
</script>

<template>
    <div class="flex flex-row text-gray-800 space-x-2 mt-4">
        <div v-for="crumb in breadcrumbs" :key="crumb.route" class="flex flex-row items-center space-x-2">
            <router-link :to="{ name: crumb.route }">
                <h3 class="text-md">{{ crumb.name }}</h3>
            </router-link>
            <ChevronRightIcon class="h-4 w-4" />
        </div>

        <h3 class="text-md font-semibold">{{ currentCrumb }}</h3>
    </div>
</template>
