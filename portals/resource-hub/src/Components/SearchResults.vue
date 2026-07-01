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
    import SearchLoading from '@common/portal/home/SearchLoading.vue';
    import { DocumentTextIcon, FolderIcon } from '@heroicons/vue/24/outline';
    import { defineProps } from 'vue';
    import Article from './Article.vue';
    import Pagination from './Pagination.vue';
    import ResourceList from './ResourceList.vue';
    import ResourceListItem from './ResourceListItem.vue';
    import Subheading from '@common/portal/Subheading.vue';
    import Tabs from './Tabs.vue';

    const emit = defineEmits(['fetchNextPage', 'fetchPreviousPage', 'fetchPage', 'change-filter']);

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
        selectedFilter: {
            type: String,
            default: '',
        },
        currentPage: {
            type: Number,
            required: true,
        },
        lastPage: {
            type: Number,
            required: true,
        },
        fromItem: {
            type: Number,
            required: true,
        },
        toItem: {
            type: Number,
            required: true,
        },
        totalItems: {
            type: Number,
            required: true,
        },
    });

    const filterTabs = [
        { label: 'All Articles', value: 'all-articles' },
        { label: 'Featured', value: 'featured' },
        { label: 'Most Viewed', value: 'most-viewed' },
    ];

    const updateFilter = (value) => {
        emit('change-filter', value);
    };

    function fetchNextPage() {
        emit('fetchNextPage');
    }
    function fetchPreviousPage() {
        emit('fetchPreviousPage');
    }
    function fetchPage(page) {
        emit('fetchPage', page);
    }
</script>

<template>
    <div v-if="loadingResults">
        <SearchLoading />
    </div>

    <div v-if="!loadingResults && searchResults?.data" class="flex flex-col gap-6">
        <Subheading>
            Search results<template v-if="searchQuery">
                for <span class="text-gray-500">&ldquo;{{ searchQuery }}&rdquo;</span></template
            >
        </Subheading>

        <div class="flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <Tabs
                :tabs="filterTabs"
                :modelValue="selectedFilter || 'all-articles'"
                @update:modelValue="updateFilter"
                :contained="true"
            />

            <div v-if="searchResults.data.articles.data.length > 0">
                <ul role="list" class="divide-y">
                    <li v-for="article in searchResults.data.articles.data" :key="article.id">
                        <Article :article="article" />
                    </li>
                </ul>
                <Pagination
                    :currentPage="currentPage"
                    :lastPage="lastPage"
                    :fromItem="fromItem"
                    :toItem="toItem"
                    :totalItems="totalItems"
                    @fetchNextPage="fetchNextPage"
                    @fetchPreviousPage="fetchPreviousPage"
                    @fetchPage="fetchPage"
                />
            </div>

            <section v-else class="px-6 py-4 flex items-start gap-x-4">
                <div class="flex size-12 items-center justify-center rounded-full bg-gray-100">
                    <DocumentTextIcon class="size-6 text-gray-400" />
                </div>

                <div class="flex-1">
                    <h4 class="text-base font-semibold leading-6 text-gray-950">No articles found</h4>

                    <p class="mt-1 text-sm text-gray-500">No articles match your current search criteria.</p>
                </div>
            </section>
        </div>

        <ResourceList v-if="searchResults.data.categories.length > 0" heading="Categories">
            <ResourceListItem
                v-for="category in searchResults.data.categories"
                :key="category.id"
                :to="{ name: 'view-category', params: { categoryId: category.id } }"
            >
                <template #primary>{{ category.name }}</template>
            </ResourceListItem>
        </ResourceList>

        <section v-else class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 px-6 py-4 flex items-start gap-x-4">
            <div class="flex size-12 items-center justify-center rounded-full bg-gray-100">
                <FolderIcon class="size-6 text-gray-400" />
            </div>

            <div class="flex-1">
                <h4 class="text-base font-semibold leading-6 text-gray-950">No categories found</h4>

                <p class="mt-1 text-sm text-gray-500">No categories match your current search criteria.</p>
            </div>
        </section>
    </div>
</template>
