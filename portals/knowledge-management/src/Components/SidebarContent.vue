<script setup>
import { defineProps } from 'vue';

defineProps({
    categories: {
        type: Object,
        default: {},
    },
});
</script>

<template>
    <nav class="flex flex-1 flex-col">
        <div class="flex justify-center">
            <router-link :to="{ name: 'home' }">
                <h3 class="text-xl text-white">Help Center</h3>
            </router-link>
        </div>

        <ul role="list" class="flex flex-1 flex-col gap-y-7 mt-4">
            <li v-for="category in categories" :key="category.id">
                <div
                    class="bg-gray-100 text-gray-800 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                >
                    <router-link
                        :to="{ name: 'view-category', params: { categoryId: category.id } }"
                        active-class="font-bold"
                    >
                        {{ category.name }}
                    </router-link>
                </div>
            </li>
        </ul>
    </nav>
</template>
