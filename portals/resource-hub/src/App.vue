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
    import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
    import { RouterView, useRoute, useRouter } from 'vue-router';
    import AppLoading from './Components/AppLoading.vue';
    import axios from './Globals/Axios.js';
    import Login from './Pages/Login.vue';
    import { consumer } from './Services/Consumer.js';
    import determineIfUserIsAuthenticated from './Services/DetermineIfUserIsAuthenticated.js';
    import { useAuthStore } from './Stores/auth.js';
    import { useFeatureStore } from './Stores/feature.js';
    import { useTokenStore } from './Stores/token.js';

    /**
     * Computes a perceptually-correct contrast colour for text placed on top of
     * a given sRGB background expressed as "R G B" space-separated integers.
     * Returns '#111827' (near-black) for light palettes (yellow, lime, amber, etc.)
     * and 'white' for dark palettes, so primary-variant button text is always readable
     * regardless of which static primary colour the admin has configured.
     */
    function contrastOnColor(rgbString) {
        const parts = String(rgbString ?? '')
            .trim()
            .split(/\s+/)
            .map(Number);
        if (parts.length !== 3 || parts.some((n) => isNaN(n))) return 'white';
        const [r, g, b] = parts.map((c) => {
            const s = c / 255;
            return s <= 0.04045 ? s / 12.92 : Math.pow((s + 0.055) / 1.055, 2.4);
        });
        const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;
        return luminance > 0.35 ? '#111827' : 'white';
    }

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

    const searchUrl = ref(props.searchUrl);
    const apiUrl = ref(props.apiUrl);
    const userAuthenticationUrl = ref(props.userAuthenticationUrl);
    const appUrl = ref(props.appUrl);

    const errorLoading = ref(false);
    const loading = ref(true);
    const userIsAuthenticated = ref(false);
    const requiresAuthentication = ref(false);
    const hasServiceManagement = ref(false);
    const isStatusEnabled = ref(false);
    const isAdvisoryEnabled = ref(false);
    const isAssetEnabled = ref(false);
    const isLicenseEnabled = ref(false);
    const hasAssets = ref(false);
    const hasLicense = ref(false);
    const hasTasks = ref(false);
    const showLogin = ref(false);

    const portalPrimaryColor = ref('');
    const portalRounding = ref('');

    /** Contrast-safe text colour for primary-variant buttons (adapts to any static palette). */
    const primaryOnColor = computed(() => contrastOnColor(portalPrimaryColor.value?.[500]));
    const categories = ref({});
    const serviceRequests = ref({});
    const headerLogo = ref('');
    const favicon = ref('');
    const tags = ref({});
    const appName = ref('');
    const footerLogo = ref('');

    const authentication = ref({
        code: null,
        email: null,
        isRequested: false,
        requestedMessage: null,
        requestUrl: null,
        url: null,
        registrationAllowed: false,
    });

    const route = useRoute();
    const router = useRouter();

    const { user } = storeToRefs(useAuthStore());

    const menuItems = computed(() => [
        {
            label: 'Home',
            routeName: 'home',
            icon: HomeIcon,
        },
    ]);

    const logout = () => {
        const { post } = consumer();
        const { removeToken } = useTokenStore();

        post(apiUrl.value + '/logout').then((response) => {
            if (!response.data.success) {
                return;
            }

            removeToken();
            window.location.href = response.data.redirect_url;
        });
    };

    const assistantWidgetLoaderUrl = ref(null);
    const assistantWidgetConfigUrl = ref(null);

    const showSignIn = computed(() => {
        return (
            !userIsAuthenticated.value && (requiresAuthentication.value || showLogin.value || route.meta?.requiresAuth)
        );
    });

    function loadAssistantWidget() {
        if (!assistantWidgetLoaderUrl.value || !assistantWidgetConfigUrl.value) {
            return;
        }

        if (document.getElementById('assistant-widget-root')) {
            return;
        }

        const script = document.createElement('script');
        script.src = assistantWidgetLoaderUrl.value;
        script.setAttribute('data-config', assistantWidgetConfigUrl.value);
        document.body.appendChild(script);

        const observer = new MutationObserver(() => {
            if (document.querySelector('assistant-widget-embed')) {
                observer.disconnect();
                updateWidgetServiceManagement();
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    function updateWidgetServiceManagement() {
        const widget = document.querySelector('assistant-widget-embed');
        if (!widget) return;

        if (hasServiceManagement.value && userIsAuthenticated.value) {
            widget.setAttribute('portal-service-management', '');
        } else {
            widget.removeAttribute('portal-service-management');
        }
    }

    watch([() => hasServiceManagement.value, () => userIsAuthenticated.value], updateWidgetServiceManagement, {
        immediate: true,
    });

    function handleOpenServiceRequest() {
        window.dispatchEvent(new CustomEvent('assistant:close'));
        router.push({ name: 'create-service-request' });
    }

    window.addEventListener('assistant:open-service-request', handleOpenServiceRequest);

    async function handleWidgetAuthenticated(event) {
        const { token } = event.detail ?? {};
        if (!token) return;
        await useTokenStore().setToken(token);
        const isAuth = await determineIfUserIsAuthenticated(userAuthenticationUrl.value);
        if (isAuth) {
            userIsAuthenticated.value = true;
            await getKnowledgeManagementPortal();
            await getData();
        }
    }

    window.addEventListener('assistant-widget:authenticated', handleWidgetAuthenticated);

    onUnmounted(() => {
        window.removeEventListener('assistant:open-service-request', handleOpenServiceRequest);
        window.removeEventListener('assistant-widget:authenticated', handleWidgetAuthenticated);
    });

    watch([showSignIn, assistantWidgetLoaderUrl], ([isSignIn]) => {
        if (isSignIn) {
            const widgetRoot = document.getElementById('assistant-widget-root');
            if (widgetRoot) {
                widgetRoot.style.display = 'none';
            }
        } else {
            const widgetRoot = document.getElementById('assistant-widget-root');
            if (widgetRoot) {
                widgetRoot.style.display = '';
                updateWidgetServiceManagement();
            } else {
                loadAssistantWidget();
            }
        }
    });

    onMounted(async () => {
        if (props.appTitle) {
            document.title = props.appTitle;
        }
    });

    watch(
        route,
        async () => {
            await getKnowledgeManagementPortal().then(async () => {
                const { requiresAuthentication } = useAuthStore();

                if (userAuthenticationUrl.value) {
                    userIsAuthenticated.value = await determineIfUserIsAuthenticated(userAuthenticationUrl.value);
                }

                if (userIsAuthenticated.value || !requiresAuthentication.value) {
                    await getData();
                    return;
                }
                loading.value = false;
            });
        },
        {
            immediate: true,
        },
    );

    watch(favicon, async (newFavicon, oldFavicon) => {
        if (newFavicon != oldFavicon) {
            var link = document.querySelector("link[rel='icon']");
            if (!link) {
                link = document.createElement('link');
                link.rel = 'icon';
                document.getElementsByTagName('head')[0].appendChild(link);
            }
            link.href = favicon.value;
        }
    });

    async function getKnowledgeManagementPortal() {
        await axios
            .get(props.url)
            .then((response) => {
                errorLoading.value = false;

                if (response.error) {
                    throw new Error(response.error);
                }

                if (response.data.search_url) {
                    searchUrl.value = response.data.search_url;
                }
                if (response.data.api_url) {
                    apiUrl.value = response.data.api_url;
                }
                if (response.data.user_authentication_url) {
                    userAuthenticationUrl.value = response.data.user_authentication_url;
                }
                if (response.data.app_url) {
                    appUrl.value = response.data.app_url;
                }

                const { setRequiresAuthentication } = useAuthStore();

                const {
                    setHasServiceManagement,
                    setHasAssets,
                    setHasLicense,
                    setHasTasks,
                    setIsStatusEnabled,
                    setIsAdvisoryEnabled,
                    setIsAssetEnabled,
                    setIsLicenseEnabled,
                } = useFeatureStore();

                portalPrimaryColor.value = response.data.primary_color;

                headerLogo.value = response.data.header_logo;

                favicon.value = response.data.favicon;

                appName.value = response.data.app_name;

                footerLogo.value = response.data.footer_logo;

                setRequiresAuthentication(response.data.requires_authentication).then(() => {
                    requiresAuthentication.value = response.data.requires_authentication;

                    if (response.data.assistant_widget_loader_url && response.data.assistant_widget_config_url) {
                        assistantWidgetLoaderUrl.value = response.data.assistant_widget_loader_url;
                        assistantWidgetConfigUrl.value = response.data.assistant_widget_config_url;
                    }
                });

                setHasServiceManagement(response.data.service_management_enabled).then(() => {
                    hasServiceManagement.value = response.data.service_management_enabled;
                });

                setHasAssets(response.data.has_assets).then(() => {
                    hasAssets.value = response.data.has_assets;
                });

                setHasLicense(response.data.has_license).then(() => {
                    hasLicense.value = response.data.has_license;
                });

                setHasTasks(response.data.has_tasks).then(() => {
                    hasTasks.value = response.data.has_tasks;
                });

                setIsStatusEnabled(response.data.service_monitoring_enabled).then(() => {
                    isStatusEnabled.value = response.data.service_monitoring_enabled;
                });

                setIsAdvisoryEnabled(response.data.advisory_management_enabled).then(() => {
                    isAdvisoryEnabled.value = response.data.advisory_management_enabled;
                });

                setIsAssetEnabled(response.data.asset_management_enabled).then(() => {
                    isAssetEnabled.value = response.data.asset_management_enabled;
                });

                setIsLicenseEnabled(response.data.license_management_enabled).then(() => {
                    isLicenseEnabled.value = response.data.license_management_enabled;
                });

                authentication.value.requestUrl = response.data.authentication_url ?? null;

                portalRounding.value = {
                    none: {
                        sm: '0px',
                        default: '0px',
                        md: '0px',
                        lg: '0px',
                        full: '0px',
                    },
                    sm: {
                        sm: '0.125rem',
                        default: '0.25rem',
                        md: '0.375rem',
                        lg: '0.5rem',
                        full: '9999px',
                    },
                    md: {
                        sm: '0.25rem',
                        default: '0.375rem',
                        md: '0.5rem',
                        lg: '0.75rem',
                        full: '9999px',
                    },
                    lg: {
                        sm: '0.375rem',
                        default: '0.5rem',
                        md: '0.75rem',
                        lg: '1rem',
                        full: '9999px',
                    },
                    full: {
                        sm: '9999px',
                        default: '9999px',
                        md: '9999px',
                        lg: '9999px',
                        full: '9999px',
                    },
                }[response.data.rounding ?? 'md'];
            })
            .catch((error) => {
                errorLoading.value = true;
                console.error(`Resource Hub Embed ${error}`);
            });
    }

    async function getData() {
        await getResourceHubPortalCategories()
            .then((response) => {
                errorLoading.value = false;

                if (response.error) {
                    throw new Error(response.error);
                }
                categories.value = response;

                loading.value = false;
            })
            .catch((error) => {
                errorLoading.value = true;
                loading.value = false;
                console.error(`Resource Hub Portal Embed ${error}`);
            });
    }

    async function getResourceHubPortalCategories() {
        const { get } = consumer();

        return get(`${apiUrl.value}/categories`).then((response) => {
            if (response.error) {
                throw new Error(response.error);
            }

            return response.data;
        });
    }

    async function getServiceRequests() {
        const { get } = consumer();

        return get(`${apiUrl.value}/service-requests`).then((response) => {
            if (response.error) {
                throw new Error(response.error);
            }

            return response.data;
        });
    }

    async function getTags() {
        const { get } = consumer();

        return get(`${apiUrl.value}/tags`).then((response) => {
            if (response.error) {
                throw new Error(response.error);
            }

            return response.data;
        });
    }

    async function authenticate(formData, node, done) {
        node.clearErrors();

        const { setToken } = useTokenStore();
        const { setUser } = useAuthStore();

        const {
            setHasServiceManagement,
            setHasAssets,
            setHasLicense,
            setHasTasks,
            setIsStatusEnabled,
            setIsAdvisoryEnabled,
            setIsAssetEnabled,
            setIsLicenseEnabled,
        } = useFeatureStore();

        if (authentication.value.isRequested) {
            let data = {
                code: formData.code,
            };

            if (authentication.value.registrationAllowed) {
                data = {
                    ...data,
                    email: formData.email,
                    first_name: formData.first_name,
                    last_name: formData.last_name,
                    preferred: formData.preferred,
                    mobile: formData.mobile,
                    phone: formData.phone,
                    sms_opt_out: formData.sms_opt_out,
                };
            }

            axios
                .post(authentication.value.url, data)
                .then((response) => {
                    if (response.errors) {
                        node.setErrors([], response.errors);

                        return;
                    }

                    if (response.data.is_expired) {
                        node.setErrors(['The authentication code expires after 24 hours. Please authenticate again.']);

                        authentication.value.isRequested = false;
                        authentication.value.requestedMessage = null;
                        authentication.value.url = null;
                        authentication.value.registrationAllowed = false;

                        return;
                    }

                    if (response.data.success === true) {
                        setToken(response.data.token);
                        setUser(response.data.user);

                        setHasServiceManagement(response.data.service_management_enabled).then(() => {
                            hasServiceManagement.value = response.data.service_management_enabled;
                        });

                        setHasAssets(response.data.has_assets).then(() => {
                            hasAssets.value = response.data.has_assets;
                        });

                        setHasLicense(response.data.has_license).then(() => {
                            hasLicense.value = response.data.has_license;
                        });

                        setHasTasks(response.data.has_tasks).then(() => {
                            hasTasks.value = response.data.has_tasks;
                        });

                        setIsStatusEnabled(response.data.service_monitoring_enabled).then(() => {
                            isStatusEnabled.value = response.data.service_monitoring_enabled;
                        });

                        setIsAdvisoryEnabled(response.data.advisory_management_enabled).then(() => {
                            isAdvisoryEnabled.value = response.data.advisory_management_enabled;
                        });

                        setIsAssetEnabled(response.data.asset_management_enabled).then(() => {
                            isAssetEnabled.value = response.data.asset_management_enabled;
                        });

                        setIsLicenseEnabled(response.data.license_management_enabled).then(() => {
                            isLicenseEnabled.value = response.data.license_management_enabled;
                        });

                        if (response.data.assistant_widget_loader_url && response.data.assistant_widget_config_url) {
                            assistantWidgetLoaderUrl.value = response.data.assistant_widget_loader_url;
                            assistantWidgetConfigUrl.value = response.data.assistant_widget_config_url;
                        }

                        userIsAuthenticated.value = true;

                        getData();
                    }
                })
                .catch((error) => {
                    node.setErrors([], error.response.data.errors);
                })
                .finally(() => done());

            return;
        }

        axios
            .post(authentication.value.requestUrl, {
                email: formData.email,
            })
            .then((response) => {
                if (!response.data.authentication_url) {
                    node.setErrors([response.data.message]);

                    return;
                }

                authentication.value.isRequested = true;
                authentication.value.requestedMessage = response.data.message;
                authentication.value.url = response.data.authentication_url;
            })
            .catch((error) => {
                const status = error.response.status;
                const data = error.response.data;

                if (status === 404 && data.registrationAllowed === true) {
                    authentication.value.registrationAllowed = true;
                    authentication.value.isRequested = true;
                    authentication.value.requestedMessage = data.message;
                    authentication.value.url = data.authentication_url;

                    return;
                }

                node.setErrors([], data.errors);
            })
            .finally(() => done());
    }
</script>

<template>
    <div
        class="font-sans bg-gray-50 min-h-screen w-full max-w-full"
        :style="{
            '--primary-50': portalPrimaryColor[50],
            '--primary-100': portalPrimaryColor[100],
            '--primary-200': portalPrimaryColor[200],
            '--primary-300': portalPrimaryColor[300],
            '--primary-400': portalPrimaryColor[400],
            '--primary-500': portalPrimaryColor[500],
            '--primary-600': portalPrimaryColor[600],
            '--primary-700': portalPrimaryColor[700],
            '--primary-800': portalPrimaryColor[800],
            '--primary-900': portalPrimaryColor[900],
            '--primary-950': portalPrimaryColor[950],
            '--primary-on-color': primaryOnColor,
            '--rounding-sm': portalRounding.sm,
            '--rounding': portalRounding.default,
            '--rounding-md': portalRounding.md,
            '--rounding-lg': portalRounding.lg,
            '--rounding-full': portalRounding.full,
        }"
    >
        <div>
            <link rel="stylesheet" v-bind:href="props.cssUrl" />
        </div>
        <div v-if="loading">
            <AppLoading />
        </div>

        <div v-else>
            <Login
                v-if="!userIsAuthenticated && (requiresAuthentication || showLogin || route.meta.requiresAuth)"
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
                    @show-login="showLogin = true"
                    @logout="logout"
                />

                <main class="flex-1">
                    <div v-if="errorLoading" class="text-center w-full">
                        <h1 class="text-3xl font-bold text-red-500">Error Loading the Resource Hub</h1>
                        <p class="text-lg text-red-500">Please try again later</p>
                    </div>

                    <RouterView
                        :search-url="searchUrl"
                        :api-url="apiUrl"
                        :categories="categories"
                        :service-requests="serviceRequests"
                        :tags="tags"
                        v-else
                    />
                </main>

                <Footer :logo="footerLogo" :app-name="appName" />
            </div>
        </div>
    </div>
</template>
