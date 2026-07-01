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
    import { XMarkIcon } from '@heroicons/vue/24/outline/index.js';

    defineProps({
        icon: {
            type: Object,
            default: null,
        },
        contained: {
            type: Boolean,
            default: true,
        },
    });
</script>

<template>
    <section class="px-6 py-12" :class="contained && 'rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5'">
        <div class="mx-auto grid max-w-lg justify-items-center text-center">
            <div class="mb-4 flex size-12 items-center justify-center rounded-full bg-gray-100">
                <component :is="icon || XMarkIcon" class="size-6 text-gray-400" />
            </div>

            <div class="grid justify-items-center text-center">
                <h4 class="text-base font-semibold leading-6 text-gray-950">
                    <slot name="heading" />
                </h4>

                <p v-if="$slots.description" class="mt-1 text-sm text-gray-500">
                    <slot name="description" />
                </p>

                <div v-if="$slots.actions" class="mt-6">
                    <slot name="actions" />
                </div>
            </div>
        </div>
    </section>
</template>
