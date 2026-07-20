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
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import Article from '@common/portal/category/Article.vue';
    import HeroSearch from '@common/portal/HeroSearch.vue';
    import Page from '@common/portal/Page.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import SearchResults from '@common/portal/SearchResults.vue';
    import Subheading from '@common/portal/Subheading.vue';
    import Tabs from '@common/portal/Tabs.vue';
    import { DocumentTextIcon } from '@heroicons/vue/24/outline';
    import { useQuery } from '@pinia/colada';
    import { computed, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { useResourceHubSearch } from '../Composables/useResourceHubSearch.js';
    import { apiGet } from '../Services/api.js';
    import { useCategoryData } from './loaders.js';

    const route = useRoute();
    const router = useRouter();

    const {
        searchQuery,
        loadingResults,
        globalSearchInput,
        selectedTags,
        tagsArray,
        toggleTag,
        searchResultArticles,
        searchResultCategories,
        totalArticles,
        fromArticle,
        toArticle,
    } = useResourceHubSearch();

    // Category + page 1 of both filters arrive in a single request via the route data
    // loader, so switching tabs is instant with no additional loading state.
    const { data: categoryData } = useCategoryData();

    const category = computed(() => categoryData.value?.category ?? null);

    const activeFilter = computed(() => route.query.filter || 'all-articles');
    const currentPage = computed(() => parseInt(route.query.page) || 1);

    const filterTabs = [
        { label: 'All Articles', value: 'all-articles' },
        { label: 'Most Viewed', value: 'most-viewed' },
    ];

    function firstPageFor(filter) {
        const key = filter === 'most-viewed' ? 'most_viewed_articles' : 'all_articles';

        return categoryData.value?.[key] ?? null;
    }

    // Pages beyond the first are fetched (and cached) per filter + page via Pinia Colada.
    const pageQuery = useQuery({
        key: () => [
            'resource-hub',
            'category-articles',
            String(route.params.categoryId),
            activeFilter.value,
            currentPage.value,
        ],
        query: () =>
            apiGet(`/categories/${route.params.categoryId}`, { filter: activeFilter.value, page: currentPage.value }),
        enabled: () => currentPage.value > 1,
    });

    const currentPaginator = computed(() =>
        currentPage.value > 1 ? (pageQuery.data.value?.articles ?? null) : firstPageFor(activeFilter.value),
    );

    // Keep the previously rendered page visible while a new page loads so content
    // never disappears during pagination.
    const shownPaginator = ref(null);
    watch(
        currentPaginator,
        (paginator) => {
            if (paginator) {
                shownPaginator.value = paginator;
            }
        },
        { immediate: true },
    );

    const loadingPage = computed(() => (currentPage.value > 1 && pageQuery.isLoading.value ? currentPage.value : null));

    const articlesWithRoutes = computed(() =>
        (shownPaginator.value?.data ?? []).map((article) => ({
            ...article,
            key: article.id,
            to: { name: 'view-article', params: { categoryId: article.categoryId, articleId: article.id } },
        })),
    );

    const pagination = computed(() => ({
        currentPage: shownPaginator.value?.current_page ?? 1,
        lastPage: shownPaginator.value?.last_page ?? 1,
        total: shownPaginator.value?.total ?? 0,
        from: shownPaginator.value?.from ?? 0,
        to: shownPaginator.value?.to ?? 0,
    }));

    const breadcrumbs = computed(() => {
        if (category.value?.parentCategory) {
            return [
                {
                    name: category.value.parentCategory.name,
                    route: 'view-category',
                    params: { categoryId: category.value.parentCategory.id },
                },
            ];
        }

        return [];
    });

    function pushQuery(page, filter) {
        router.push({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page,
                filter: filter === 'all-articles' ? undefined : filter,
            },
        });
    }

    function changeFilter(value) {
        pushQuery(1, value);
    }

    function goToPage(page) {
        if (page === pagination.value.currentPage || loadingPage.value !== null) {
            return;
        }

        pushQuery(page, activeFilter.value);
    }

    function fetchNextPage() {
        if (pagination.value.currentPage < pagination.value.lastPage) {
            goToPage(pagination.value.currentPage + 1);
        }
    }

    function fetchPreviousPage() {
        if (pagination.value.currentPage > 1) {
            goToPage(pagination.value.currentPage - 1);
        }
    }
</script>

<template>
    <Page>
        <template #heading>{{ category?.name ?? '' }}</template>

        <template v-if="category?.description" #description>{{ category.description }}</template>

        <template #breadcrumbs>
            <Breadcrumbs v-if="category" :currentCrumb="category.name" :breadcrumbs="breadcrumbs" />
        </template>

        <template #belowHeaderContent>
            <HeroSearch
                ref="globalSearchInput"
                v-model="searchQuery"
                :tags="tagsArray"
                :selectedTags="selectedTags"
                @toggle-tag="toggleTag"
            />
        </template>

        <div v-if="category">
            <main class="flex flex-col gap-8">
                <SearchResults
                    v-if="searchQuery"
                    :searchQuery="searchQuery"
                    :articles="searchResultArticles"
                    :categories="searchResultCategories"
                    :loadingResults="loadingResults"
                    :fromItem="fromArticle"
                    :toItem="toArticle"
                    :totalItems="totalArticles"
                />

                <div v-else class="flex flex-col gap-6">
                    <div class="flex flex-col gap-4">
                        <Subheading :title="category.name" />
                        <div class="flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                            <Tabs
                                :tabs="filterTabs"
                                :modelValue="activeFilter"
                                @update:modelValue="changeFilter"
                                :contained="true"
                            />

                            <div v-if="articlesWithRoutes.length > 0">
                                <ul role="list" class="divide-y">
                                    <li v-for="article in articlesWithRoutes" :key="article.key">
                                        <Article :to="article.to" :name="article.name" />
                                    </li>
                                </ul>
                                <Pagination
                                    :currentPage="pagination.currentPage"
                                    :lastPage="pagination.lastPage"
                                    :fromItem="pagination.from"
                                    :toItem="pagination.to"
                                    :totalItems="pagination.total"
                                    :loadingPage="loadingPage"
                                    @fetchNextPage="fetchNextPage"
                                    @fetchPreviousPage="fetchPreviousPage"
                                    @fetchPage="goToPage"
                                />
                            </div>
                            <section v-else class="px-6 py-4 flex items-start gap-x-4">
                                <div class="flex size-12 items-center justify-center rounded-full bg-gray-100">
                                    <DocumentTextIcon class="size-6 text-gray-400" />
                                </div>

                                <div class="flex-1">
                                    <h4 class="text-base font-semibold leading-6 text-gray-950">No articles found</h4>

                                    <p class="mt-1 text-sm text-gray-500">No articles found in this category.</p>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </Page>
</template>
