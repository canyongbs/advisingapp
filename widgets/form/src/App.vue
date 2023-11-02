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
        display.value = true;
    })
    .catch((error) => {
        console.error(`ASSIST Embed Form ${error}`);
    });
</script>

<template>
    <div class="font-sans">
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
