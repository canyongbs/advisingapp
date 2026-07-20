/*
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
*/
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { apiPost } from '../Services/api.js';
import { useConfigStore } from '../Stores/config.js';

function debounce(func, delay) {
    let timerId;

    return function (...args) {
        if (timerId) clearTimeout(timerId);
        timerId = setTimeout(() => func(...args), delay);
    };
}

/**
 * Shared resource hub search behaviour, used by both the homepage and category
 * pages: keeps `searchQuery` in sync with the `search` route query param (so
 * searches are shareable/bookmarkable and survive back/forward navigation),
 * fetches results (debounced) from the signed search endpoint, and exposes the
 * results already mapped with router `to` targets for the shared `SearchResults`
 * component.
 *
 * `loadingResults` is flipped to `true` synchronously, before the debounce delay,
 * so the loading state is shown immediately and stale results are never flashed
 * while a new search is pending.
 */
export function useResourceHubSearch() {
    const route = useRoute();
    const router = useRouter();
    const config = useConfigStore();

    const searchQuery = ref('');
    const loadingResults = ref(false);
    const searchResults = ref(null);
    const globalSearchInput = ref(null);
    const selectedTags = ref([]);
    const tagsArray = computed(() => []);

    const searchResultArticles = computed(() =>
        (searchResults.value?.articles ?? []).map((article) => ({
            ...article,
            key: article.id,
            to: { name: 'view-article', params: { categoryId: article.categoryId, articleId: article.id } },
        })),
    );

    const searchResultCategories = computed(() =>
        (searchResults.value?.categories ?? []).map((category) => ({
            ...category,
            key: category.id,
            to: { name: 'view-category', params: { categoryId: category.id } },
        })),
    );

    const totalArticles = computed(() => searchResultArticles.value.length);
    const fromArticle = computed(() => (totalArticles.value > 0 ? 1 : 0));
    const toArticle = computed(() => totalArticles.value);

    const fetchResults = debounce(async (value) => {
        try {
            // `searchUrl` is a full signed URL (e.g. `${apiUrl}/search?signature=...`).
            // `apiPost` expects a path relative to the configured API base URL, so strip
            // that base URL off, keeping the signature/expiry query string intact.
            const searchPath = config.searchUrl.replace(config.apiUrl, '');

            const response = await apiPost(searchPath, { search: value });

            searchResults.value = {
                articles: response?.data?.articles ?? [],
                categories: response?.data?.categories ?? [],
            };
        } finally {
            loadingResults.value = false;
        }
    }, 500);

    function search(value) {
        if (!value || !value.trim()) {
            loadingResults.value = false;
            searchResults.value = null;

            return;
        }

        loadingResults.value = true;
        searchResults.value = null;
        fetchResults(value);
    }

    function toggleTag(tag) {
        if (selectedTags.value.includes(tag)) {
            selectedTags.value = selectedTags.value.filter((selectedTag) => selectedTag !== tag);
        } else {
            selectedTags.value = [...selectedTags.value, tag];
        }
    }

    watch(searchQuery, (newSearch) => {
        const isSearchEmpty = !newSearch || newSearch.trim() === '';

        router.replace({
            name: route.name,
            params: route.params,
            query: { ...route.query, search: isSearchEmpty ? undefined : newSearch },
        });

        search(newSearch);
    });

    watch(
        () => route.query.search,
        (newSearch) => {
            if ((newSearch || '') !== (searchQuery.value || '')) {
                searchQuery.value = newSearch || '';
            }
        },
    );

    onMounted(() => {
        const initialSearch = route.query.search;

        if (initialSearch) {
            loadingResults.value = true;
            searchResults.value = null;
            searchQuery.value = initialSearch;

            nextTick(() => {
                globalSearchInput.value?.focus();
            });

            search(initialSearch);
        }
    });

    return {
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
    };
}
