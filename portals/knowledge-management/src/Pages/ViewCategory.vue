<!--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
import { defineProps, ref, watch, onMounted } from 'vue';
import { useRoute, onBeforeRouteUpdate } from 'vue-router';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import AppLoading from '@/Components/AppLoading.vue';
import { consumer } from '@/Services/Consumer.js';
import { Bars3Icon } from "@heroicons/vue/24/outline/index.js";
import { ChevronRightIcon, XMarkIcon, ChevronLeftIcon } from '@heroicons/vue/20/solid/index.js';

const route = useRoute();

const props = defineProps({
    searchUrl: {
        type: String,
        required: true,
    },
    apiUrl: {
        type: String,
        required: true,
    },
    categories: {
        type: Object,
        required: true,
    },
});

const loadingResults = ref(true);
const category = ref(null);
const articles = ref(null);
const currentPage = ref(1);
const nextPageUrl = ref(null);
const prevPageUrl = ref(null);
const lastPage = ref(null);
const totalArticles = ref(0);
const fromArticle = ref(0);
const toArticle = ref(0);

watch(
    route,
    async function (newRouteValue) {
        await getData();
    },
    {
        immediate: true,
    },
);

const fetchNextPage = () => {
    currentPage.value = currentPage.value !== lastPage.value ? currentPage.value + 1 : lastPage.value;
    getData(currentPage.value);
};
const fetchPreviousPage = () => {
    currentPage.value = currentPage.value !== 1 ? currentPage.value - 1 : 1;
    getData(currentPage.value);
};
const fetchPage = (page) => {
    currentPage.value = page;
    getData(currentPage.value);
};
const visiblePages = () => {
    const range = 2;
    const start = Math.max(currentPage.value - range, 1);
    const end = Math.min(currentPage.value + range, lastPage.value);
    return Array.from({ length: end - start + 1 }, (_, i) => i + start);
};

// onMounted(async function () {
//     await getData();
// });

async function getData(page = 1) {
    loadingResults.value = true;

    const { get } = consumer();

    await get(props.apiUrl + '/categories/' + route.params.categoryId, { page: page }).then((response) => {
        category.value = response.data.category;
        articles.value = response.data.articles.data;
        currentPage.value = response.data.articles.current_page;
        prevPageUrl.value = response.data.articles.prev_page_url;
        nextPageUrl.value = response.data.articles.next_page_url;
        lastPage.value = response.data.articles.last_page;
        totalArticles.value = response.data.articles.total;
        fromArticle.value = response.data.articles.from;
        toArticle.value = response.data.articles.to;
        loadingResults.value = false;
    });
}
</script>

<template>
    <div class="sticky top-0 z-40 flex flex-col items-center bg-gray-50 ">
        <button class="w-full p-3 lg:hidden" type="button" @click="$emit('sidebarOpened')">
            <span class="sr-only">Open sidebar</span>

            <Bars3Icon class="h-6 w-6 text-gray-900"></Bars3Icon>
        </button>

        <div class="w-full px-6">
            <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                <div v-if="loadingResults">
                    <AppLoading />
                </div>
                <div v-else>
                    <main class="flex flex-col gap-8">
                        <Breadcrumbs currentCrumb="Categories"></Breadcrumbs>

                        <div class="flex flex-col gap-6">
                            <h2 class="text-2xl font-bold text-primary-950">
                                {{ category.name }}
                            </h2>

                            <div
                                class="flex flex-col divide-y ring-1 ring-black/5 shadow-sm px-3 pt-3 pb-1 rounded bg-white">
                                <h3 class="text-lg font-semibold text-gray-800 px-3 pt-1 pb-3">Articles</h3>

                                <div v-if="articles.length > 0">
                                    <ul role="list" class="divide-y">
                                        <li v-for="article in articles" :key="article.id">
                                            <router-link :to="{
                                                name: 'view-article',
                                                params: { categoryId: article.categoryId, articleId: article.id },
                                            }" class="group p-3 flex items-start text-sm font-medium text-gray-700">
                                                <h4>
                                                    {{ article.name }}
                                                </h4>

                                                <ChevronRightIcon
                                                    class="opacity-0 h-5 w-5 text-primary-600 transition-all group-hover:translate-x-2 group-hover:opacity-100" />
                                            </router-link>
                                        </li>
                                    </ul>
                                    <div
                                        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                                        <div class="flex flex-1 justify-between sm:hidden">
                                            <button type="button"
                                                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                :disabled="currentPage === 1" @click="fetchPreviousPage">
                                                Previous
                                            </button>
                                            <button type="button"
                                                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                :disabled="currentPage === lastPage" @click="fetchNextPage">
                                                Next
                                            </button>
                                        </div>
                                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                            <div>
                                                <p class="text-sm text-gray-700">
                                                    Showing
                                                    {{ ' ' }}
                                                    <span class="font-medium">{{ fromArticle }}</span>
                                                    {{ ' ' }}
                                                    to
                                                    {{ ' ' }}
                                                    <span class="font-medium">{{ toArticle }}</span>
                                                    {{ ' ' }}
                                                    of
                                                    {{ ' ' }}
                                                    <span class="font-medium">{{ totalArticles }}</span>
                                                    {{ ' ' }}
                                                    results
                                                </p>
                                            </div>
                                            <div>
                                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                                    aria-label="Pagination">
                                                    <button type="button"
                                                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                                        :disabled="currentPage === 1" @click="fetchPreviousPage">
                                                        <span class="sr-only">Previous</span>
                                                        <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                                                    </button>

                                                    <!-- First Page Button -->
                                                    <button v-if="currentPage > 4"
                                                        class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                        :class="currentPage === 1
                                                            ? 'bg-indigo-600 text-white'
                                                            : 'bg-white-500 text-black border border-gray-300'
                                                            " @click="fetchPage(1)">
                                                        1
                                                    </button>
                                                    <span v-if="currentPage > 4">...</span>

                                                    <!-- Page Numbers -->
                                                    <button v-for="page in visiblePages()" :key="page"
                                                        @click="fetchPage(page)"
                                                        aria-current="page {{ page }} {{ currentPage }}"
                                                        class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                        :class="page === currentPage
                                                            ? 'bg-indigo-600 text-white'
                                                            : 'bg-white-500 text-black border border-gray-300'
                                                            " :disabled="page === currentPage">
                                                        {{ page }}
                                                    </button>

                                                    <span v-if="currentPage < lastPage - 3">...</span>
                                                    <button v-if="currentPage < lastPage - 3"
                                                        class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                        :class="currentPage === lastPage
                                                            ? 'bg-indigo-600 text-white'
                                                            : 'bg-white-500 text-black border border-gray-300'
                                                            " @click="fetchPage(lastPage)">
                                                        {{ lastPage }}
                                                    </button>

                                                    <button type="button"
                                                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                                        :disabled="currentPage === lastPage" @click="fetchNextPage">
                                                        <span class="sr-only">Next </span>
                                                        <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                                                    </button>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="p-3 flex items-start gap-2">
                                    <XMarkIcon class="h-5 w-5 text-gray-400" />

                                    <p class="text-gray-600 text-sm font-medium">
                                        No articles found in this category.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
