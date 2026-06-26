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
    import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';
    import { computed, defineProps } from 'vue';

    const props = defineProps({
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

    const emit = defineEmits(['fetchNextPage', 'fetchPreviousPage', 'fetchPage']);

    const paginationElements = computed(() => {
        const elements = [];
        const current = props.currentPage;
        const last = props.lastPage;

        if (last <= 7) {
            for (let i = 1; i <= last; i++) elements.push(i);
            return elements;
        }

        elements.push(1);

        if (current > 3) elements.push('...');

        const start = Math.max(2, current - 1);
        const end = Math.min(last - 1, current + 1);

        for (let i = start; i <= end; i++) elements.push(i);

        if (current < last - 2) elements.push('...');

        elements.push(last);

        return elements;
    });
</script>

<template>
    <nav
        aria-label="Pagination"
        role="navigation"
        class="grid grid-cols-[1fr_auto_1fr] items-center gap-x-3 border-t border-gray-200 px-6 py-3"
        :class="lastPage <= 1 && 'hidden md:grid'"
    >
        <button
            v-if="currentPage > 1"
            type="button"
            @click="emit('fetchPreviousPage')"
            class="justify-self-start md:hidden relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 text-gray-950 ring-1 ring-gray-950/10 hover:bg-gray-50 focus-visible:ring-2"
        >
            Previous
        </button>

        <span class="hidden md:inline text-sm font-medium text-gray-700">
            Showing {{ fromItem }} to {{ toItem }} of {{ totalItems }} results
        </span>

        <button
            v-if="currentPage < lastPage"
            type="button"
            @click="emit('fetchNextPage')"
            class="col-start-3 justify-self-end md:hidden relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 text-gray-950 ring-1 ring-gray-950/10 hover:bg-gray-50 focus-visible:ring-2"
        >
            Next
        </button>

        <ol
            v-if="lastPage > 1"
            class="hidden md:flex col-start-3 justify-self-end divide-x divide-gray-200 rounded-lg bg-white shadow-sm ring-1 ring-gray-950/10"
        >
            <li>
                <button
                    type="button"
                    :disabled="currentPage === 1"
                    aria-label="Previous page"
                    @click="emit('fetchPreviousPage')"
                    class="relative flex overflow-hidden p-2 transition duration-75 outline-none rounded-s-lg enabled:hover:bg-gray-50 enabled:focus-visible:z-10 enabled:focus-visible:ring-2 enabled:focus-visible:ring-brand-600"
                >
                    <ChevronLeftIcon class="size-5 text-gray-400 transition duration-75" />
                </button>
            </li>

            <li v-for="(element, index) in paginationElements" :key="index">
                <button
                    v-if="element === '...'"
                    type="button"
                    disabled
                    class="relative flex overflow-hidden p-2 transition duration-75 outline-none"
                >
                    <span class="px-1.5 text-sm font-semibold text-gray-500">&hellip;</span>
                </button>
                <button
                    v-else
                    type="button"
                    :aria-label="`Go to page ${element}`"
                    @click="emit('fetchPage', element)"
                    class="relative flex overflow-hidden p-2 transition duration-75 outline-none enabled:hover:bg-gray-50 enabled:focus-visible:z-10 enabled:focus-visible:ring-2 enabled:focus-visible:ring-brand-600"
                    :class="element === currentPage && 'bg-gray-50'"
                >
                    <span
                        class="px-1.5 text-sm font-semibold"
                        :class="element === currentPage ? 'text-brand-700' : 'text-gray-700'"
                    >
                        {{ element }}
                    </span>
                </button>
            </li>

            <li>
                <button
                    type="button"
                    :disabled="currentPage === lastPage"
                    aria-label="Next page"
                    @click="emit('fetchNextPage')"
                    class="relative flex overflow-hidden p-2 transition duration-75 outline-none rounded-e-lg enabled:hover:bg-gray-50 enabled:focus-visible:z-10 enabled:focus-visible:ring-2 enabled:focus-visible:ring-brand-600"
                >
                    <ChevronRightIcon class="size-5 text-gray-400 transition duration-75" />
                </button>
            </li>
        </ol>
    </nav>
</template>
