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
    import Page from '@common/portal/Page.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import Subheading from '@common/portal/Subheading.vue';
    import Tabs from '@common/portal/Tabs.vue';
    import { DocumentTextIcon } from '@heroicons/vue/24/outline';
    import NProgress from 'nprogress';
    import 'nprogress/nprogress.css';
    import { computed, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { consumer } from '../Services/Consumer.js';

    NProgress.configure({ showSpinner: false });

    const route = useRoute();
    const router = useRouter();
    const { get } = consumer();

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
    });

    const category = ref(null);
    const initialLoading = ref(true);
    const loadingPage = ref(null);
    const activeFilter = ref('all-articles');

    const filterState = ref({
        'all-articles': { data: [], currentPage: 1, lastPage: 1, total: 0, from: 0, to: 0 },
        'most-viewed': { data: [], currentPage: 1, lastPage: 1, total: 0, from: 0, to: 0 },
    });

    const filterTabs = [
        { label: 'All Articles', value: 'all-articles' },
        { label: 'Most Viewed', value: 'most-viewed' },
    ];

    const currentFilterState = computed(() => filterState.value[activeFilter.value]);

    const articlesWithRoutes = computed(() => currentFilterState.value?.data ?? []);

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

    function setFilterPagination(filterKey, pagination) {
        filterState.value[filterKey] = {
            ...filterState.value[filterKey],
            currentPage: pagination.current_page,
            lastPage: pagination.last_page,
            total: pagination.total,
            from: pagination.from ?? 0,
            to: pagination.to ?? 0,
        };
    }

    function mapArticles(rawData) {
        return rawData.map((article) => ({
            ...article,
            key: article.id,
            to: { name: 'view-article', params: { categoryId: article.categoryId, articleId: article.id } },
        }));
    }

    function changeFilter(value) {
        activeFilter.value = value;

        router.push({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page: 1,
                filter: value === 'all-articles' ? undefined : value,
            },
        });
    }

    const fetchNextPage = () => {
        const state = currentFilterState.value;
        if (state.currentPage < state.lastPage) fetchPage(state.currentPage + 1);
    };

    const fetchPreviousPage = () => {
        const state = currentFilterState.value;
        if (state.currentPage > 1) fetchPage(state.currentPage - 1);
    };

    const fetchPage = (page) => {
        const state = currentFilterState.value;
        if (page === state.currentPage || loadingPage.value !== null) return;

        router.push({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page,
                filter: activeFilter.value === 'all-articles' ? undefined : activeFilter.value,
            },
        });
    };

    async function fetchFilterPage(filter, page) {
        loadingPage.value = page;

        try {
            const response = await get(props.apiUrl + '/categories/' + route.params.categoryId, {
                filter,
                page,
            });

            filterState.value[filter].data = mapArticles(response.data.articles.data);
            setFilterPagination(filter, response.data.articles);
        } catch (error) {
            console.error('Error fetching page:', error);
        } finally {
            loadingPage.value = null;
        }
    }

    let lastCategoryId = null;

    async function loadCategory() {
        NProgress.start();
        initialLoading.value = true;
        category.value = null;

        try {
            const response = await get(props.apiUrl + '/categories/' + route.params.categoryId);

            if (route.params.categoryId && route.params.parentCategoryId) {
                router.replace({
                    name: 'view-subcategory',
                    params: {
                        parentCategoryId: response.data.category.id,
                        categoryId: response.data.category.id,
                    },
                    query: { ...route.query },
                });
            }

            category.value = response.data.category;
            filterState.value['all-articles'].data = mapArticles(response.data.all_articles.data);
            setFilterPagination('all-articles', response.data.all_articles);
            filterState.value['most-viewed'].data = mapArticles(response.data.most_viewed_articles.data);
            setFilterPagination('most-viewed', response.data.most_viewed_articles);
        } catch (error) {
            console.error('Error loading category:', error);
        } finally {
            initialLoading.value = false;
            NProgress.done();
        }
    }

    watch(
        route,
        async (newRoute) => {
            const newCategoryId = newRoute.params.categoryId;
            const newFilter = newRoute.query.filter || 'all-articles';
            const newPage = parseInt(newRoute.query.page) || 1;

            if (newCategoryId !== lastCategoryId) {
                lastCategoryId = newCategoryId;
                activeFilter.value = newFilter;
                await loadCategory();
                return;
            }

            if (newFilter !== activeFilter.value) {
                activeFilter.value = newFilter;
            }

            const currentPage = currentFilterState.value?.currentPage ?? 1;
            if (newPage !== currentPage) {
                await fetchFilterPage(newFilter, newPage);
            }
        },
        { immediate: true },
    );
</script>

<template>
    <Page>
        <template #heading>{{ category?.name ?? '' }}</template>

        <template v-if="category?.description" #description>{{ category.description }}</template>

        <template #breadcrumbs>
            <Breadcrumbs v-if="!initialLoading && category" :currentCrumb="category.name" :breadcrumbs="breadcrumbs" />
        </template>

        <div v-if="initialLoading" class="flex items-center justify-center py-12">
            <div class="size-8 animate-spin rounded-full border-4 border-gray-200 border-t-brand-600"></div>
        </div>

        <div v-else-if="category">
            <main class="flex flex-col gap-8">
                <div class="flex flex-col gap-6">
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
                                        <Article
                                            :to="article.to"
                                            :name="article.name"
                                            :tags="article.tags ?? []"
                                            :featured="article.featured ?? false"
                                        />
                                    </li>
                                </ul>
                                <Pagination
                                    :currentPage="currentFilterState.currentPage"
                                    :lastPage="currentFilterState.lastPage"
                                    :fromItem="currentFilterState.from"
                                    :toItem="currentFilterState.to"
                                    :totalItems="currentFilterState.total"
                                    :loadingPage="loadingPage"
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
