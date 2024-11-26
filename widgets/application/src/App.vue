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
import { defineProps, reactive, ref } from 'vue';
import asteriskPlugin from '../../form/src/FormKit/asterisk.js';
import wizard from '../../form/src/FormKit/wizard';
let { steps, visitedSteps, activeStep, setStep, wizardPlugin } = wizard();

const props = defineProps(['url']);

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

        fetch(applicationSubmissionUrl.value, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
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

const scriptUrl = new URL(document.currentScript.getAttribute('src'));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const scriptQuery = Object.fromEntries(scriptUrl.searchParams);

const hostUrl = `${protocol}//${scriptHostname}`;

const display = ref(false);
const applicationName = ref('');
const applicationDescription = ref('');
const applicationSubmissionUrl = ref('');
const applicationPrimaryColor = ref('');
const applicationRounding = ref('');
const schema = ref([]);

const authentication = ref({
    code: null,
    email: null,
    isRequested: false,
    requestedMessage: null,
    requestUrl: null,
    url: null,
});

fetch(props.url)
    .then((response) => response.json())
    .then((json) => {
        if (json.error) {
            throw new Error(json.error);
        }

        applicationName.value = json.name;
        applicationDescription.value = json.description;
        schema.value = json.schema;
        applicationPrimaryColor.value = json.primary_color;
        authentication.value.requestUrl = json.authentication_url;

        applicationRounding.value = {
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

        display.value = true;
    })
    .catch((error) => {
        console.error(`Advising App Embed Application ${error}`);
    });

async function authenticate(applicationData, node) {
    node.clearErrors();

    if (authentication.value.isRequested) {
        fetch(authentication.value.url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                code: applicationData.code,
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

                    return;
                }

                if (!json.submission_url) {
                    node.setErrors([json.message]);

                    return;
                }

                applicationSubmissionUrl.value = json.submission_url;
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
            email: applicationData.email,
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
            '--primary-50': applicationPrimaryColor[50],
            '--primary-100': applicationPrimaryColor[100],
            '--primary-200': applicationPrimaryColor[200],
            '--primary-300': applicationPrimaryColor[300],
            '--primary-400': applicationPrimaryColor[400],
            '--primary-500': applicationPrimaryColor[500],
            '--primary-600': applicationPrimaryColor[600],
            '--primary-700': applicationPrimaryColor[700],
            '--primary-800': applicationPrimaryColor[800],
            '--primary-900': applicationPrimaryColor[900],
            '--rounding-sm': applicationRounding.sm,
            '--rounding': applicationRounding.default,
            '--rounding-md': applicationRounding.md,
            '--rounding-lg': applicationRounding.lg,
            '--rounding-full': applicationRounding.full,
        }"
        class="font-sans"
    >
        <div class="prose max-w-none" v-if="display && !submittedSuccess">
            <link rel="stylesheet" v-bind:href="hostUrl + '/js/widgets/application/style.css'" />

            <h1>
                {{ applicationName }}
            </h1>

            <p>
                {{ applicationDescription }}
            </p>

            <div v-if="!applicationSubmissionUrl">
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

            <div v-if="applicationSubmissionUrl" class="space-y-6">
                <p class="text-sm">
                    Signed in as <strong>{{ authentication.email }}</strong>
                </p>

                <FormKitSchema :schema="schema" :data="data" />
            </div>
        </div>

        <div v-if="submittedSuccess">
            <h1 class="text-2xl font-bold mb-2 text-center">Thank you, your application has been received.</h1>
        </div>
    </div>
</template>
