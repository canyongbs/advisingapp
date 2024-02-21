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
import { defineProps, onMounted, ref, watch } from 'vue';
import attachRecaptchaScript from '../../../app-modules/integration-google-recaptcha/resources/js/Services/AttachRecaptchaScript.js';
import getRecaptchaToken from '../../../app-modules/integration-google-recaptcha/resources/js/Services/GetRecaptchaToken.js';
import AppLoading from '@/Components/AppLoading.vue';
import MobileSidebar from '@/Components/MobileSidebar.vue';
import DesktopSidebar from '@/Components/DesktopSidebar.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import determineIfUserIsAuthenticated from '@/Services/DetermineIfUserIsAuthenticated.js';
import axios from '@/Globals/Axios.js';

const errorLoading = ref(false);
const loading = ref(true);
const showMobileMenu = ref(false);

const requiresAuthentication = ref(false);
const userIsAuthenticated = ref(false);

onMounted(async () => {
    console.log('onMounted');

    await axios.get('/sanctum/csrf-cookie');

    // Determine if the user is authenticated by session or token...
    await determineIfUserIsAuthenticated().then((response) => {
        userIsAuthenticated.value = response;
    });

    // Load the portal contents...
    await getKnowledgeManagementPortal().then(() => {
        loading.value = false;
    });
});

const props = defineProps({
    url: {
        type: String,
        required: true,
    },
    searchUrl: {
        type: String,
        required: true,
    },
    apiUrl: {
        type: String,
        required: true,
    },
});

const scriptUrl = new URL(document.currentScript.getAttribute('src'));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const scriptQuery = Object.fromEntries(scriptUrl.searchParams);

const hostUrl = `${protocol}//${scriptHostname}`;

const portalPrimaryColor = ref('');
const portalRounding = ref('');
const categories = ref({});

const authentication = ref({
    code: null,
    email: null,
    isRequested: false,
    requestedMessage: null,
    requestUrl: null,
    url: null,
});

async function getKnowledgeManagementPortal() {
    await axios
        .get(props.url)
        .then((response) => {
            errorLoading.value = false;

            if (response.error) {
                throw new Error(response.error);
            }

            categories.value = response.data.categories;

            portalPrimaryColor.value = response.data.primary_color;

            requiresAuthentication.value = response.data.service_management_enabled;
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
            console.error(`Knowledge Management Portal Embed ${error}`);
        });
}

async function authenticate(formData, node) {
    node.clearErrors();

    if (authentication.value.isRequested) {
        await axios.get('/sanctum/csrf-cookie');
        console.log('authenticate() url', authentication.value.url);
        axios
            .post(authentication.value.url, {
                code: formData.code,
            })
            .then((response) => {
                console.log('response', response);
                if (response.errors) {
                    node.setErrors([], response.errors);

                    return;
                }

                if (response.data.is_expired) {
                    node.setErrors(['The authentication code expires after 24 hours. Please authenticate again.']);

                    authentication.value.isRequested = false;
                    authentication.value.requestedMessage = null;

                    return;
                }

                //    TODO Redirect because user is authenticated
                if (response.data.success === true) {
                    userIsAuthenticated.value = true;
                }
            })
            .catch((error) => {
                node.setErrors([error]);
            });

        return;
    }

    await axios.get('/sanctum/csrf-cookie');

    axios
        .post(authentication.value.requestUrl, {
            email: formData.email,
        })
        .then((response) => {
            console.log('response', response);
            if (response.errors) {
                node.setErrors([], response.errors);

                return;
            }

            if (!response.data.authentication_url) {
                node.setErrors([response.data.message]);

                return;
            }

            authentication.value.isRequested = true;
            authentication.value.requestedMessage = response.data.message;
            authentication.value.url = response.data.authentication_url;
        })
        .catch((error) => {
            node.setErrors([error]);
        });
}
</script>

<template>
    <div
        class="font-sans"
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
            '--rounding-sm': portalRounding.sm,
            '--rounding': portalRounding.default,
            '--rounding-md': portalRounding.md,
            '--rounding-lg': portalRounding.lg,
            '--rounding-full': portalRounding.full,
        }"
    >
        <div>
            <link rel="stylesheet" v-bind:href="hostUrl + '/js/portals/knowledge-management/style.css'" />
        </div>

        <div v-if="loading">
            <AppLoading />
        </div>

        <div v-else>
            <div
                v-if="requiresAuthentication && userIsAuthenticated === false"
                class="flex flex-col items-center justify-center"
            >
                <h1 class="text-black">This portal requires authentication...</h1>

                <FormKit type="form" @submit="authenticate" v-model="authentication">
                    <FormKit
                        type="email"
                        label="Your email address"
                        name="email"
                        validation="required|email"
                        validation-visibility="submit"
                        :disabled="authentication.isRequested"
                    />

                    <p v-if="authentication.requestedMessage" class="text-sm">
                        {{ authentication.requestedMessage }}
                    </p>

                    <FormKit
                        type="otp"
                        digits="6"
                        label="Authentication code"
                        name="code"
                        help="We’ve sent a code to your email address."
                        validation="required"
                        validation-visibility="submit"
                        v-if="authentication.isRequested"
                    />
                </FormKit>
            </div>
            <div v-else>
                <div v-if="errorLoading" class="text-center">
                    <h1 class="text-3xl font-bold text-red-500">Error Loading Portal</h1>
                    <p class="text-lg text-red-500">Please try again later</p>
                </div>

                <div v-else>
                    <MobileSidebar
                        v-if="showMobileMenu"
                        @sidebar-closed="showMobileMenu = !showMobileMenu"
                        :categories="categories"
                    ></MobileSidebar>

                    <DesktopSidebar :categories="categories"></DesktopSidebar>

                    <div class="lg:pl-72">
                        <div class="px-4 sm:px-6 lg:px-8">
                            <RouterView :search-url="searchUrl" :api-url="apiUrl" :categories="categories"></RouterView>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
