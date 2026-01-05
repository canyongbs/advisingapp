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
        slots: {
            type: Array,
            required: true,
        },
        selectedSlot: {
            type: Object,
            default: null,
        },
        selectedDate: {
            type: Date,
            default: null,
        },
    });

    const emit = defineEmits(['selectSlot']);

    const formattedDate = computed(() => {
        if (!props.selectedDate) return '';
        return props.selectedDate.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        });
    });

    const formatSlotTime = (isoString) => {
        const date = new Date(isoString);
        return date.toLocaleString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });
    };

    const isSelected = (slot) => {
        if (!props.selectedSlot) return false;
        return slot.start === props.selectedSlot.start;
    };
</script>

<template>
    <div class="bg-white rounded-lg shadow-2xl ring-1 ring-primary-950/5 backdrop-blur-sm p-6">
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-1 tracking-tight">{{ formattedDate }}</h3>
            <p class="text-sm text-gray-500 font-medium">{{ slots.length }} available times</p>
        </div>

        <div
            class="-m-2 p-2 grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-primary-300 scrollbar-track-gray-100 scrollbar-thumb-rounded scrollbar-track-rounded hover:scrollbar-thumb-primary-400"
        >
            <button
                v-for="(slot, index) in slots"
                :key="index"
                @click="emit('selectSlot', slot)"
                class="px-4 py-3 border rounded-lg text-sm font-medium text-center cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500/50"
                :class="
                    isSelected(slot)
                        ? 'bg-primary-600 border-primary-600 text-white shadow-xl shadow-primary-600/20 ring-2 ring-primary-500/50'
                        : 'bg-white border-gray-200 text-gray-900 hover:border-primary-500 hover:bg-primary-50 hover:text-primary-700 hover:scale-105 active:scale-95 hover:shadow-lg'
                "
            >
                {{ formatSlotTime(slot.start) }}
            </button>
        </div>

        <div v-if="slots.length === 0" class="text-center p-8 text-gray-500 text-sm">
            No available times for this day
        </div>
    </div>
</template>
