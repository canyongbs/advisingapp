<!--
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
import { defineProps } from 'vue';
import SearchLoading from '@/Components/SearchLoading.vue';

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
    <h3 class="text-xl">Search Results for {{ searchQuery }}</h3>

    <div class="flex flex-col space-y-6 mt-6">
        <div v-if="loadingResults">
            <SearchLoading />
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
                            <router-link
                                :to="{
                                    name: 'view-article',
                                    params: { categoryId: article.categoryId, articleId: article.id },
                                }"
                            >
                                <h5 class="text-md text-gray-800">{{ article.name }}</h5>
                            </router-link>
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
                            <router-link :to="{ name: 'view-category', params: { categoryId: category.id } }">
                                <h5 class="text-md text-gray-800">{{ category.name }}</h5>
                            </router-link>
                        </li>
                    </ul>
                </div>
                <div v-else>
                    <p class="text-gray-500 mt-2">No categories found for "{{ searchQuery }}"</p>
                </div>
            </div>
        </div>
    </div>
</template>
