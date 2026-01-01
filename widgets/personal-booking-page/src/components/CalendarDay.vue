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
    import { computed, defineEmits, defineProps } from 'vue';

    const props = defineProps({
        date: {
            type: Date,
            required: true,
        },
        isToday: {
            type: Boolean,
            default: false,
        },
        isSelected: {
            type: Boolean,
            default: false,
        },
        isPast: {
            type: Boolean,
            default: false,
        },
        hasSlots: {
            type: Boolean,
            default: false,
        },
        isCurrentMonth: {
            type: Boolean,
            default: true,
        },
    });

    const emit = defineEmits(['select']);

    const dayNumber = computed(() => props.date.getDate());

    const isClickable = computed(() => {
        return !props.isPast && props.hasSlots && props.isCurrentMonth;
    });

    const handleClick = () => {
        if (isClickable.value) {
            emit('select', props.date);
        }
    };
</script>

<template>
    <button
        @click="handleClick"
        :disabled="!isClickable"
        class="group relative min-h-14 border rounded-lg flex flex-col items-center justify-center transition-all duration-150 font-medium"
        :class="[
            isSelected
                ? 'bg-primary-600 border-primary-600 text-white shadow-xl shadow-primary-600/20 ring-2 ring-primary-500/50'
                : isToday && !isSelected
                  ? 'border-2 border-primary-500 bg-white text-primary-700 shadow-lg ring-1 ring-primary-500/20'
                  : 'border-gray-200 bg-white text-gray-900',
            isPast || !isCurrentMonth ? 'opacity-30 bg-gray-50/50' : '',
            !hasSlots && !isPast ? 'opacity-50 cursor-not-allowed' : '',
            isClickable
                ? 'cursor-pointer hover:border-primary-500 hover:bg-primary-50 hover:text-primary-700 hover:scale-105 active:scale-95 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-500/50'
                : 'cursor-default',
        ]"
    >
        <span class="text-sm">{{ dayNumber }}</span>
        <span
            v-if="hasSlots && !isPast && isCurrentMonth"
            class="absolute bottom-1.5 w-1.5 h-1.5 rounded-full transition-all duration-150"
            :class="isSelected ? 'bg-white group-hover:bg-primary-600' : 'bg-primary-600'"
        ></span>
    </button>
</template>
