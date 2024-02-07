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
import { defineProps, onMounted, ref, watch } from 'vue';
import attachRecaptchaScript from '../../../app-modules/integration-google-recaptcha/resources/js/Services/AttachRecaptchaScript.js';
import getRecaptchaToken from '../../../app-modules/integration-google-recaptcha/resources/js/Services/GetRecaptchaToken.js';
import Loading from './Components/Loading.vue';
import MobileSidebar from './Components/MobileSidebar.vue';
import HelpCenter from './Components/HelpCenter.vue';
import SearchResults from './Components/SearchResults.vue';
import DesktopSidebar from './Components/DesktopSidebar.vue';
import { Bars3Icon } from '@heroicons/vue/24/outline';

const errorLoading = ref(false);
const loading = ref(true);
const loadingResults = ref(false);
const showMobileMenu = ref(false);

onMounted(async () => {
    await getKnowledgeManagementPortal().then(() => {
        loading.value = false;
    });
});

const props = defineProps({
    url: {
        type: String,
        required: true,
    },
    searchUrl: {
        type: String,
        required: true,
    },
});

const scriptUrl = new URL(document.currentScript.getAttribute('src'));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const scriptQuery = Object.fromEntries(scriptUrl.searchParams);

const urlParams = new URLSearchParams(window.location.search);
const searchParameter = urlParams.get('q');
const searchQuery = ref(searchParameter ?? null);

const hostUrl = `${protocol}//${scriptHostname}`;

const searchResults = ref(null);

watch(searchQuery, (value) => {
    loadingResults.value = true;

    if (!value) {
        searchQuery.value = null;
        searchResults.value = null;
        return;
    }

    fetch(props.searchUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ search: searchQuery.value }),
    })
        .then((response) => response.json())
        .then((json) => {
            console.log('search results', json);
            searchResults.value = json;
            loadingResults.value = false;
        });
});

const portalPrimaryColor = ref('');
const portalRounding = ref('');
const categories = ref({});

async function getKnowledgeManagementPortal() {
    await fetch(props.url)
        .then((response) => response.json())
        .then((json) => {
            errorLoading.value = false;

            if (json.error) {
                throw new Error(json.error);
            }

            console.log('getKnowledgeManagementPortal', json);

            categories.value = json.categories;
            console.log('categories', categories.value);

            portalPrimaryColor.value = json.primary_color;

            portalRounding.value = {
                none: {
                    sm: '0px',
                    default: '0px',
                    md: '0px',
                    lg: '0px',
                    full: '0px',
                },
                sm: {
                    sm: '0.125rem',
                    default: '0.25rem',
                    md: '0.375rem',
                    lg: '0.5rem',
                    full: '9999px',
                },
                md: {
                    sm: '0.25rem',
                    default: '0.375rem',
                    md: '0.5rem',
                    lg: '0.75rem',
                    full: '9999px',
                },
                lg: {
                    sm: '0.375rem',
                    default: '0.5rem',
                    md: '0.75rem',
                    lg: '1rem',
                    full: '9999px',
                },
                full: {
                    sm: '9999px',
                    default: '9999px',
                    md: '9999px',
                    lg: '9999px',
                    full: '9999px',
                },
            }[json.rounding ?? 'md'];
        })
        .catch((error) => {
            errorLoading.value = true;
            console.error(`Knowledge Management Portal Embed ${error}`);
        });
}
</script>

<template>
    <div
        class="font-sans"
        :style="{
            '--primary-50': portalPrimaryColor[50],
            '--primary-100': portalPrimaryColor[100],
            '--primary-200': portalPrimaryColor[200],
            '--primary-300': portalPrimaryColor[300],
            '--primary-400': portalPrimaryColor[400],
            '--primary-500': portalPrimaryColor[500],
            '--primary-600': portalPrimaryColor[600],
            '--primary-700': portalPrimaryColor[700],
            '--primary-800': portalPrimaryColor[800],
            '--primary-900': portalPrimaryColor[900],
            '--rounding-sm': portalRounding.sm,
            '--rounding': portalRounding.default,
            '--rounding-md': portalRounding.md,
            '--rounding-lg': portalRounding.lg,
            '--rounding-full': portalRounding.full,
        }"
    >
        <div>
            <link rel="stylesheet" v-bind:href="hostUrl + '/js/portals/knowledge-management/style.css'" />

            <div v-if="loading">
                <Loading></Loading>
            </div>

            <div v-else>
                <div v-if="errorLoading" class="text-center">
                    <h1 class="text-3xl font-bold text-red-500">Error Loading Portal</h1>
                    <p class="text-lg text-red-500">Please try again later</p>
                </div>

                <div v-else>
                    <MobileSidebar
                        v-if="showMobileMenu"
                        @sidebar-closed="showMobileMenu = !showMobileMenu"
                        :categories="categories"
                    ></MobileSidebar>

                    <!-- Desktop Sidebar -->
                    <DesktopSidebar :categories="categories"></DesktopSidebar>

                    <div class="lg:pl-72">
                        <div
                            class="sticky top-0 z-40 flex flex-col items-center border-b border-gray-100 bg-white px-4 py-4 shadow-sm sm:px-6 lg:px-8"
                        >
                            <button
                                class="w-full p-2.5 lg:hidden"
                                type="button"
                                v-on:click="showMobileMenu = !showMobileMenu"
                            >
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
                                v-if="searchResults"
                                :searchQuery="searchQuery"
                                :searchResults="searchResults"
                            ></SearchResults>

                            <HelpCenter v-else :categories="categories"></HelpCenter>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
