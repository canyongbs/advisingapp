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
    import { ArrowRightEndOnRectangleIcon, ArrowRightStartOnRectangleIcon } from '@heroicons/vue/20/solid';
    import { HomeIcon } from '@heroicons/vue/24/outline';
    import { storeToRefs } from 'pinia';
    import { ref } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { consumer } from '../Services/Consumer.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { globalSearchQuery } from '../Stores/globalState.js';
    import { useTokenStore } from '../Stores/token.js';

    const route = useRoute();
    const router = useRouter();
    const { user, requiresAuthentication } = storeToRefs(useAuthStore());

    const { removeToken } = useTokenStore();

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
        headerLogo: {
            type: String,
            required: true,
        },
        appName: {
            type: String,
            required: true,
        },
    });

    const emit = defineEmits(['showLogin']);

    const sidebarOpen = ref(false);

    const logout = () => {
        const { post } = consumer();

        post(props.apiUrl + '/logout').then((response) => {
            if (!response.data.success) {
                return;
            }

            removeToken();
            window.location.href = response.data.redirect_url;
        });
    };

    const menuItems = ref([
        {
            label: 'Home',
            routeName: 'home',
            icon: HomeIcon,
        },
    ]);

    const onSearch = () => {
        router.push({ name: 'home', query: { search: globalSearchQuery.value } });
    };
</script>

<template>
    <!-- Sidebar close overlay -->
    <Transition
        enter-active-class="transition duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-gray-950/50 xl:hidden" @click="sidebarOpen = false"></div>
    </Transition>

    <!-- Mobile sidebar -->
    <aside
        class="fixed inset-y-0 start-0 z-30 flex h-dvh w-80 flex-col bg-white shadow-xl ring-1 ring-gray-950/5 transition-all xl:hidden"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <header class="flex h-16 shrink-0 items-center bg-white px-6 ring-1 ring-gray-950/5 shadow-xs">
            <div class="flex-1">
                <router-link :to="{ name: 'home' }" @click="sidebarOpen = false">
                    <img :src="headerLogo" :alt="appName" class="h-9 block" />
                </router-link>
            </div>
        </header>

        <nav class="flex grow flex-col gap-y-7 overflow-x-hidden overflow-y-auto px-4 py-4">
            <ul class="flex flex-col gap-y-1">
                <li v-for="item in menuItems" :key="item.label">
                    <router-link :to="{ name: item.routeName }" custom v-slot="{ navigate, isActive, isExactActive }">
                        <a
                            @click="
                                navigate();
                                sidebarOpen = false;
                            "
                            class="relative flex items-center gap-x-3 rounded-lg p-2 outline-none transition duration-75 cursor-pointer"
                            :class="
                                isActive || isExactActive
                                    ? 'bg-gray-100'
                                    : 'hover:bg-gray-100 focus-visible:bg-gray-100'
                            "
                        >
                            <component
                                :is="item.icon"
                                class="size-6"
                                :class="isActive || isExactActive ? 'text-brand-700' : 'text-gray-400'"
                            />
                            <span
                                class="flex-1 truncate text-sm font-medium"
                                :class="isActive || isExactActive ? 'text-brand-700' : 'text-gray-700'"
                            >
                                {{ item.label }}
                            </span>
                        </a>
                    </router-link>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Topbar -->
    <div class="sticky top-0 z-20 overflow-x-clip">
        <nav class="flex min-h-16 items-center bg-white px-4 shadow-xs ring-1 ring-gray-950/5">
            <!-- Mobile sidebar toggle -->
            <button
                type="button"
                class="relative flex size-9 items-center justify-center rounded-lg text-gray-500 outline-none transition duration-75 hover:text-gray-600 focus-visible:ring-2 focus-visible:ring-brand-600 xl:hidden"
                @click="sidebarOpen = true"
            >
                <svg
                    class="size-6"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                    />
                </svg>
            </button>

            <!-- Logo (desktop) -->
            <div class="me-6 hidden items-center xl:flex">
                <router-link :to="{ name: 'home' }" class="ms-3">
                    <img :src="headerLogo" :alt="appName" class="h-9 block" />
                </router-link>
            </div>

            <!-- Nav items (desktop) -->
            <ul class="ms-4 me-4 hidden items-center gap-x-2 xl:my-2 xl:flex xl:flex-wrap xl:gap-y-1">
                <li v-for="item in menuItems" :key="item.label">
                    <router-link :to="{ name: item.routeName }" custom v-slot="{ navigate, isActive, isExactActive }">
                        <a
                            @click="navigate"
                            class="flex items-center justify-center gap-x-2 rounded-lg px-3 py-2 outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 cursor-pointer"
                            :class="(isActive || isExactActive) && 'bg-gray-50'"
                        >
                            <span
                                class="text-sm font-medium"
                                :class="isActive || isExactActive ? 'text-brand-600' : 'text-gray-700'"
                            >
                                {{ item.label }}
                            </span>
                        </a>
                    </router-link>
                </li>
            </ul>

            <!-- End section -->
            <div class="ms-auto flex items-center gap-x-4">
                <!-- Global search -->
                <form
                    v-if="!['home', 'view-category'].includes(route.name)"
                    @submit.prevent="onSearch"
                    class="flex items-center max-w-[12rem]"
                >
                    <div
                        class="flex rounded-lg bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus-within:ring-2 focus-within:ring-brand-600"
                    >
                        <div class="flex items-center gap-x-3 ps-3 pe-2">
                            <svg
                                class="size-5 text-gray-400"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <input
                                v-model="globalSearchQuery"
                                type="search"
                                autocomplete="off"
                                placeholder="Search"
                                class="block w-full appearance-none border-none bg-transparent ps-0 px-3 py-1.5 text-start text-sm leading-6 text-gray-950 placeholder:text-gray-400 focus:ring-0 focus:outline-none"
                            />
                        </div>
                    </div>
                </form>

                <!-- Sign in / Sign out -->
                <div v-if="requiresAuthentication">
                    <button
                        v-if="user"
                        type="button"
                        @click="logout"
                        class="relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 bg-brand-600 text-white hover:bg-brand-500 focus-visible:ring-2 focus-visible:ring-brand-500/50"
                    >
                        <ArrowRightStartOnRectangleIcon class="size-5" />
                        Sign out
                    </button>
                    <button
                        v-else
                        type="button"
                        @click="emit('showLogin')"
                        class="relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 bg-brand-600 text-white hover:bg-brand-500 focus-visible:ring-2 focus-visible:ring-brand-500/50"
                    >
                        <ArrowRightEndOnRectangleIcon class="size-5" />
                        Sign in
                    </button>
                </div>
            </div>
        </nav>
    </div>
</template>
