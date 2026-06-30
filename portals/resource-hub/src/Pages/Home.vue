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
    import { defineProps, nextTick, onMounted, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import HelpCenter from '../Components/HelpCenter.vue';
    import HeroSearch from '../Components/HeroSearch.vue';
    import Page from '../Components/Page.vue';
    import SearchResults from '../Components/SearchResults.vue';
    import { consumer } from '../Services/Consumer.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { useFeatureStore } from '../Stores/feature.js';
    import { globalSearchQuery } from '../Stores/globalState.js';

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
        serviceRequests: {
            type: Object,
            required: true,
        },
        tags: {
            type: Object,
            required: true,
        },
    });

    const searchQuery = ref('');
    const loadingResults = ref(false);
    const searchResults = ref(null);
    const selectedTags = ref([]);
    const route = useRoute();
    const router = useRouter();
    const globalSearchInput = ref(null);
    const filter = ref('');
    const currentPage = ref(1);
    const nextPageUrl = ref(null);
    const prevPageUrl = ref(null);
    const lastPage = ref(null);
    const totalArticles = ref(0);
    const fromArticle = ref(0);
    const toArticle = ref(0);

    const filterRouteChange = (page = currentPage.value || 1) => {
        router.push({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page: page,
                search: searchQuery.value || undefined,
                tags: selectedTags.value.join(',') || undefined,
                filter: filter.value || route.query.filter || undefined,
            },
        });
    };

    const debounceSearch = debounce((value, page = 1) => {
        const { post } = consumer();
        if (!value && selectedTags.value.length < 1) {
            searchQuery.value = null;
            searchResults.value = null;
            return;
        }

        loadingResults.value = true;

        post(props.searchUrl, {
            search: JSON.stringify(value),
            tags: selectedTags.value.join(','),
            filter: filter.value || route.query.filter || undefined,
            page: page,
        }).then((response) => {
            searchResults.value = response.data;
            loadingResults.value = false;
            setPagination(response.data.data.articles.meta);
        });
        globalSearchQuery.value = '';
    }, 500);

    const { user } = useAuthStore();
    const { hasServiceManagement } = useFeatureStore();

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
        () => route.query,
        (newQuery) => {
            const tags = newQuery.tags ? newQuery.tags.split(',') : [];

            if (Object.keys(newQuery).length === 0) {
                filter.value = '';
                searchQuery.value = '';
                selectedTags.value = [];
            }

            if (!newQuery.tags || tags.length === 0) {
                selectedTags.value = [];
            }

            handleInitialQuery();
        },
    );

    onMounted(() => {
        handleInitialQuery({ setFocus: true });
    });

    function handleInitialQuery({ setFocus = false } = {}) {
        const search = route.query.search;
        const tags = route.query.tags ? route.query.tags.split(',') : [];

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

        if (tags.length > 0) {
            selectedTags.value = tags;
            currentPage.value = parseInt(route.query.page) || 1;
        }
    }

    watch(
        () => [searchQuery.value, [...selectedTags.value]],
        ([newSearch, newTags]) => {
            const isSearchEmpty = !newSearch || newSearch.trim() === '';
            const areTagsEmpty = newTags.length === 0;

            if (isSearchEmpty && areTagsEmpty) {
                router.push({
                    name: route.name,
                    params: route.params,
                    query: {},
                });
                return;
            }

            const urlSearch = route.query.search || '';
            const urlTags = route.query.tags ? route.query.tags.split(',') : [];

            const isSearchChanged = newSearch !== urlSearch;
            const isTagsChanged = newTags.length !== urlTags.length || newTags.some((tag, i) => tag !== urlTags[i]);
            filter.value = route.query.filter || '';
            if (isSearchChanged || isTagsChanged) {
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
            } else {
                debounceSearch(searchQuery.value, route.query.page);
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

    const changeSearchFilter = (value) => {
        filter.value = value;
        filterRouteChange(1);
        debounceSearch(searchQuery.value, 1);
    };

    const fetchNextPage = () => {
        currentPage.value = currentPage.value !== lastPage.value ? currentPage.value + 1 : lastPage.value;
        fetchPage(currentPage.value);
    };

    const fetchPreviousPage = () => {
        currentPage.value = currentPage.value !== 1 ? currentPage.value - 1 : 1;
        fetchPage(currentPage.value);
    };

    const fetchPage = (page) => {
        filterRouteChange(page);
        debounceSearch(searchQuery.value, page);
    };
</script>

<template>
    <Page>
        <template #heading> Need help? </template>

        <template #description> Search our resource hub for advice and answers </template>

        <template #breadcrumbs>
            <Breadcrumbs currentCrumb="Home" />
        </template>

        <template #belowHeaderContent>
            <HeroSearch
                ref="globalSearchInput"
                v-model="searchQuery"
                :tags="tags"
                :selectedTags="selectedTags"
                @toggle-tag="toggleTag"
            />
        </template>

        <SearchResults
            v-if="searchQuery || selectedTags.length > 0"
            :searchQuery="searchQuery"
            :searchResults="searchResults"
            :loadingResults="loadingResults"
            @change-filter="changeSearchFilter"
            :selected-filter="filter"
            :currentPage="currentPage"
            :lastPage="lastPage"
            :fromItem="fromArticle"
            :toItem="toArticle"
            :totalItems="totalArticles"
            @fetchNextPage="fetchNextPage"
            @fetchPreviousPage="fetchPreviousPage"
            @fetchPage="fetchPage"
        >
        </SearchResults>

        <HelpCenter v-else :categories="categories" :service-requests="serviceRequests"></HelpCenter>
    </Page>
</template>
