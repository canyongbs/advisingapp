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
    import { InformationCircleIcon } from '@heroicons/vue/24/outline';
    import { computed, defineProps, onMounted, ref, watch } from 'vue';
    import BookingForm from './components/BookingForm.vue';
    import BookingSuccess from './components/BookingSuccess.vue';
    import CalendarGrid from './components/CalendarGrid.vue';
    import TimeSlotPicker from './components/TimeSlotPicker.vue';
    const props = defineProps({
        entryUrl: {
            type: String,
            required: true,
        },
    });
    const displayName = ref('');
    const duration = ref(30);
    const userTimezone = ref('');
    const visitorTimezone = ref('');
    const availableBlocks = ref([]);
    const loading = ref(true);
    const loadingSlots = ref(false);
    const error = ref(null);
    const primaryColor = ref({});
    const currentYear = ref(new Date().getFullYear());
    const currentMonth = ref(new Date().getMonth() + 1);
    const selectedDate = ref(null);
    const selectedSlot = ref(null);

    // Booking state
    const slug = ref('');
    const showBookingForm = ref(false);
    const bookingSuccess = ref(null);
    const conflictError = ref(null);
    const bookingUrl = ref('');
    const availableSlotsUrl = ref('');
    const appointmentSlots = computed(() => {
        const slots = [];
        const allowedMinutes = [0, 15, 30, 45];

        for (const block of availableBlocks.value) {
            const blockStart = new Date(block.start);
            const blockEnd = new Date(block.end);
            const durationMs = duration.value * 60 * 1000;

            // Find the first allowed time slot at or after the block start
            let slotStart = new Date(blockStart);
            const currentMinutes = slotStart.getMinutes();

            // Round up to the next allowed minute interval
            const nextAllowedMinute = allowedMinutes.find((minutes) => minutes >= currentMinutes) ?? allowedMinutes[0];
            if (nextAllowedMinute < currentMinutes) {
                // Need to go to next hour
                slotStart.setHours(slotStart.getHours() + 1);
                slotStart.setMinutes(0);
            } else {
                slotStart.setMinutes(nextAllowedMinute);
            }
            slotStart.setSeconds(0);
            slotStart.setMilliseconds(0);

            // Generate slots at 15-minute intervals
            while (slotStart.getTime() + durationMs <= blockEnd.getTime()) {
                const slotEnd = new Date(slotStart.getTime() + durationMs);
                slots.push({
                    start: slotStart.toISOString(),
                    end: slotEnd.toISOString(),
                });

                // Move to next 15-minute interval
                slotStart = new Date(slotStart.getTime() + 15 * 60 * 1000);
            }
        }
        return slots;
    });
    const slotsPerDay = computed(() => {
        const slotsMap = {};
        appointmentSlots.value.forEach((slot) => {
            const date = new Date(slot.start);
            // Use local date string to avoid timezone issues
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const dateKey = `${year}-${month}-${day}`;
            slotsMap[dateKey] = (slotsMap[dateKey] || 0) + 1;
        });
        return slotsMap;
    });
    const slotsForSelectedDate = computed(() => {
        if (!selectedDate.value) return [];
        // Use local date string to avoid timezone issues
        const year = selectedDate.value.getFullYear();
        const month = String(selectedDate.value.getMonth() + 1).padStart(2, '0');
        const day = String(selectedDate.value.getDate()).padStart(2, '0');
        const dateKey = `${year}-${month}-${day}`;
        return appointmentSlots.value.filter((slot) => {
            const slotDate = new Date(slot.start);
            const slotYear = slotDate.getFullYear();
            const slotMonth = String(slotDate.getMonth() + 1).padStart(2, '0');
            const slotDay = String(slotDate.getDate()).padStart(2, '0');
            const slotDateKey = `${slotYear}-${slotMonth}-${slotDay}`;
            return slotDateKey === dateKey;
        });
    });
    const hasAnySlots = computed(() => {
        return appointmentSlots.value.length > 0;
    });
    onMounted(async () => {
        visitorTimezone.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
        await getBookingPageData();
        await fetchAvailableBlocks();
    });
    watch([currentYear, currentMonth], async () => {
        await fetchAvailableBlocks();
    });
    watch(availableBlocks, () => {
        if (hasAnySlots.value && !selectedDate.value) {
            selectFirstAvailableDate();
        } else if (!hasAnySlots.value) {
            checkAndMoveToNextMonth();
        }
    });
    async function getBookingPageData() {
        try {
            const response = await fetch(props.entryUrl);
            if (!response.ok) {
                throw new Error('Failed to load booking page data');
            }
            const data = await response.json();
            displayName.value = data.display_name;
            slug.value = data.slug;
            duration.value = data.duration;
            userTimezone.value = data.timezone;
            primaryColor.value = data.primary_color || {};
            bookingUrl.value = data.booking_url || '';
            availableSlotsUrl.value = data.available_slots_url || '';
        } catch (err) {
            error.value = err.message;
        } finally {
            loading.value = false;
        }
    }
    async function fetchAvailableBlocks() {
        loadingSlots.value = true;
        try {
            if (!availableSlotsUrl.value) {
                throw new Error('Available slots URL not loaded');
            }
            const url = new URL(availableSlotsUrl.value);
            url.searchParams.set('year', currentYear.value.toString());
            url.searchParams.set('month', currentMonth.value.toString());
            const response = await fetch(url.toString());
            if (!response.ok) {
                throw new Error('Failed to load available blocks');
            }
            const data = await response.json();
            availableBlocks.value = data.blocks || [];
        } catch (error) {
            console.error('Error fetching blocks:', error);
        } finally {
            loadingSlots.value = false;
        }
    }
    function selectFirstAvailableDate() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const sortedDates = Object.keys(slotsPerDay.value)
            .map((dateStr) => {
                // Parse the local date key (YYYY-MM-DD) correctly
                const [year, month, day] = dateStr.split('-').map(Number);
                return new Date(year, month - 1, day);
            })
            .filter((date) => date >= today)
            .sort((a, b) => a - b);
        if (sortedDates.length > 0) {
            selectedDate.value = sortedDates[0];
        }
    }
    function checkAndMoveToNextMonth() {
        const today = new Date();
        const currentMonthDate = new Date(currentYear.value, currentMonth.value - 1);
        const daysUntilMonthEnd = new Date(currentYear.value, currentMonth.value, 0).getDate() - today.getDate();
        if (daysUntilMonthEnd <= 7 && currentMonthDate.getMonth() === today.getMonth()) {
            goToNextMonth();
        }
    }
    function handleSelectDate(date) {
        selectedDate.value = date;
        selectedSlot.value = null;
    }
    function handleSelectSlot(slot) {
        selectedSlot.value = slot;
        showBookingForm.value = true;
        conflictError.value = null;
    }
    function goToPreviousMonth() {
        if (currentMonth.value === 1) {
            currentMonth.value = 12;
            currentYear.value--;
        } else {
            currentMonth.value--;
        }
        selectedDate.value = null;
        selectedSlot.value = null;
    }
    function goToNextMonth() {
        if (currentMonth.value === 12) {
            currentMonth.value = 1;
            currentYear.value++;
        } else {
            currentMonth.value++;
        }
        selectedDate.value = null;
        selectedSlot.value = null;
    }

    function handleBookingSuccess(data) {
        bookingSuccess.value = data;
        showBookingForm.value = false;
    }

    function handleBookingConflict(message) {
        conflictError.value = message;
        showBookingForm.value = false;
        selectedSlot.value = null;
        // Refresh the available slots to get updated availability
        fetchAvailableBlocks();
    }

    function handleCancelBooking() {
        showBookingForm.value = false;
        selectedSlot.value = null;
    }

    function handleBookAnother() {
        bookingSuccess.value = null;
        selectedSlot.value = null;
        selectedDate.value = null;
        showBookingForm.value = false;
        conflictError.value = null;
        // Refresh the available slots
        fetchAvailableBlocks();
    }
</script>
<template>
    <div
        class="min-h-screen p-6 font-sans"
        :style="{
            '--primary-50': primaryColor[50] || 'rgb(239 246 255)',
            '--primary-100': primaryColor[100] || 'rgb(219 234 254)',
            '--primary-200': primaryColor[200] || 'rgb(191 219 254)',
            '--primary-300': primaryColor[300] || 'rgb(147 197 253)',
            '--primary-400': primaryColor[400] || 'rgb(96 165 250)',
            '--primary-500': primaryColor[500] || 'rgb(59 130 246)',
            '--primary-600': primaryColor[600] || 'rgb(37 99 235)',
            '--primary-700': primaryColor[700] || 'rgb(29 78 216)',
            '--primary-800': primaryColor[800] || 'rgb(30 64 175)',
            '--primary-900': primaryColor[900] || 'rgb(30 58 138)',
            '--primary-950': primaryColor[950] || 'rgb(23 37 84)',
        }"
    >
        <div v-if="loading" class="text-center p-8 text-lg">Loading...</div>
        <div
            v-else-if="error"
            class="text-center p-6 text-white text-sm bg-red-600/90 rounded-lg max-w-xl mx-auto shadow-xl"
        >
            {{ error }}
        </div>
        <div v-else class="max-w-6xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-semibold mb-2 tracking-tight">Book a meeting with {{ displayName }}</h1>
                <p class="text-lg opacity-90">{{ duration }} minute appointment</p>
            </div>

            <div v-if="loadingSlots" class="text-center py-16 px-8 text-white">
                <div
                    class="w-12 h-12 border-4 border-white/30 border-t-white rounded-full animate-spin mx-auto mb-4"
                ></div>
                <p class="font-medium">Loading available appointments...</p>
            </div>

            <div v-else class="grid lg:grid-cols-2 gap-6 items-start">
                <div class="animate-fade-in">
                    <CalendarGrid
                        :year="currentYear"
                        :month="currentMonth"
                        :selected-date="selectedDate"
                        :slots-per-day="slotsPerDay"
                        @select-date="handleSelectDate"
                        @previous-month="goToPreviousMonth"
                        @next-month="goToNextMonth"
                    />
                </div>

                <div v-if="bookingSuccess" class="animate-fade-in">
                    <BookingSuccess
                        :message="bookingSuccess.message"
                        :event="bookingSuccess.event"
                        @book-another="handleBookAnother"
                    />
                </div>

                <div v-else-if="showBookingForm && selectedSlot" class="animate-fade-in">
                    <BookingForm
                        :selected-slot="selectedSlot"
                        :booking-url="bookingUrl"
                        :display-name="displayName"
                        :duration="duration"
                        @booking-success="handleBookingSuccess"
                        @booking-conflict="handleBookingConflict"
                        @cancel="handleCancelBooking"
                    />
                </div>

                <div v-else-if="selectedDate" class="animate-fade-in">
                    <div v-if="conflictError" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-800 font-medium">{{ conflictError }}</p>
                        <p class="text-xs text-red-600 mt-1">Please select a different time slot.</p>
                    </div>
                    <TimeSlotPicker
                        :slots="slotsForSelectedDate"
                        :selected-slot="selectedSlot"
                        :selected-date="selectedDate"
                        @select-slot="handleSelectSlot"
                    />
                </div>
            </div>

            <div
                v-if="userTimezone && visitorTimezone && userTimezone !== visitorTimezone"
                class="relative flex items-start gap-4 mt-16"
            >
                <div class="flex-shrink-0 bg-primary-100 p-2 rounded-lg">
                    <InformationCircleIcon class="w-5 h-5 text-primary-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-primary-900 mb-1">Timezone Information</h4>
                    <p class="text-sm text-primary-800 leading-relaxed">
                        {{ displayName }}'s availability is based on
                        <span class="font-medium">{{ userTimezone }}</span> timezone. All times shown below are
                        automatically converted to your local timezone (<span class="font-medium">{{
                            visitorTimezone
                        }}</span
                        >).
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
