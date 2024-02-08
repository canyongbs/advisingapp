<script setup>
import { defineProps } from 'vue';

defineProps({
    searchQuery: {
        type: String,
        required: true,
    },
    searchResults: {
        type: Object,
        required: true,
    },
    loadingResults: {
        type: Boolean,
        required: true,
    },
});
</script>

<template>
    <div class="px-4 sm:px-6 lg:px-8">
        <h3 class="text-xl">Search Results for {{ searchQuery }}</h3>

        <div class="flex flex-col space-y-6 mt-6">
            <div v-if="loadingResults">
                <p>Loading results...</p>
            </div>
            <div v-if="!loadingResults && searchResults && searchResults.data" class="flex flex-col space-y-4">
                <div class="border border-gray-200 rounded p-4 shadow">
                    <h4 class="text-lg font-bold text-gray-900">Articles</h4>
                    <div v-if="searchResults.data.articles.length > 0">
                        <ul role="list" class="divide-y divide-gray-200 mt-2">
                            <li
                                v-for="article in searchResults.data.articles"
                                :key="article.id"
                                class="py-4 border-gray-200"
                            >
                                <a :href="article.id" class="block">
                                    <h5 class="text-md text-gray-800">{{ article.name }}</h5>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <p class="text-gray-500 mt-2">No articles found for "{{ searchQuery }}"</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded p-4 shadow">
                    <h4 class="text-lg font-bold text-gray-900">Categories</h4>
                    <div v-if="searchResults.data.categories.length > 0">
                        <ul role="list" class="divide-y divide-gray-200 mt-2">
                            <li
                                v-for="category in searchResults.data.categories"
                                :key="category.id"
                                class="py-4 border-gray-200"
                            >
                                <a :href="category.id" class="block">
                                    <h5 class="text-md text-gray-800">{{ category.name }}</h5>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <p class="text-gray-500 mt-2">No categories found for "{{ searchQuery }}"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
