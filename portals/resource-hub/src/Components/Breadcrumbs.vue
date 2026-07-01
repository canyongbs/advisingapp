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
    import { defineEmits, defineProps } from 'vue';

    defineProps({
        currentCrumb: {
            type: String,
            required: true,
        },
        breadcrumbs: {
            type: Array,
            default: () => [],
        },
    });

    const emit = defineEmits(['crumb-click']);
</script>

<template>
    <nav aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-x-2">
            <li v-if="currentCrumb !== 'Home'" class="flex items-center gap-x-2 text-sm font-medium">
                <router-link :to="{ name: 'home' }" class="text-white/70 transition duration-75 hover:text-white">
                    Home
                </router-link>
            </li>
            <li
                v-for="(crumb, index) in breadcrumbs"
                :key="(crumb.id || crumb.name) + index"
                class="flex items-center gap-x-2 text-sm font-medium"
            >
                <ChevronRightIcon class="size-5 text-white/40" aria-hidden="true" />

                <template v-if="crumb.route">
                    <router-link
                        :to="{ name: crumb.route, params: crumb.params }"
                        class="text-white/70 transition duration-75 hover:text-white"
                    >
                        {{ crumb.name }}
                    </router-link>
                </template>
                <template v-else>
                    <button
                        @click="emit('crumb-click', crumb)"
                        class="text-white/70 transition duration-75 hover:text-white"
                    >
                        {{ crumb.name }}
                    </button>
                </template>
            </li>
            <li class="flex items-center gap-x-2 text-sm font-medium">
                <ChevronRightIcon v-if="currentCrumb !== 'Home'" class="size-5 text-white/40" aria-hidden="true" />
                <span class="text-white">{{ currentCrumb }}</span>
            </li>
        </ol>
    </nav>
</template>
