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
    import CalendarDay from './CalendarDay.vue';
    import CalendarHeader from './CalendarHeader.vue';

    const props = defineProps({
        year: {
            type: Number,
            required: true,
        },
        month: {
            type: Number,
            required: true,
        },
        selectedDate: {
            type: Date,
            default: null,
        },
        slotsPerDay: {
            type: Object,
            required: true,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits(['selectDate', 'previousMonth', 'nextMonth']);

    const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    const calendarDays = computed(() => {
        const firstDay = new Date(props.year, props.month - 1, 1);
        const lastDay = new Date(props.year, props.month, 0);
        const days = [];

        // Add days from previous month to fill the first week
        const firstDayOfWeek = firstDay.getDay();
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const date = new Date(props.year, props.month - 1, -i);
            days.push({
                date,
                isCurrentMonth: false,
            });
        }

        // Add all days of current month
        for (let i = 1; i <= lastDay.getDate(); i++) {
            const date = new Date(props.year, props.month - 1, i);
            days.push({
                date,
                isCurrentMonth: true,
            });
        }

        // Add days from next month to complete the last week
        const remainingDays = 7 - (days.length % 7);
        if (remainingDays < 7) {
            for (let i = 1; i <= remainingDays; i++) {
                const date = new Date(props.year, props.month, i);
                days.push({
                    date,
                    isCurrentMonth: false,
                });
            }
        }

        return days;
    });

    const isToday = (date) => {
        const today = new Date();
        return (
            date.getDate() === today.getDate() &&
            date.getMonth() === today.getMonth() &&
            date.getFullYear() === today.getFullYear()
        );
    };

    const isPast = (date) => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const compareDate = new Date(date);
        compareDate.setHours(0, 0, 0, 0);
        return compareDate < today;
    };

    const isSelected = (date) => {
        if (!props.selectedDate) return false;
        return (
            date.getDate() === props.selectedDate.getDate() &&
            date.getMonth() === props.selectedDate.getMonth() &&
            date.getFullYear() === props.selectedDate.getFullYear()
        );
    };

    const hasSlots = (date) => {
        // Use local date string to avoid timezone issues
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const dateKey = `${year}-${month}-${day}`;
        return props.slotsPerDay[dateKey] > 0;
    };

    const handleSelectDate = (date) => {
        emit('selectDate', date);
    };

    const hasAnySlots = computed(() => {
        return Object.values(props.slotsPerDay).some((count) => count > 0);
    });
</script>

<template>
    <div
        class="bg-white rounded-lg shadow-2xl ring-1 ring-primary-950/5 backdrop-blur-sm p-6"
        :class="{ 'opacity-60 pointer-events-none': disabled || !hasAnySlots }"
    >
        <CalendarHeader :year="year" :month="month" @previous="emit('previousMonth')" @next="emit('nextMonth')" />

        <div
            v-if="!hasAnySlots"
            class="text-center p-6 text-gray-500 text-sm bg-gray-50 rounded-lg mb-4 border border-gray-100"
        >
            No available appointments this month
        </div>

        <div class="grid grid-cols-7 gap-2">
            <div
                v-for="day in weekDays"
                :key="day"
                class="text-center text-xs font-semibold text-gray-500 py-3 uppercase tracking-wide"
            >
                {{ day }}
            </div>

            <CalendarDay
                v-for="(dayInfo, index) in calendarDays"
                :key="index"
                :date="dayInfo.date"
                :is-today="isToday(dayInfo.date)"
                :is-selected="isSelected(dayInfo.date)"
                :is-past="isPast(dayInfo.date)"
                :has-slots="hasSlots(dayInfo.date)"
                :is-current-month="dayInfo.isCurrentMonth"
                @select="handleSelectDate"
            />
        </div>
    </div>
</template>
