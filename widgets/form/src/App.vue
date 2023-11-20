<!--
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
import { defineProps, ref, reactive } from 'vue';
import useSteps from './useSteps.js';

let { steps, visitedSteps, activeStep, setStep, stepPlugin } = useSteps();

const props = defineProps(['url']);

const data = reactive({
    steps,
    visitedSteps,
    activeStep,
    plugins: [
        stepPlugin
    ],
    setStep: target => () => {
        setStep(target)
    },
    setActiveStep: stepName => () => {
        data.activeStep = stepName
    },
    showStepErrors: stepName => {
        return (steps[stepName].errorCount > 0 || steps[stepName].blockingCount > 0) && (visitedSteps.value && visitedSteps.value.includes(stepName))
    },
    stepIsValid: stepName => {
        return steps[stepName].valid && steps[stepName].errorCount === 0
    },
    stringify: (value) => JSON.stringify(value, null, 2),
    submitForm: async (data, node) => {
        node.clearErrors();

        fetch(formSubmissionUrl.value, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
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
            })
    },
})

const submittedSuccess = ref(false);

const scriptUrl = new URL(document.currentScript.getAttribute("src"));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const scriptQuery = Object.fromEntries(scriptUrl.searchParams);

const hostUrl = `${protocol}//${scriptHostname}`;

const display = ref(false);
const formName = ref("");
const formDescription = ref("");
const formSubmissionUrl = ref("");
const formPrimaryColor = ref("");
const formRounding= ref("");
const schema = ref([]);

fetch(props.url)
    .then((response) => response.json())
    .then((json) => {
        if (json.error) {
            throw new Error(json.error);
        }

        formName.value = json.name;
        formDescription.value = json.description;
        schema.value = json.schema;
        formSubmissionUrl.value = json.submission_url;
        formPrimaryColor.value = json.primary_color;

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
        }[json.rounding ?? 'md']

        display.value = true;
    })
    .catch((error) => {
        console.error(`ASSIST Embed Form ${error}`);
    });
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
        <div v-if="display && !submittedSuccess">
            <link
                rel="stylesheet"
                v-bind:href="hostUrl + '/js/widgets/form/style.css'"
            />

            <h1 class="text-2xl font-bold mb-2 text-center">
                {{ formName }}
            </h1>

            <p class="text-base mb-6">
                {{ formDescription }}
            </p>

            <FormKitSchema :schema="schema" :data="data" />
        </div>

        <div v-if="submittedSuccess">
            <h1 class="text-2xl font-bold mb-2 text-center">
                Thank you, your submission has been received.
            </h1>
        </div>
    </div>
</template>
