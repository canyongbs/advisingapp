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
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import Loading from './Components/Loading.vue';
import MobileSidebar from './Components/MobileSidebar.vue';
import DesktopSidebar from './Components/DesktopSidebar.vue';
import { Bars3Icon } from '@heroicons/vue/24/outline';
import axios from 'axios';

const loading = ref(true);
const showMobileMenu = ref(false);

onMounted(async () => {
    await getKnowledgeManagementPortal().then(() => {
        loading.value = false;
    });
});

const props = defineProps(['url']);

const submittedSuccess = ref(false);

const scriptUrl = new URL(document.currentScript.getAttribute('src'));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const scriptQuery = Object.fromEntries(scriptUrl.searchParams);

const urlParams = new URLSearchParams(window.location.search);
const searchParameter = urlParams.get('q');
const searchQuery = ref(searchParameter ?? null);

const hostUrl = `${protocol}//${scriptHostname}`;

watch(searchQuery, (value) => {
    console.log('searchQuery', value);
    console.log('scriptUrl', scriptUrl);
    console.log('protocol', protocol);
    console.log('scriptHostname', scriptHostname);
    console.log('scriptQuery', scriptQuery);

    axios.post(hostUrl + '/graphql', {
        query: '{ language }',
    });
});

const portalPrimaryColor = ref('');
const portalRounding = ref('');
const categories = ref({});

async function getKnowledgeManagementPortal() {
    await fetch(props.url)
        .then((response) => response.json())
        .then((json) => {
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
                        <div v-if="results">Here are your results...</div>
                        <div class="px-4 sm:px-6 lg:px-8" v-else>
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
                                        categoryId === categories.length - 1
                                            ? 'rounded-bl-lg rounded-br-lg sm:rounded-bl-none'
                                            : '',
                                        'group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500',
                                    ]"
                                >
                                    <div class="mt-8">
                                        <h3 class="text-base font-semibold leading-6 text-gray-900">
                                            <a class="focus:outline-none" :href="category.name">
                                                <span class="absolute inset-0" aria-hidden="true" />
                                                {{ category.name }}
                                            </a>
                                        </h3>
                                        <p class="mt-2 text-sm text-gray-500">
                                            Doloribus dolores nostrum quia qui natus officia quod et dolorem. Sit
                                            repellendus qui ut at blanditiis et quo et molestiae.
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
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
