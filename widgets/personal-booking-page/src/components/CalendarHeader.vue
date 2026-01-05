<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
    import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';
    import { computed, defineEmits, defineProps } from 'vue';

    const props = defineProps({
        year: {
            type: Number,
            required: true,
        },
        month: {
            type: Number,
            required: true,
        },
        canGoNext: {
            type: Boolean,
            default: true,
        },
    });

    const emit = defineEmits(['previous', 'next']);

    const monthName = computed(() => {
        const date = new Date(props.year, props.month - 1);
        return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    });

    const canGoPrevious = computed(() => {
        const today = new Date();
        const currentMonth = new Date(props.year, props.month - 1);
        return currentMonth > new Date(today.getFullYear(), today.getMonth());
    });
</script>

<template>
    <div class="flex items-center justify-between mb-6 px-2">
        <button
            @click="emit('previous')"
            :disabled="!canGoPrevious"
            class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 hover:text-primary-600 hover:bg-primary-50 hover:border-primary-300 hover:scale-105 active:scale-95 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500/20 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-gray-50 disabled:hover:text-gray-600 disabled:hover:border-gray-200 disabled:hover:scale-100 shadow-sm"
        >
            <ChevronLeftIcon class="w-5 h-5" />
        </button>

        <h2 class="text-xl font-semibold text-gray-900 tracking-tight">{{ monthName }}</h2>

        <button
            @click="emit('next')"
            :disabled="!canGoNext"
            class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 hover:text-primary-600 hover:bg-primary-50 hover:border-primary-300 hover:scale-105 active:scale-95 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500/20 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-gray-50 disabled:hover:text-gray-600 disabled:hover:border-gray-200 disabled:hover:scale-100 shadow-sm"
        >
            <ChevronRightIcon class="w-5 h-5" />
        </button>
    </div>
</template>
