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
    import Footer from '@common/portal/Footer.vue';
    import Header from '@common/portal/Header.vue';
    import { HomeIcon } from '@heroicons/vue/24/outline';
    import { storeToRefs } from 'pinia';
    import { computed, onMounted, ref } from 'vue';
    import { RouterView, useRoute, useRouter } from 'vue-router';
    import { useIsDataLoading } from 'vue-router/experimental';
    import BootScreen from './Components/BootScreen.vue';
    import NavigationProgress from './Components/NavigationProgress.vue';
    import { usePortalAuth } from './Composables/usePortalAuth.js';
    import { usePortalTheme } from './Composables/usePortalTheme.js';
    import Login from './Pages/Login.vue';
    import { useAuthStore } from './Stores/auth.js';
    import { useConfigStore } from './Stores/config.js';

    const props = defineProps({
        url: {
            type: String,
            required: true,
        },
        searchUrl: {
            type: String,
            default: null,
        },
        apiUrl: {
            type: String,
            default: null,
        },
        accessUrl: {
            type: String,
            default: null,
        },
        userAuthenticationUrl: {
            type: String,
            default: null,
        },
        appUrl: {
            type: String,
            default: null,
        },
        appTitle: {
            type: String,
            default: null,
        },
        cssUrl: {
            type: String,
            default: null,
        },
    });

    const route = useRoute();
    const router = useRouter();

    const config = useConfigStore();
    const { appName, headerLogo, footerLogo, errorLoading } = storeToRefs(config);

    const authStore = useAuthStore();
    const { user, requiresAuthentication, userIsAuthenticated } = storeToRefs(authStore);

    const { themeStyles } = usePortalTheme();

    const { authentication, authenticate, logout } = usePortalAuth();

    // Reflects the global data-loader pending state so the progress bar animates
    // during any client-side navigation that awaits route data.
    const isNavigating = useIsDataLoading();

    const loading = ref(true);
    const showLogin = ref(false);

    const menuItems = computed(() => [
        {
            label: 'Home',
            routeName: 'home',
            icon: HomeIcon,
        },
    ]);

    const showSignIn = computed(
        () =>
            !userIsAuthenticated.value && (requiresAuthentication.value || showLogin.value || route.meta?.requiresAuth),
    );

    // Hide navbar search on pages that already show their own hero search.
    const hideHeaderSearch = computed(() => ['home', 'view-category', 'view-subcategory'].includes(route.name));

    function onHeaderSearch(query) {
        router.push({ name: 'home', query: { search: query } });
    }

    onMounted(async () => {
        if (props.appTitle) {
            document.title = props.appTitle;
        }

        // Resolves once boot (config + auth gate) and the initial route's data loaders
        // have completed, so the shell renders with its data already present. Errors are
        // surfaced via `config.errorLoading`; we still drop the boot screen either way.
        try {
            await router.isReady();
        } catch {
            // Initial navigation failed (e.g. unauthenticated); the sign-in screen handles it.
        } finally {
            loading.value = false;
        }
    });
</script>

<template>
    <div class="font-sans bg-gray-50 min-h-screen w-full max-w-full" :style="themeStyles">
        <div>
            <link rel="stylesheet" v-bind:href="props.cssUrl" />
        </div>

        <BootScreen v-if="loading" label="Loading Resource Hub..." />

        <div v-else>
            <NavigationProgress :active="isNavigating" />

            <Login
                v-if="showSignIn"
                v-model:authentication="authentication"
                :requires-authentication="requiresAuthentication"
                :header-logo="headerLogo"
                :footer-logo="footerLogo"
                :app-name="appName"
                @authenticate="authenticate"
                @cancel="showLogin = false"
            />
            <div v-else class="min-h-screen flex flex-col">
                <Header
                    :header-logo="headerLogo"
                    :app-name="appName"
                    :user="user"
                    :requires-authentication="requiresAuthentication"
                    :menu-items="menuItems"
                    :hide-search="hideHeaderSearch"
                    @show-login="showLogin = true"
                    @logout="logout"
                    @search="onHeaderSearch"
                />

                <main class="flex-1">
                    <div v-if="errorLoading" class="text-center w-full">
                        <h1 class="text-3xl font-bold text-red-500">Error Loading the Resource Hub</h1>
                        <p class="text-lg text-red-500">Please try again later</p>
                    </div>

                    <RouterView v-else />
                </main>

                <Footer :logo="footerLogo" :app-name="appName" />
            </div>
        </div>
    </div>
</template>
