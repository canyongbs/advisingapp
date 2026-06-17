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
    import { ArrowLeftIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';
    import DOMPurify from 'dompurify';
    import { marked } from 'marked';
    import { defineProps, onMounted, reactive, ref } from 'vue';
    import attachRecaptchaScript from '../../../app-modules/integration-google-recaptcha/resources/js/Services/AttachRecaptchaScript.js';
    import getRecaptchaToken from '../../../app-modules/integration-google-recaptcha/resources/js/Services/GetRecaptchaToken.js';
    import asteriskPlugin from './FormKit/asterisk.js';
    import wizard from './FormKit/wizard';

    onMounted(async () => {
        await getForm().then(function () {
            if (formRecaptchaEnabled.value === true) {
                attachRecaptchaScript(formRecaptchaKey.value);
            }
        });
    });

    let { steps, visitedSteps, activeStep, setStep, wizardPlugin } = wizard();

    const props = defineProps({
        entryUrl: {
            type: String,
            required: true,
        },
        preview: {
            type: Boolean,
            default: false,
        },
    });

    const data = reactive({
        steps,
        visitedSteps,
        activeStep,
        plugins: [wizardPlugin, asteriskPlugin],
        setStep: (target) => () => {
            setStep(target);
        },
        setActiveStep: (stepName) => () => {
            data.activeStep = stepName;
        },
        showStepErrors: (stepName) => {
            return (
                (steps[stepName].errorCount > 0 || steps[stepName].blockingCount > 0) &&
                visitedSteps.value &&
                visitedSteps.value.includes(stepName)
            );
        },
        stepIsValid: (stepName) => {
            return steps[stepName].valid && steps[stepName].errorCount === 0;
        },
        stringify: (value) => JSON.stringify(value, null, 2),
        submitForm: async (data, node) => {
            node.clearErrors();

            if (props.preview === 'true' || props.preview === true) {
                submittedSuccess.value = true;
                return;
            }

            let recaptchaToken = null;

            if (formRecaptchaEnabled.value === true) {
                recaptchaToken = await getRecaptchaToken(formRecaptchaKey.value);
            }

            const submissionData = { ...data };

            if (recaptchaToken !== null) {
                submissionData['recaptcha-token'] = recaptchaToken;
            }

            fetch(formSubmissionUrl.value, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(submissionData),
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
                    node.setErrors([error]);
                });
        },
    });

    const submittedSuccess = ref(false);

    const display = ref(false);
    const formName = ref('');
    const formIsAuthenticated = ref(false);
    const formDescription = ref('');
    const formSubmissionUrl = ref('');
    const formPrimaryColor = ref('');
    const formRounding = ref('');
    const formRecaptchaEnabled = ref(false);
    const formRecaptchaKey = ref(null);
    const onScreenResponse = ref(null);
    const schema = ref([]);
    const formTitleFontWeight = ref('');
    const formTitleColor = ref({});

    const allowViewPastSubmissions = ref(false);
    const pastSubmissionsCount = ref(0);
    const pastSubmissionsUrl = ref(null);

    const currentView = ref('form');
    const pastSubmissions = ref([]);
    const pastSubmissionsMeta = ref(null);
    const pastSubmissionsCurrentPage = ref(1);
    const isLoadingSubmissions = ref(false);
    const isLoadingSubmission = ref(false);
    const currentSubmission = ref(null);

    const authentication = ref({
        code: null,
        email: null,
        isRequested: false,
        requestedMessage: null,
        requestUrl: null,
        url: null,
        registrationAllowed: false,
    });

    function formatSubmissionDateTime(isoString) {
        if (!isoString) return '';
        return new Date(isoString).toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });
    }

    async function loadPastSubmissions(page = 1) {
        isLoadingSubmissions.value = true;
        try {
            const url = new URL(pastSubmissionsUrl.value);
            url.searchParams.set('page', page);
            url.searchParams.set('per_page', 10);
            const response = await fetch(url.toString(), { headers: { Accept: 'application/json' } });
            const json = await response.json();
            pastSubmissions.value = json.past_submissions ?? [];
            pastSubmissionsMeta.value = json.meta ?? null;
            pastSubmissionsCurrentPage.value = page;
            currentView.value = 'table';
        } catch (e) {
            console.error('Error loading past submissions', e);
        } finally {
            isLoadingSubmissions.value = false;
        }
    }

    async function loadSubmission(viewUrl) {
        isLoadingSubmission.value = true;
        try {
            const response = await fetch(viewUrl, { headers: { Accept: 'application/json' } });
            const json = await response.json();
            currentSubmission.value = json;
            currentView.value = 'submission';
        } catch (e) {
            console.error('Error loading submission', e);
        } finally {
            isLoadingSubmission.value = false;
        }
    }

    function startNewSubmission() {
        currentView.value = 'form';
    }

    function backToTable() {
        currentView.value = 'table';
    }

    function backToSplash() {
        currentView.value = 'splash';
    }

    async function getForm() {
        await fetch(props.entryUrl)
            .then((response) => response.json())
            .then((json) => {
                if (json.error) {
                    throw new Error(json.error);
                }

                formName.value = json.name;
                formDescription.value = json.description;
                schema.value = json.schema;
                formIsAuthenticated.value = json.is_authenticated ?? false;
                formSubmissionUrl.value = json.submission_url ?? null;
                formPrimaryColor.value = json.primary_color;
                formTitleFontWeight.value = json.title_font_weight;
                formTitleColor.value = json.title_color;
                authentication.value.requestUrl = json.authentication_url ?? null;

                formRecaptchaEnabled.value = json.recaptcha_enabled ?? false;
                formRecaptchaKey.value = json.recaptcha_site_key ?? null;

                if (props.preview === 'true' || props.preview === true) {
                    visitedSteps.value = [];
                    activeStep.value = '';

                    Object.keys(steps).forEach((stepName) => {
                        if (steps[stepName]) {
                            steps[stepName].errorCount = 0;
                            steps[stepName].blockingCount = 0;
                            steps[stepName].valid = true;
                        }
                    });
                }

                if (props.preview === 'true' || props.preview === true) {
                    if (formIsAuthenticated.value) {
                        // Simulate the authenticated sign-in step with disabled, pre-filled values.
                        authentication.value.isRequested = true;
                        authentication.value.email = 'noreply@canyongbs.com';
                        authentication.value.code = '111111';
                    } else if (!json.authentication_url) {
                        formSubmissionUrl.value = 'preview-mode';
                    }
                }

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
                }[json.rounding ?? 'md'];

                onScreenResponse.value = json.on_screen_response ?? null;

                display.value = true;
            })
            .catch((error) => {
                console.error(`Advising App Embed Form ${error}`);
            });
    }

    async function authenticate(formData, node) {
        node.clearErrors();

        if (props.preview === 'true' || props.preview === true) {
            formSubmissionUrl.value = 'preview-mode';
            return;
        }

        if (authentication.value.isRequested) {
            fetch(authentication.value.url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    code: formData.code,
                }),
            })
                .then((response) => response.json())
                .then((json) => {
                    if (json.errors) {
                        node.setErrors([], json.errors);

                        return;
                    }

                    if (json.is_expired) {
                        node.setErrors(['The authentication code expires after 24 hours. Please authenticate again.']);

                        authentication.value.isRequested = false;
                        authentication.value.requestedMessage = null;
                        authentication.value.registrationAllowed = false;

                        return;
                    }

                    if (!json.submission_url) {
                        node.setErrors([json.message]);

                        return;
                    }

                    formSubmissionUrl.value = json.submission_url;

                    if (json.schema) {
                        schema.value = json.schema;
                    }

                    allowViewPastSubmissions.value = json.allow_view_past_submissions ?? false;
                    pastSubmissionsCount.value = json.past_submissions_count ?? 0;
                    pastSubmissionsUrl.value = json.past_submissions_url ?? null;

                    if (allowViewPastSubmissions.value && pastSubmissionsCount.value > 0) {
                        currentView.value = 'splash';
                    }
                })
                .catch((error) => {
                    node.setErrors([error]);
                });

            return;
        }

        if (authentication.value.registrationAllowed) {
            fetch(authentication.value.url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: formData.email,
                    first_name: formData.first_name,
                    last_name: formData.last_name,
                    preferred: formData.preferred,
                    mobile: formData.mobile,
                    birthdate: formData.birthdate,
                    address: formData.address,
                    address_2: formData.address_2,
                    city: formData.city,
                    state: formData.state,
                    postal: formData.postal,
                }),
            })
                .then((response) => response.json())
                .then((json) => {
                    if (json.errors) {
                        node.setErrors([], json.errors);

                        return;
                    }

                    authentication.value.isRequested = true;
                    authentication.value.requestedMessage = json.message;
                    authentication.value.url = json.authentication_url;
                })
                .catch((error) => {
                    node.setErrors([error]);
                });

            return;
        }

        fetch(authentication.value.requestUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: formData.email,
            }),
        })
            .then((response) => response.json())
            .then((json) => {
                if (json.errors) {
                    node.setErrors([], json.errors);

                    return;
                }

                if (!json.authentication_url) {
                    node.setErrors([json.message]);

                    return;
                }
                if (json.registrationAllowed) {
                    authentication.value.registrationAllowed = true;
                    authentication.value.isRequested = false;
                    authentication.value.requestedMessage = json.message;
                    authentication.value.url = json.authentication_url;

                    return;
                }

                authentication.value.isRequested = true;
                authentication.value.requestedMessage = json.message;
                authentication.value.url = json.authentication_url;
            })
            .catch((error) => {
                node.setErrors([error]);
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
        class="font-sans"
    >
        <div class="prose max-w-none" v-if="display && !submittedSuccess">
            <div
                v-if="props.preview === 'true' || props.preview === true"
                style="
                    background: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 0.375rem;
                    padding: 1rem;
                    margin-bottom: 1.5rem;
                "
                class="preview-banner"
            >
                <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin: 0">
                    Preview Mode - This is only a preview of your form. Nothing will be saved.
                </p>
            </div>

            <h1
                v-if="formName"
                :style="{
                    fontWeight: formTitleFontWeight,
                    color: `rgb(${formTitleColor[900]})`,
                }"
            >
                {{ formName }}
            </h1>

            <p v-if="formDescription">
                {{ formDescription }}
            </p>

            <div v-if="!formSubmissionUrl">
                <FormKit type="form" @submit="authenticate" v-model="authentication">
                    <FormKit
                        type="email"
                        label="Your email address"
                        name="email"
                        validation="required|email"
                        validation-visibility="submit"
                        :disabled="authentication.isRequested"
                    />

                    <div v-if="authentication.registrationAllowed">
                        <p class="text-gray-700 font-medium text-xs my-3">
                            You are not registered yet. Please fill in the form below to register.
                        </p>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="text"
                                    label="First Name"
                                    name="first_name"
                                    validation="required|alpha|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="text"
                                    label="Last Name"
                                    name="last_name"
                                    validation="required|alpha|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="text"
                                    label="Preferred Name"
                                    name="preferred"
                                    validation="required|alpha|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="date"
                                    label="Birth Date"
                                    name="birthdate"
                                    validation="required"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="tel"
                                    label="Mobile"
                                    name="mobile"
                                    placeholder="xxx-xxx-xxxx"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="text"
                                    label="Address"
                                    name="address"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="text"
                                    label="Apartment/Unit Number"
                                    name="address_2"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="text"
                                    label="City"
                                    name="city"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="text"
                                    label="State"
                                    name="state"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="text"
                                    label="Postal"
                                    name="postal"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                    </div>

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
                        :disabled="props.preview === 'true' || props.preview === true"
                    />
                </FormKit>
            </div>

            <div v-if="formSubmissionUrl" class="space-y-6">
                <!-- Splash screen: View Past Submissions or New Submission -->
                <div v-if="currentView === 'splash'" class="py-4 not-prose">
                    <p v-if="authentication.email" class="text-sm text-gray-500 mb-6">
                        Signed in as <strong class="font-semibold text-gray-800">{{ authentication.email }}</strong>
                    </p>
                    <div class="flex flex-col items-center gap-5">
                        <h2 class="text-base font-semibold text-gray-800">What would you like to do?</h2>
                        <div class="flex flex-col sm:flex-row gap-3 w-full max-w-sm">
                            <button
                                @click="loadPastSubmissions(1)"
                                :disabled="isLoadingSubmissions"
                                class="flex-1 h-11 px-5 rounded border-2 border-primary-600 text-primary-600 text-sm font-semibold whitespace-nowrap hover:bg-primary-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="isLoadingSubmissions">Loading…</span>
                                <span v-else>View Past Submissions</span>
                            </button>
                            <button
                                @click="startNewSubmission"
                                class="flex-1 h-11 px-5 rounded bg-primary-600 text-white text-sm font-semibold whitespace-nowrap hover:bg-primary-700 transition-colors"
                            >
                                New Submission
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Past submissions table -->
                <div v-else-if="currentView === 'table'" class="space-y-4 not-prose">
                    <div class="flex items-center justify-between">
                        <button
                            @click="backToSplash"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-800 cursor-pointer transition-colors"
                        >
                            <ArrowLeftIcon class="w-4 h-4" />
                            Back
                        </button>
                        <p class="text-sm text-gray-500">
                            Signed in as <strong class="font-semibold text-gray-700">{{ authentication.email }}</strong>
                        </p>
                    </div>
                    <h2 class="text-base font-semibold text-gray-800">Past Submissions</h2>
                    <div v-if="isLoadingSubmissions" class="py-8 text-center text-sm text-gray-500">
                        Loading submissions…
                    </div>
                    <div v-else-if="pastSubmissions.length === 0" class="py-8 text-center text-sm text-gray-500">
                        No past submissions found.
                    </div>
                    <div v-else class="overflow-hidden rounded border border-gray-200 not-prose">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200 text-left">
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                        Date Submitted
                                    </th>
                                    <th class="px-4 py-3 w-24"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="submission in pastSubmissions"
                                    :key="submission.id"
                                    @click="!isLoadingSubmission && loadSubmission(submission.view_url)"
                                    :class="[
                                        'transition-colors',
                                        isLoadingSubmission
                                            ? 'opacity-60 cursor-not-allowed'
                                            : 'cursor-pointer hover:bg-primary-50',
                                    ]"
                                >
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ formatSubmissionDateTime(submission.submitted_at) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <ChevronRightIcon class="w-4 h-4 text-gray-400 ml-auto" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div
                            v-if="pastSubmissionsMeta && pastSubmissionsMeta.last_page > 1"
                            class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-gray-50"
                        >
                            <button
                                @click="loadPastSubmissions(pastSubmissionsCurrentPage - 1)"
                                :disabled="pastSubmissionsCurrentPage <= 1 || isLoadingSubmissions"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded border border-gray-300 text-gray-600 disabled:opacity-40 hover:bg-white transition-colors"
                            >
                                <ChevronLeftIcon class="w-3 h-3" />
                                Previous
                            </button>
                            <span class="text-xs text-gray-500">
                                Page {{ pastSubmissionsCurrentPage }} of {{ pastSubmissionsMeta.last_page }}
                            </span>
                            <button
                                @click="loadPastSubmissions(pastSubmissionsCurrentPage + 1)"
                                :disabled="
                                    pastSubmissionsCurrentPage >= pastSubmissionsMeta.last_page || isLoadingSubmissions
                                "
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded border border-gray-300 text-gray-600 disabled:opacity-40 hover:bg-white transition-colors"
                            >
                                Next
                                <ChevronRightIcon class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Read-only submission detail -->
                <div v-else-if="currentView === 'submission'" class="space-y-4 not-prose">
                    <button
                        @click="backToTable"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-800 cursor-pointer transition-colors"
                    >
                        <ArrowLeftIcon class="w-4 h-4" />
                        Back to Submissions
                    </button>
                    <div v-if="isLoadingSubmission" class="py-8 text-center text-sm text-gray-500">Loading…</div>
                    <div v-else-if="currentSubmission" class="space-y-4">
                        <div class="rounded bg-primary-50 border border-primary-200 px-4 py-3 text-sm text-primary-900">
                            This form was submitted by <strong>{{ authentication.email }}</strong> on
                            {{ formatSubmissionDateTime(currentSubmission.submitted_at) }}.
                        </div>

                        <FormKitSchema
                            v-if="currentSubmission.schema"
                            :schema="currentSubmission.schema"
                            :data="data"
                        />
                    </div>
                </div>

                <!-- Normal new submission form -->
                <div v-else class="space-y-6">
                    <p v-if="formIsAuthenticated && authentication.email" class="text-sm">
                        Signed in as <strong>{{ authentication.email }}</strong>
                    </p>

                    <FormKitSchema :schema="schema" :data="data" />
                </div>
            </div>
        </div>

        <div v-if="submittedSuccess" class="flex justify-center items-center w-full">
            <div v-if="onScreenResponse" v-html="DOMPurify.sanitize(marked.parse(onScreenResponse))"></div>
            <h2
                v-else-if="props.preview === 'true' || props.preview === true"
                class="text-xl font-bold mb-2 text-center"
                style="text-align: center; margin: 0 auto; display: block; width: 100%"
            >
                This was only a preview. Your data has not been submitted.
            </h2>
            <h1 v-else class="text-2xl font-bold mb-2 text-center">Thank you, your submission has been received.</h1>
        </div>
    </div>
</template>
