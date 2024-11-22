<!--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
import { defineProps } from 'vue';

defineProps({
    categories: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div
        class="overflow-hidden rounded bg-gray-200 shadow-sm ring-1 ring-black/5 sm:grid sm:grid-cols-2 sm:gap-px sm:divide-y-0"
    >
        <div
            v-for="(category, categoryId) in categories"
            :key="category.id"
            class="group relative bg-white p-6 focus-within:bg-gray-50"
        >
            <span
                v-if="category.icon"
                v-html="category.icon"
                class="pointer-events-none absolute top-6 text-primary-700"
                aria-hidden="true"
            >
            </span>
            <div class="mt-8">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    <router-link :to="{ name: 'view-category', params: { categoryId: category.id } }">
                        <span class="absolute inset-0" aria-hidden="true" />
                        {{ category.name }}
                    </router-link>
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    {{ category.description }}
                </p>
            </div>
            <span
                class="pointer-events-none absolute right-6 top-6 text-gray-300 transition group-hover:text-primary-500"
                aria-hidden="true"
            >
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"
                    />
                </svg>
            </span>
        </div>

        <div v-show="categories.length % 2 === 1" class="hidden sm:block bg-white"></div>
    </div>
</template>
