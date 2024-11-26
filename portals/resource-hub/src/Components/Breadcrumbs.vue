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
import { ChevronRightIcon, HomeIcon } from '@heroicons/vue/24/outline';
import { defineProps } from 'vue';

defineProps({
    currentCrumb: {
        type: String,
        required: true,
    },
    breadcrumbs: {
        type: Object,
        default: {},
    },
});
</script>

<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
            <li>
                <div>
                    <router-link :to="{ name: 'home' }" class="text-gray-400 hover:text-gray-500">
                        <HomeIcon class="h-5 w-5 flex-shrink-0" aria-hidden="true" />
                        <span class="sr-only">Home</span>
                    </router-link>
                </div>
            </li>
            <li v-for="crumb in breadcrumbs" :key="crumb.route">
                <div class="flex items-center">
                    <ChevronRightIcon class="h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />

                    <router-link
                        :to="{ name: crumb.route }"
                        class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        {{ crumb.name }}
                    </router-link>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <ChevronRightIcon class="h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />

                    <div class="ml-4 text-sm font-medium text-primary-600">
                        {{ currentCrumb }}
                    </div>
                </div>
            </li>
        </ol>
    </nav>
</template>
