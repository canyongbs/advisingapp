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
    import SearchResults from '@common/portal/category/SearchResults.vue';
    import SubCategories from '@common/portal/category/SubCategories.vue';
    import HeroSearch from '@common/portal/HeroSearch.vue';
    import Page from '@common/portal/Page.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import Subheading from '@common/portal/Subheading.vue';
    import Tabs from '@common/portal/Tabs.vue';
    import { DocumentTextIcon } from '@heroicons/vue/24/outline';
    import { computed, defineProps, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import AppLoading from '../Components/AppLoading.vue';
    import { consumer } from '../Services/Consumer.js';

    const route = useRoute();
    const router = useRouter();

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
        tags: {
            type: Object,
            required: true,
        },
    });

    const loadingResults = ref(true);
    const loadingeSearchResults = ref(true);
    const category = ref(null);
    const articles = ref(null);
    const searchQuery = ref('');
    const searchResults = ref(null);
    const selectedTags = ref([]);
    const currentPage = ref(1);
    const nextPageUrl = ref(null);
    const prevPageUrl = ref(null);
    const lastPage = ref(null);
    const totalArticles = ref(0);
    const fromArticle = ref(0);
    const toArticle = ref(0);
    const filter = ref('');
    const fromSearch = ref(false);

    const filterTabs = [
        { label: 'All Articles', value: 'all-articles' },
        { label: 'Featured', value: 'featured' },
        { label: 'Most Viewed', value: 'most-viewed' },
    ];

    const debounceSearch = debounce((value, page = 1) => {
        const { post } = consumer();

        fromSearch.value = true;

        if (!value && selectedTags.value.length < 1) {
            searchQuery.value = null;
            searchResults.value = null;
            return;
        }

        loadingeSearchResults.value = true;

        post(props.searchUrl, {
            search: JSON.stringify(value),
            tags: selectedTags.value.join(','),
            filter: filter.value,
            page: page,
        }).then((response) => {
            searchResults.value = response.data;
            loadingeSearchResults.value = false;
        });
    }, 500);

    const articlesWithRoutes = computed(() =>
        (articles.value ?? []).map((article) => ({
            ...article,
            key: article.id,
            to: { name: 'view-article', params: { categoryId: article.categoryId, articleId: article.id } },
        })),
    );

    const subCategoriesWithRoutes = computed(() =>
        (category.value?.subCategories ?? []).map((subCategory) => ({
            ...subCategory,
            key: subCategory.id,
            to: {
                name: 'view-subcategory',
                params: {
                    parentCategoryId: subCategory.parentCategory.id,
                    categoryId: subCategory.id,
                },
            },
        })),
    );

    const searchArticles = computed(() =>
        (searchResults.value?.data?.articles ?? []).map((article) => ({
            ...article,
            key: article.id,
            to: { name: 'view-article', params: { categoryId: article.categoryId, articleId: article.id } },
        })),
    );

    const searchCategories = computed(() =>
        (searchResults.value?.data?.categories ?? []).map((cat) => ({
            ...cat,
            key: cat.id,
            to: { name: 'view-category', params: { categoryId: cat.id } },
        })),
    );

    const setPagination = (pagination) => {
        currentPage.value = pagination.current_page;
        prevPageUrl.value = pagination.prev_page_url;
        nextPageUrl.value = pagination.next_page_url;
        lastPage.value = pagination.last_page;
        totalArticles.value = pagination.total;
        fromArticle.value = pagination.from;
        toArticle.value = pagination.to;
    };

    watch(
        () => [searchQuery.value, [...selectedTags.value]],
        ([newSearch, newTags]) => {
            const urlSearch = route.query.search || '';
            const urlTags = route.query.tags ? route.query.tags.split(',') : [];

            const isSearchChanged = newSearch !== urlSearch;
            const isTagsChanged = newTags.length !== urlTags.length || newTags.some((tag, i) => tag !== urlTags[i]);

            if (isSearchChanged || isTagsChanged) {
                fromSearch.value = !!(newSearch || newTags.length);

                router.push({
                    name: route.name,
                    params: route.params,
                    query: {
                        ...route.query,
                        page: 1,
                        search: newSearch || undefined,
                        tags: newTags.join(',') || undefined,
                        filter: filter.value || undefined,
                    },
                });

                debounceSearch(newSearch, 1);
            }
        },
        { immediate: false },
    );

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

    function toggleTag(tag) {
        if (selectedTags.value.includes(tag)) {
            selectedTags.value = selectedTags.value.filter((t) => t !== tag);
        } else {
            selectedTags.value = [...selectedTags.value, tag];
        }
    }

    const fetchNextPage = () => {
        if (currentPage.value < lastPage.value) {
            const newPage = currentPage.value + 1;
            fetchPage(newPage);
        }
    };

    const fetchPreviousPage = () => {
        if (currentPage.value > 1) {
            const newPage = currentPage.value - 1;
            fetchPage(newPage);
        }
    };

    const fetchPage = (page) => {
        if (page === currentPage.value) return;

        router.push({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page,
                search: searchQuery.value || undefined,
                tags: selectedTags.value.join(',') || undefined,
                filter: filter.value || undefined,
            },
        });
    };

    const changeFilter = (value) => {
        filter.value = value;

        filterRouteChange();
    };

    const changeSearchFilter = (value) => {
        filter.value = value;
        filterRouteChange();
        debounceSearch(searchQuery.value);
    };

    const filterRouteChange = () => {
        router.push({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page: 1,
                filter: filter.value,
            },
        });
    };

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

    watch(
        route,
        async (newRoute) => {
            const page = parseInt(newRoute.query.page) || 1;
            const search = newRoute.query.search || '';
            const tags = newRoute.query.tags ? newRoute.query.tags.split(',') : [];
            const appliedFilter = newRoute.query.filter || '';
            const isSearchMode = !!search || tags.length > 0;

            currentPage.value = page;
            searchQuery.value = search;

            selectedTags.value.splice(0, selectedTags.value.length, ...tags);

            filter.value = appliedFilter;
            fromSearch.value = isSearchMode;
            await getData(page);
        },
        { immediate: true },
    );

    async function getData(page = 1) {
        if (fromSearch.value) {
            loadingResults.value = false;
            debounceSearch(searchQuery.value, page);
            return;
        }

        loadingResults.value = true;

        const { get } = consumer();

        await get(props.apiUrl + '/categories/' + route.params.categoryId, {
            page: page,
            filter: filter.value,
        })
            .then((response) => {
                if (route.params.categoryId && route.params.parentCategoryId) {
                    router.replace({
                        name: 'view-subcategory',
                        params: {
                            parentCategoryId: response.data.category.parentCategory.id,
                            categoryId: response.data.category.id,
                        },
                        query: { ...route.query },
                    });
                } else if (route.params.categoryId) {
                    router.replace({
                        name: 'view-category',
                        params: { categoryId: response.data.category.id },
                        query: { ...route.query },
                    });
                }

                category.value = response.data.category;
                articles.value = response.data.articles.data;
                setPagination(response.data.articles);
                loadingResults.value = false;
            })
            .catch((error) => {
                console.error('Error loading category:', error);
                loadingResults.value = false;
            });
    }
</script>

<template>
    <Page>
        <template #heading> Need help? </template>

        <template #description> Search our resource hub for advice and answers </template>

        <template #belowHeaderContent>
            <HeroSearch v-model="searchQuery" :tags="tags" :selectedTags="selectedTags" @toggle-tag="toggleTag" />
        </template>

        <template #breadcrumbs>
            <Breadcrumbs
                :currentCrumb="category?.name"
                :breadcrumbs="breadcrumbs"
                v-if="!loadingResults && category && !(searchQuery || selectedTags.length > 0)"
            />
        </template>

        <div v-if="loadingResults">
            <AppLoading />
        </div>
        <div v-else-if="category">
            <main class="flex flex-col gap-8">
                <div v-if="searchQuery || selectedTags.length > 0" class="flex flex-col gap-6">
                    <SearchResults
                        :searchQuery="searchQuery"
                        :articles="searchArticles"
                        :categories="searchCategories"
                        :loadingResults="loadingeSearchResults"
                        label="Searching Resource Hub..."
                    />
                </div>
                <div v-else class="flex flex-col gap-6">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1">
                            <Subheading :title="category.name" />
                            <p v-if="category.description" class="text-sm text-gray-500">{{ category.description }}</p>
                        </div>
                        <SubCategories
                            v-if="subCategoriesWithRoutes.length > 0"
                            :subCategories="subCategoriesWithRoutes"
                        ></SubCategories>
                        <div class="flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                            <Tabs
                                :tabs="filterTabs"
                                :modelValue="filter || 'all-articles'"
                                @update:modelValue="changeFilter"
                                :contained="true"
                            />

                            <div v-if="articlesWithRoutes.length > 0">
                                <ul role="list" class="divide-y">
                                    <li v-for="article in articlesWithRoutes" :key="article.key">
                                        <Article
                                            :to="article.to"
                                            :name="article.name"
                                            :tags="article.tags"
                                            :featured="article.featured"
                                        />
                                    </li>
                                </ul>
                                <Pagination
                                    :currentPage="currentPage"
                                    :lastPage="lastPage"
                                    :fromItem="fromArticle"
                                    :toItem="toArticle"
                                    :totalItems="totalArticles"
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
