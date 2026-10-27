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
import { useQuery } from '@pinia/colada';
import { computed, ref, watch } from 'vue';

/**
 * A paginated API envelope whose default view arrives via a route data loader, with every other page or filter fetched
 * on demand. The last loaded envelope stays shown while the next one loads. Envelopes are either an API resource
 * collection (`data` plus `meta`) or a plain Laravel paginator.
 *
 * `pagination` holds the props and listeners for the common `Pagination` component, so it can be bound with `v-bind`.
 *
 * @param {object} options
 * @param {import('vue').Ref<object|null>} options.initialEnvelope The route data loader's envelope for the default view.
 * @param {(page: number) => boolean} options.usesInitialEnvelope Whether this page of the current view is the default view.
 * @param {(page: number) => Array} options.key The query key for this page of the current view.
 * @param {(page: number) => Promise<object>} options.query Fetches this page of the current view.
 * @param {() => boolean} [options.enabled] Whether the current view can be fetched yet.
 * @param {import('vue').Ref<number>} [options.currentPage] The current page, for pages that keep it outside the
 *     composable (such as in the URL). Defaults to a local ref starting at page 1.
 */
export function usePaginatedEnvelope({
    initialEnvelope,
    usesInitialEnvelope,
    key,
    query,
    enabled = () => true,
    currentPage = ref(1),
}) {
    const usesInitial = computed(() => usesInitialEnvelope(currentPage.value));

    const pageQuery = useQuery({
        key: () => key(currentPage.value),
        query: () => query(currentPage.value),
        enabled: () => !usesInitial.value && enabled(),
    });

    const currentEnvelope = computed(() => (usesInitial.value ? initialEnvelope.value : pageQuery.data.value) ?? null);

    const shownEnvelope = ref(null);
    watch(
        currentEnvelope,
        (envelope) => {
            if (envelope) {
                shownEnvelope.value = envelope;
            }
        },
        { immediate: true },
    );

    const isFetching = computed(() => !usesInitial.value && pageQuery.isLoading.value);
    const loadFailed = computed(() => !usesInitial.value && !isFetching.value && Boolean(pageQuery.error.value));

    function fetchPage(page) {
        currentPage.value = page;
    }

    const meta = computed(() => shownEnvelope.value?.meta ?? shownEnvelope.value);

    const pagination = computed(() => ({
        currentPage: currentPage.value,
        lastPage: meta.value?.last_page ?? 1,
        fromItem: meta.value?.from ?? 0,
        toItem: meta.value?.to ?? 0,
        totalItems: meta.value?.total ?? 0,
        loadingPage: isFetching.value ? currentPage.value : null,
        onFetchPage: fetchPage,
        onFetchPreviousPage: () => fetchPage(currentPage.value - 1),
        onFetchNextPage: () => fetchPage(currentPage.value + 1),
    }));

    return {
        currentPage,
        shownEnvelope,
        isFetching,
        loadFailed,
        pagination,
        refetch: () => pageQuery.refetch(),
    };
}
