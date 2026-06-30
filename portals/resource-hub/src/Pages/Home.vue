<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
    import { consumer } from '@/Services/Consumer.js';
    import HelpCenter from '@common-components/portal/home/HelpCenter.vue';
    import HeroSearch from '@common-components/portal/home/HeroSearch.vue';
    import SearchResults from '@common-components/portal/home/SearchResults.vue';
    import { defineProps, ref, watch } from 'vue';

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

        const { post } = consumer();

        loadingResults.value = true;

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
    <div class="flex flex-col bg-gray-50">
        <HeroSearch v-model="searchQuery" @sidebar-opened="$emit('sidebarOpened')"></HeroSearch>
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
    </div>
</template>
