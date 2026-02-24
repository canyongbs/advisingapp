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
    import { ExclamationCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';
    import { computed, defineEmits, defineProps, ref } from 'vue';

    const props = defineProps({
        selectedSlot: {
            type: Object,
            required: true,
        },
        bookingUrl: {
            type: String,
            required: true,
        },
        displayName: {
            type: String,
            required: true,
        },
        duration: {
            type: Number,
            required: true,
        },
    });

    const emit = defineEmits(['booking-success', 'booking-conflict', 'cancel']);

    const name = ref('');
    const email = ref('');
    const isSubmitting = ref(false);
    const error = ref(null);
    const validationErrors = ref({});

    const formattedSlotTime = computed(() => {
        const start = new Date(props.selectedSlot.start);
        const end = new Date(props.selectedSlot.end);
        return `${start.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        })} at ${start.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        })} - ${end.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        })}`;
    });

    const validateEmail = (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    };

    const validateForm = () => {
        validationErrors.value = {};
        let isValid = true;

        if (!name.value.trim()) {
            validationErrors.value.name = 'Please provide your name.';
            isValid = false;
        }

        if (!email.value.trim()) {
            validationErrors.value.email = 'Please provide your email address.';
            isValid = false;
        } else if (!validateEmail(email.value)) {
            validationErrors.value.email = 'Please provide a valid email address.';
            isValid = false;
        }

        return isValid;
    };

    const submitBooking = async () => {
        error.value = null;
        validationErrors.value = {};

        if (!validateForm()) {
            return;
        }

        isSubmitting.value = true;

        try {
            const response = await fetch(props.bookingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    name: name.value.trim(),
                    email: email.value.trim(),
                    starts_at: props.selectedSlot.start,
                    ends_at: props.selectedSlot.end,
                }),
            });

            const data = await response.json();

            if (response.ok && data.success) {
                emit('booking-success', data);
            } else if (response.status === 409) {
                // Slot conflict
                emit('booking-conflict', data.message);
            } else if (response.status === 403) {
                // Restricted to existing students only
                error.value = data.message || 'You are not authorized to book this appointment.';
            } else if (response.status === 422) {
                // Validation error
                if (data.errors) {
                    validationErrors.value = Object.fromEntries(
                        Object.entries(data.errors).map(([key, value]) => [
                            key,
                            Array.isArray(value) ? value[0] : value,
                        ]),
                    );
                } else {
                    error.value = data.message || 'Please check your input and try again.';
                }
            } else {
                error.value = data.message || 'An unexpected error occurred. Please try again.';
            }
        } catch (err) {
            console.error('Booking error:', err);
            error.value = 'Unable to connect to the server. Please check your internet connection and try again.';
        } finally {
            isSubmitting.value = false;
        }
    };
</script>

<template>
    <div class="bg-white rounded-lg shadow-2xl ring-1 ring-primary-950/5 backdrop-blur-sm p-6 animate-fade-in">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 tracking-tight">Confirm Your Booking</h3>
            <div class="bg-primary-50 rounded-lg p-4 border border-primary-200">
                <p class="text-sm text-primary-900 font-medium mb-1">Meeting with {{ displayName }}</p>
                <p class="text-sm text-primary-700">{{ formattedSlotTime }}</p>
                <p class="text-xs text-primary-600 mt-1">Duration: {{ duration }} minutes</p>
            </div>
        </div>

        <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
            <XCircleIcon class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-red-800">{{ error }}</p>
        </div>

        <form @submit.prevent="submitBooking" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                <input
                    id="name"
                    v-model="name"
                    type="text"
                    required
                    :disabled="isSubmitting"
                    class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed"
                    :class="validationErrors.name ? 'border-red-300 bg-red-50' : 'border-gray-300'"
                    placeholder="Enter your full name"
                />
                <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600 flex items-center gap-1">
                    <ExclamationCircleIcon class="w-4 h-4" />
                    {{ validationErrors.name }}
                </p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Your Email *</label>
                <input
                    id="email"
                    v-model="email"
                    type="email"
                    required
                    :disabled="isSubmitting"
                    class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed"
                    :class="validationErrors.email ? 'border-red-300 bg-red-50' : 'border-gray-300'"
                    placeholder="your.email@example.com"
                />
                <p v-if="validationErrors.email" class="mt-1 text-sm text-red-600 flex items-center gap-1">
                    <ExclamationCircleIcon class="w-4 h-4" />
                    {{ validationErrors.email }}
                </p>
            </div>

            <div class="flex gap-3 pt-2">
                <button
                    type="button"
                    @click="emit('cancel')"
                    :disabled="isSubmitting"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500/50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-primary-600/20 flex items-center justify-center gap-2"
                >
                    <div
                        v-if="isSubmitting"
                        class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"
                    ></div>
                    <span>{{ isSubmitting ? 'Booking...' : 'Confirm Booking' }}</span>
                </button>
            </div>
        </form>
    </div>
</template>
