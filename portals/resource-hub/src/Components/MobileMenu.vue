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
    import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { ref } from 'vue';

    const props = defineProps({
        visibleMenuItems: {
            type: Object,
            required: true,
        },
    });

    const visible = ref(false);

    const openDrawer = () => {
        visible.value = true;
    };

    const closeDrawer = () => {
        visible.value = false;
    };
</script>

<template>
    <div>
        <Bars3Icon @click="openDrawer" class="h-6 w-6 text-gray-700" aria-hidden="true" />

        <Transition name="backdrop">
            <div v-if="visible" class="fixed inset-0 z-40 bg-black/50" @click="closeDrawer"></div>
        </Transition>

        <Transition name="drawer">
            <div v-if="visible" class="fixed top-0 right-0 w-64 h-full bg-white shadow-xl z-50" @click.stop>
                <div class="flex justify-between items-center px-4 py-3 border-b">
                    <h3 class="text-lg font-semibold text-gray-700">Menu</h3>
                    <XMarkIcon @click="closeDrawer" class="h-5 w-5 text-gray-700" />
                </div>

                <div class="p-4 space-y-5">
                    <template v-for="item in visibleMenuItems" :key="item.label">
                        <router-link
                            :to="{ name: item.routeName }"
                            custom
                            v-slot="{ navigate, isActive, isExactActive }"
                        >
                            <a
                                @click="navigate"
                                class="flex items-center font-medium text-sm"
                                :class="[
                                    isActive || isExactActive ? 'text-brand-500' : 'text-gray-700',
                                    'hover:text-brand-500',
                                ]"
                            >
                                {{ item.label }}
                            </a>
                        </router-link>
                    </template>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
    .drawer-enter-active,
    .drawer-leave-active {
        transition:
            transform 0.3s ease-out,
            opacity 0.3s ease-out;
    }

    .drawer-enter-from,
    .drawer-leave-to {
        transform: translateX(100%);
        opacity: 0;
    }

    .drawer-enter-to,
    .drawer-leave-from {
        transform: translateX(0);
        opacity: 1;
    }

    .backdrop-enter-active,
    .backdrop-leave-active {
        transition: opacity 0.3s ease-out;
    }

    .backdrop-enter-from,
    .backdrop-leave-to {
        opacity: 0;
    }

    .backdrop-enter-to,
    .backdrop-leave-from {
        opacity: 1;
    }
</style>
