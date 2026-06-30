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
    import { ChevronRightIcon } from '@heroicons/vue/20/solid';

    defineProps({
        to: {
            type: [Object, String],
            required: true,
        },
        icon: {
            type: String,
            default: null,
        },
        name: {
            type: String,
            required: true,
        },
        description: {
            type: String,
            default: null,
        },
    });
</script>

<template>
    <router-link
        :to="to"
        class="group flex items-start w-full gap-3 rounded-xl bg-white px-6 py-4 ring-1 ring-gray-950/5 transition duration-75 hover:bg-gray-50"
    >
        <div
            v-if="icon"
            v-html="icon"
            class="shrink-0 text-gray-400 [&>svg]:size-6 transition duration-75 group-hover:text-brand-500"
            aria-hidden="true"
        ></div>

        <div class="mt-0.5 grid flex-1 gap-1 min-w-0">
            <span class="text-sm font-medium text-gray-950">{{ name }}</span>
            <p class="overflow-hidden text-sm text-pretty break-words text-gray-500">
                {{ description }}
            </p>
        </div>

        <ChevronRightIcon
            class="shrink-0 size-5 self-center text-gray-400 opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100"
        />
    </router-link>
</template>
