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
    import { Bars3Icon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
    import { computed, defineEmits, defineProps } from 'vue';

    const props = defineProps({
        modelValue: {
            type: [String, null],
            default: null,
        },
    });

    const emit = defineEmits(['update:modelValue', 'sidebarOpened']);

    const searchQuery = computed({
        get() {
            return props.modelValue;
        },
        set(value) {
            emit('update:modelValue', value);
        },
    });
</script>

<template>
    <div class="sticky top-0 z-40 bg-gray-50 lg:hidden">
        <button class="w-full p-3" type="button" @click="emit('sidebarOpened')">
            <span class="sr-only">Open sidebar</span>

            <Bars3Icon class="h-6 w-6 text-gray-900"></Bars3Icon>
        </button>
    </div>

    <div
        class="bg-[linear-gradient(to_right_bottom,rgba(var(--primary-500),1),rgba(var(--primary-800),1))] w-full px-6"
    >
        <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
            <div class="flex flex-col gap-y-1 text-left">
                <h3 class="text-3xl font-semibold text-white">Need help?</h3>
                <p class="text-primary-100">Search our resource hub for advice and answers</p>
            </div>

            <form action="#" method="GET">
                <label for="search" class="sr-only">Search</label>

                <div class="relative rounded">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                    </div>

                    <input
                        id="search"
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search for articles and categories"
                        class="block w-full rounded border-0 py-3 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-200 sm:text-sm sm:leading-6"
                    />
                </div>
            </form>
        </div>
    </div>
</template>
