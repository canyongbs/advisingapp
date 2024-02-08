<script setup>
import { defineProps } from 'vue';

defineProps({
    categories: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <h3 class="text-xl">Help Center</h3>

    <div
        class="mt-4 divide-y divide-gray-100 overflow-hidden rounded-lg bg-gray-100 shadow sm:grid sm:grid-cols-2 sm:gap-px sm:divide-y-0"
    >
        <div
            v-for="(category, categoryId) in categories"
            :key="category.id"
            :class="[
                categoryId === 0 ? 'rounded-tl-lg rounded-tr-lg sm:rounded-tr-none' : '',
                categoryId === 1 ? 'sm:rounded-tr-lg' : '',
                categoryId === categories.length - 2 ? 'sm:rounded-bl-lg' : '',
                categoryId === categories.length - 1 ? 'rounded-bl-lg rounded-br-lg sm:rounded-bl-none' : '',
                'group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500',
            ]"
        >
            <div class="mt-8">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    <router-link :to="{ name: 'view-category', params: { categoryId: category.id } }">
                        <span class="absolute inset-0" aria-hidden="true" />
                        {{ category.name }}
                    </router-link>
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    {{ category.description }}
                </p>
            </div>
            <span
                class="pointer-events-none absolute right-6 top-6 text-gray-300 group-hover:text-primary-400"
                aria-hidden="true"
            >
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"
                    />
                </svg>
            </span>
        </div>
    </div>
</template>
