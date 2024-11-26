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
import { consumer } from '@/Services/Consumer.js';
import { Bars3Icon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import { defineProps, ref, watch } from 'vue';
import HelpCenter from '../Components/HelpCenter.vue';
import SearchResults from '../Components/SearchResults.vue';

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

const searchQuery = ref(null);
const loadingResults = ref(false);
const searchResults = ref(null);

const debounceSearch = debounce((value) => {
    if (!value) {
        searchQuery.value = null;
        searchResults.value = null;
        return;
    }

    loadingResults.value = true;

    const { post } = consumer();

    post(props.searchUrl, {
        body: JSON.stringify({ search: value }),
    }).then((response) => {
        searchResults.value = response.data;
        loadingResults.value = false;
    });
}, 500);

watch(searchQuery, (value) => {
    debounceSearch(value);
});

function debounce(func, delay) {
    let timerId;
    return function (...args) {
        if (timerId) {
            clearTimeout(timerId);
        }
        timerId = setTimeout(() => {
            func(...args);
        }, delay);
    };
}
</script>

<template>
    <div class="top-0 z-40 flex flex-col items-center bg-gray-50">
        <button class="w-full p-3 lg:hidden" type="button" @click="$emit('sidebarOpened')">
            <span class="sr-only">Open sidebar</span>

            <Bars3Icon class="h-6 w-6 text-gray-900"></Bars3Icon>
        </button>

        <div class="bg-gradient-to-br from-primary-500 to-primary-800 w-full px-6">
            <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                <div class="flex flex-col gap-y-1 text-left">
                    <h3 class="text-3xl font-semibold text-white">Need help?</h3>
                    <p class="text-primary-100">Search our resource hub for advice and answers</p>
                </div>

                <form action="#" method="GET">
                    <label for="search" class="sr-only">Search</label>

                    <div class="relative rounded">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </div>

                        <input
                            type="search"
                            v-model="searchQuery"
                            id="search"
                            placeholder="Search for articles and categories"
                            class="block w-full rounded border-0 py-3 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-2-- sm:text-sm sm:leading-6"
                        />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <main class="px-6">
        <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
            <SearchResults
                v-if="searchQuery"
                :searchQuery="searchQuery"
                :searchResults="searchResults"
                :loadingResults="loadingResults"
            ></SearchResults>

            <HelpCenter v-else :categories="categories"></HelpCenter>
        </div>
    </main>
</template>
