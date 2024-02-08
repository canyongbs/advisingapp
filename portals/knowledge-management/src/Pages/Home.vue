<script setup>
import { Bars3Icon } from '@heroicons/vue/24/outline';
import HelpCenter from '../Components/HelpCenter.vue';
import SearchResults from '../Components/SearchResults.vue';
import { defineProps, ref, watch } from 'vue';

const props = defineProps({
    searchUrl: {
        type: String,
        required: true,
    },
    categories: {
        type: Object,
        required: true,
    },
});

const urlParams = new URLSearchParams(window.location.search);
const searchParameter = urlParams.get('q');
const searchQuery = ref(null);
const loadingResults = ref(false);

const debounceSearch = debounce((value) => {
    if (!value) {
        searchQuery.value = null;
        searchResults.value = null;
        return;
    }

    loadingResults.value = true;

    fetch(props.searchUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ search: value }),
    })
        .then((response) => response.json())
        .then((json) => {
            searchResults.value = json;
            loadingResults.value = false;
        });
}, 500);

watch(searchQuery, (value) => {
    debounceSearch(value);
});

const searchResults = ref(null);

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
    <div
        class="sticky top-0 z-40 flex flex-col items-center border-b border-gray-100 bg-white px-4 py-4 shadow-sm sm:px-6 lg:px-8"
    >
        <button class="w-full p-2.5 lg:hidden" type="button" v-on:click="showMobileMenu = !showMobileMenu">
            <span class="sr-only">Open sidebar</span>
            <Bars3Icon class="h-6 w-6 text-gray-900"></Bars3Icon>
        </button>

        <div class="flex h-full w-full flex-col rounded bg-primary-700 px-12 py-4">
            <div class="flex flex-col text-left">
                <h3 class="text-3xl text-white">Need help?</h3>
                <p class="text-white">Search our knowledge base for advice and answers</p>
            </div>

            <form class="relative mt-2 flex h-12" action="#" method="GET">
                <label class="sr-only" for="search-field">Search</label>
                <input
                    class="block h-full w-full text-gray-900 border-0 py-0 pl-8 pr-0 placeholder:text-gray-400 focus:ring-0 sm:text-sm rounded"
                    id="search-field"
                    v-model="searchQuery"
                    name="search"
                    type="search"
                    placeholder="Search for articles and categories"
                />
            </form>
        </div>
    </div>

    <main class="py-10">
        <SearchResults
            v-if="searchQuery"
            :searchQuery="searchQuery"
            :searchResults="searchResults"
            :loadingResults="loadingResults"
        ></SearchResults>

        <HelpCenter v-else :categories="categories"></HelpCenter>
    </main>
</template>
