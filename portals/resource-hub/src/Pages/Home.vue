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
    import HeroSearch from '@common/portal/HeroSearch.vue';
    import HelpCenter from '@common/portal/home/HelpCenter.vue';
    import Page from '@common/portal/Page.vue';
    import SearchResults from '@common/portal/SearchResults.vue';
    import { computed, nextTick, onMounted, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { apiPost } from '../Services/api.js';
    import { useConfigStore } from '../Stores/config.js';
    import { useCategoriesData } from './loaders.js';

    const route = useRoute();
    const router = useRouter();
    const config = useConfigStore();
    const { data: categories } = useCategoriesData();

    const searchQuery = ref('');
    const loadingResults = ref(false);
    const searchResults = ref(null);
    const filter = ref('');
    const currentPage = ref(1);
    const lastPage = ref(1);
    const totalArticles = ref(0);
    const fromArticle = ref(0);
    const toArticle = ref(0);
    const globalSearchInput = ref(null);

    const selectedTags = ref([]);
    const tagsArray = computed(() => []);

    const categoriesWithRoutes = computed(() =>
        Object.values(categories.value ?? {}).map((category) => ({
            ...category,
            key: category.id,
            to: { name: 'view-category', params: { categoryId: category.id } },
        })),
    );

    const articlesWithRoutes = computed(() =>
        (searchResults.value?.data?.articles ?? []).map((article) => ({
            ...article,
            key: article.id,
            to: { name: 'view-article', params: { categoryId: article.categoryId, articleId: article.id } },
        })),
    );

    const searchCategoriesWithRoutes = computed(() =>
        (searchResults.value?.data?.categories ?? []).map((category) => ({
            ...category,
            key: category.id,
            to: { name: 'view-category', params: { categoryId: category.id } },
        })),
    );

    const setPagination = (pagination) => {
        currentPage.value = pagination.current_page;
        lastPage.value = pagination.last_page;
        totalArticles.value = pagination.total;
        fromArticle.value = pagination.from;
        toArticle.value = pagination.to;
    };

    function debounce(func, delay) {
        let timerId;
        return function (...args) {
            if (timerId) clearTimeout(timerId);
            timerId = setTimeout(() => func(...args), delay);
        };
    }

    const debounceSearch = debounce(async (value, page = 1) => {
        if (!value || !value.trim()) {
            searchQuery.value = null;
            searchResults.value = null;
            currentPage.value = 1;
            lastPage.value = 1;
            totalArticles.value = 0;
            fromArticle.value = 0;
            toArticle.value = 0;
            return;
        }

        loadingResults.value = true;

        try {
            // `searchUrl` is a full signed URL (e.g. `${apiUrl}/search?signature=...`).
            // `apiPost` expects a path relative to the configured API base URL, so strip
            // that base URL off, keeping the signature/expiry query string intact.
            const searchPath = config.searchUrl.replace(config.apiUrl, '');

            const response = await apiPost(searchPath, {
                search: value,
                page,
                filter: filter.value,
            });

            searchResults.value = {
                data: {
                    articles: response?.data?.articles ?? [],
                    categories: response?.data?.categories ?? [],
                },
            };

            const articleCount = searchResults.value.data.articles.length;

            currentPage.value = 1;
            lastPage.value = 1;
            totalArticles.value = articleCount;
            fromArticle.value = articleCount > 0 ? 1 : 0;
            toArticle.value = articleCount;
        } finally {
            loadingResults.value = false;
        }
    }, 500);

    function toggleTag(tag) {
        if (selectedTags.value.includes(tag)) {
            selectedTags.value = selectedTags.value.filter((t) => t !== tag);
        } else {
            selectedTags.value = [...selectedTags.value, tag];
        }
    }

    const changeSearchFilter = (value) => {
        filter.value = value;
        router.push({
            name: route.name,
            query: { ...route.query, page: 1, filter: value || undefined },
        });
        debounceSearch(searchQuery.value, 1);
    };

    const fetchNextPage = () => {
        if (currentPage.value < lastPage.value) {
            fetchPage(currentPage.value + 1);
        }
    };

    const fetchPreviousPage = () => {
        if (currentPage.value > 1) {
            fetchPage(currentPage.value - 1);
        }
    };

    const fetchPage = (page) => {
        if (page <= 1) {
            return;
        }

        router.push({
            name: route.name,
            query: {
                ...route.query,
                page,
                search: searchQuery.value || undefined,
                filter: filter.value || undefined,
            },
        });
        debounceSearch(searchQuery.value, page);
    };

    watch(
        () => searchQuery.value,
        (newSearch) => {
            const isSearchEmpty = !newSearch || newSearch.trim() === '';

            if (isSearchEmpty) {
                router.push({ name: route.name, query: {} });
                return;
            }

            const urlSearch = route.query.search || '';
            const isSearchChanged = newSearch !== urlSearch;

            filter.value = route.query.filter || '';

            if (isSearchChanged) {
                router.push({
                    name: route.name,
                    query: {
                        page: 1,
                        search: newSearch || undefined,
                        filter: filter.value || undefined,
                    },
                });
                debounceSearch(newSearch, 1);
            } else {
                debounceSearch(searchQuery.value, route.query.page);
            }
        },
        { immediate: false },
    );

    watch(
        () => route.query,
        (newQuery) => {
            if (Object.keys(newQuery).length === 0) {
                filter.value = '';
                searchQuery.value = '';
            }

            handleInitialQuery();
        },
    );

    onMounted(() => {
        handleInitialQuery({ setFocus: true });
    });

    function handleInitialQuery({ setFocus = false } = {}) {
        const search = route.query.search;

        if (search) {
            currentPage.value = parseInt(route.query.page) || 1;
            searchQuery.value = search;

            if (setFocus) {
                nextTick(() => {
                    globalSearchInput.value?.focus();
                });
            }

            debounceSearch(search, currentPage.value);
        }
    }
</script>

<template>
    <Page>
        <template #heading>Resource Hub</template>

        <template #description>Search our knowledge base for advice and answers</template>

        <template #breadcrumbs>
            <Breadcrumbs currentCrumb="Home" />
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

        <SearchResults
            v-if="searchQuery"
            :searchQuery="searchQuery"
            :articles="articlesWithRoutes"
            :categories="searchCategoriesWithRoutes"
            :loadingResults="loadingResults"
            :selectedFilter="filter"
            :currentPage="currentPage"
            :lastPage="lastPage"
            :fromItem="fromArticle"
            :toItem="toArticle"
            :totalItems="totalArticles"
            @change-filter="changeSearchFilter"
            @fetchNextPage="fetchNextPage"
            @fetchPreviousPage="fetchPreviousPage"
            @fetchPage="fetchPage"
        />

        <HelpCenter v-else :categories="categoriesWithRoutes" />
    </Page>
</template>
