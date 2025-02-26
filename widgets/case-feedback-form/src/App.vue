<!--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
import { FormKit } from '@formkit/vue';
import { defineProps, onMounted, ref } from 'vue';
import axios from '../../../portals/resource-hub/src/Globals/Axios.js';
import determineIfUserIsAuthenticated from '../../../portals/resource-hub/src/Services/DetermineIfUserIsAuthenticated.js';
import getAppContext from '../../../portals/resource-hub/src/Services/GetAppContext.js';
import { useAuthStore } from '../../../portals/resource-hub/src/Stores/auth.js';
import { useTokenStore } from '../../../portals/resource-hub/src/Stores/token.js';
import AppLoading from '../src/Components/AppLoading.vue';
import Footer from './Components/Footer.vue';

const props = defineProps({
    url: {
        type: String,
        required: true,
    },
    apiUrl: {
        type: String,
        required: true,
    },
    accessUrl: {
        type: String,
        required: true,
    },
    userAuthenticationUrl: {
        type: String,
        required: true,
    },
    appUrl: {
        type: String,
        required: true,
    },
});

const submittedSuccess = ref(false);

const scriptUrl = new URL(document.currentScript.getAttribute('src'));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const feedbackSubmitted = ref(false);
const errorLoading = ref(false);
const loading = ref(true);
const userIsAuthenticated = ref(false);
const requiresAuthentication = ref(false);
const userGuard = ref('');
const hostUrl = `${protocol}//${scriptHostname}`;
const formSubmissionUrl = ref('');
const formPrimaryColor = ref('');
const formRounding = ref('');
const hasEnabledCsat = ref(false);
const hasEnabledNps = ref(false);
const headerLogo = ref('');
const footerLogo = ref('');
const appName = ref('');
const caseNumber = ref('');
const authentication = ref({
    code: null,
    email: null,
    isRequested: false,
    requestedMessage: null,
    requestUrl: null,
    url: null,
});

onMounted(async () => {
    const { isEmbeddedInAdvisingApp } = getAppContext(props.accessUrl);

    if (isEmbeddedInAdvisingApp) {
        await axios.get(props.appUrl + '/sanctum/csrf-cookie');
    }

    await determineIfUserIsAuthenticated(props.userAuthenticationUrl).then((response) => {
        userIsAuthenticated.value = response;
    });

    await getForm().then(() => {
        loading.value = false;
    });
});

const submitForm = async (data, node) => {
    node.clearErrors();

    // Add userGuard to the data object
    const requestData = {
        ...data,
        guard: userGuard.value,
    };

    fetch(formSubmissionUrl.value, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData),
    })
        .then((response) => response.json())
        .then((json) => {
            if (json.errors) {
                node.setErrors([], json.errors);

                return;
            }

            submittedSuccess.value = true;
        })
        .catch((error) => {
            const errorMessage = error.response?.data?.message || 'An error occurred. Please try again.';
            node.setErrors([errorMessage]);
        });
};

async function getForm() {
    await axios
        .get(props.url)
        .then((response) => {
            errorLoading.value = false;

            if (response.error) {
                throw new Error(response.error);
            }

            formPrimaryColor.value = response.data.primary_color;

            headerLogo.value = response.data.header_logo;

            appName.value = response.data.app_name;

            footerLogo.value = response.data.footer_logo;

            hasEnabledCsat.value = response.data.has_enabled_csat;

            hasEnabledNps.value = response.data.has_enabled_nps;

            authentication.value.requestUrl = response.data.authentication_url ?? null;

            formSubmissionUrl.value = response.data.submission_url;

            caseNumber.value = response.data.case_number;

            feedbackSubmitted.value = response.data.feedback_submitted;

            userGuard.value = response.data.guard;

            const { setPortalRequiresAuthentication } = useAuthStore();

            setPortalRequiresAuthentication((requiresAuthentication.value = response.data.requires_authentication));

            formRounding.value = {
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
            const errorMessage = error.response?.data?.message || 'An error occurred. Please try again.';
            node.setErrors([errorMessage]);
        });
}

async function authenticate(formData, node) {
    node.clearErrors();

    const { setToken } = useTokenStore();
    const { setUser } = useAuthStore();

    const { isEmbeddedInAdvisingApp } = getAppContext(props.accessUrl);

    if (isEmbeddedInAdvisingApp) {
        await axios.get(props.appUrl + '/sanctum/csrf-cookie');
    }

    if (authentication.value.isRequested) {
        axios
            .post(authentication.value.url, {
                code: formData.code,
            })
            .then((response) => {
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

                if (response.data.success === true) {
                    setToken(response.data.token);
                    setUser(response.data.user);
                    userGuard.value = response.data.guard;
                    userIsAuthenticated.value = true;
                }
            })
            .catch((error) => {
                const errorMessage = error.response?.data?.message || 'An error occurred. Please try again.';
                node.setErrors([errorMessage]);
            });

        return;
    }

    axios
        .post(authentication.value.requestUrl, {
            email: formData.email,
            isSpa: isEmbeddedInAdvisingApp,
        })
        .then((response) => {
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
            const errorMessage = error.response?.data?.message || 'An error occurred. Please try again.';
            node.setErrors([errorMessage]);
        });
}
</script>

<template>
    <div
        :style="{
            '--primary-50': formPrimaryColor[50],
            '--primary-100': formPrimaryColor[100],
            '--primary-200': formPrimaryColor[200],
            '--primary-300': formPrimaryColor[300],
            '--primary-400': formPrimaryColor[400],
            '--primary-500': formPrimaryColor[500],
            '--primary-600': formPrimaryColor[600],
            '--primary-700': formPrimaryColor[700],
            '--primary-800': formPrimaryColor[800],
            '--primary-900': formPrimaryColor[900],
            '--rounding-sm': formRounding.sm,
            '--rounding': formRounding.default,
            '--rounding-md': formRounding.md,
            '--rounding-lg': formRounding.lg,
            '--rounding-full': formRounding.full,
        }"
        class="font-sans px-6"
    >
        <div>
            <link rel="stylesheet" v-bind:href="hostUrl + '/js/widgets/case-feedback-form/style.css'" />
        </div>

        <div v-if="loading">
            <AppLoading />
        </div>

        <div v-else-if="!loading && !feedbackSubmitted">
            <div class="bg-gradient flex flex-col items-center justify-start min-h-screen">
                <div v-if="!submittedSuccess">
                    <div
                        v-if="requiresAuthentication && !userIsAuthenticated"
                        class="flex justify-center items-center min-h-screen px-4"
                    >
                        <div
                            class="w-full max-w-md sm:max-w-lg bg-white rounded-lg ring-1 ring-black/5 shadow-md px-6 sm:px-8 py-6 flex flex-col gap-4 sm:gap-6"
                        >
                            <h1 class="text-primary-950 text-center text-xl sm:text-2xl font-semibold">
                                Login to submit feedback
                            </h1>

                            <FormKit type="form" @submit="authenticate" v-model="authentication">
                                <FormKit
                                    type="email"
                                    label="Your email address"
                                    name="email"
                                    validation="required|email"
                                    validation-visibility="submit"
                                    :disabled="authentication.isRequested"
                                    class="text-sm sm:text-base"
                                />

                                <p v-if="authentication.requestedMessage" class="text-xs sm:text-sm text-gray-600">
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
                                    class="text-sm sm:text-base"
                                />
                            </FormKit>
                        </div>
                    </div>

                    <div v-else class="h-screen flex flex-col items-center justify-center px-4">
                        <div class="w-full max-w-md px-4 sm:px-8 pt-6 pb-4 flex flex-col items-start rounded-md">
                            <img :src="headerLogo" :alt="appName" class="max-h-20 max-w-64 object-scale-down mb-4" />

                            <div v-if="errorLoading" class="text-center sm:text-left">
                                <h1 class="text-2xl sm:text-3xl font-bold text-red-500">
                                    Error Loading the feedback form
                                </h1>
                                <p class="text-base sm:text-lg text-red-500">Please try again later</p>
                            </div>

                            <div v-else class="w-full text-left">
                                <div class="mb-4 text-sm sm:text-base">
                                    Thank you for filling out this brief survey on your case:
                                    <span class="whitespace-normal sm:whitespace-nowrap">{{ caseNumber }}</span>
                                </div>

                                <FormKit type="form" @submit="submitForm">
                                    <FormKit
                                        validation="required"
                                        type="rating"
                                        v-if="hasEnabledCsat"
                                        name="csat"
                                        label="How did we do?"
                                    />
                                    <FormKit
                                        validation="required"
                                        type="rating"
                                        v-if="hasEnabledNps"
                                        name="nps"
                                        label="How likely are you to recommend our service to a friend or colleague?"
                                        label-class="whitespace-normal sm:whitespace-nowrap"
                                    />
                                </FormKit>
                            </div>
                        </div>

                        <Footer :logo="footerLogo" class="w-full mt-4 sm:mt-8" />
                    </div>
                </div>
                <div v-if="submittedSuccess" class="flex items-center justify-center min-h-screen px-4">
                    <h1 class="text-lg sm:text-xl text-center text-gray-900">
                        Thank you, your feedback has been received.
                    </h1>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="bg-gradient flex flex-col items-center justify-center min-h-screen px-4">
                <h1 class="text-lg sm:text-xl text-center text-primary-950">
                    Feedback is already submitted for this case.
                </h1>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-gradient {
    @apply relative bg-no-repeat;
    background-image: radial-gradient(circle at top, theme('colors.primary.200'), theme('colors.white') 50%);
}
</style>
