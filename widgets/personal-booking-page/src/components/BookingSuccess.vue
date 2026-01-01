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
    import { CheckCircleIcon } from '@heroicons/vue/24/outline';
    import { defineEmits, defineProps } from 'vue';

    defineProps({
        message: {
            type: String,
            required: true,
        },
        event: {
            type: Object,
            default: null,
        },
    });

    const emit = defineEmits(['book-another']);
</script>

<template>
    <div class="bg-white rounded-lg shadow-2xl ring-1 ring-primary-950/5 backdrop-blur-sm p-8 animate-fade-in">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <CheckCircleIcon class="h-10 w-10 text-green-600" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Booking Confirmed!</h3>
            <p class="text-gray-600 mb-6">{{ message }}</p>

            <div v-if="event" class="bg-green-50 rounded-lg p-4 border border-green-200 mb-6 text-left">
                <h4 class="text-sm font-semibold text-green-900 mb-2">Appointment Details</h4>
                <p class="text-sm text-green-800 mb-1"><span class="font-medium">Meeting:</span> {{ event.title }}</p>
                <p class="text-sm text-green-800 mb-1">
                    <span class="font-medium">When:</span>
                    {{
                        new Date(event.starts_at).toLocaleDateString('en-US', {
                            weekday: 'long',
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric',
                        })
                    }}
                </p>
                <p class="text-sm text-green-800">
                    <span class="font-medium">Time:</span>
                    {{
                        new Date(event.starts_at).toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true,
                        })
                    }}
                    -
                    {{
                        new Date(event.ends_at).toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true,
                        })
                    }}
                </p>
            </div>

            <p class="text-sm text-gray-500 mb-6">A confirmation email has been sent to your email address.</p>

            <button
                @click="emit('book-another')"
                class="px-6 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all shadow-lg shadow-primary-600/20"
            >
                Book Another Appointment
            </button>
        </div>
    </div>
</template>
