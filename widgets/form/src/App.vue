<script setup>
import { ref } from "vue";

const submittedSuccess = ref(false);

const scriptUrl = new URL(document.currentScript.getAttribute("src"));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const scriptQuery = Object.fromEntries(scriptUrl.searchParams);

const hostUrl = `${protocol}//${scriptHostname}`;

const display = ref(false);
const formName = ref("");
const formDescription = ref("");
const schema = ref([]);

fetch(`${hostUrl}/api/forms/${scriptQuery.form}`)
    .then((response) => response.json())
    .then((json) => {
        if (json.error) {
            throw new Error(json.error);
        }

        formName.value = json.name;
        formDescription.value = json.description;
        schema.value = json.schema;
        display.value = true;
    })
    .catch((error) => {
        console.error(`ASSIST Embed Form ${error}`);
    });

const submit = async (data, node) => {
    node.clearErrors();

    fetch(`${hostUrl}/api/forms/${scriptQuery.form}/submit`, {
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
        });
};
</script>

<template>
    <div class="w-auto max-w-md font-sans">
        <div v-if="display && !submittedSuccess">
            <link
                rel="stylesheet"
                v-bind:href="hostUrl + '/js/widgets/form/style.css'"
            />

            <h1 class="text-2xl font-bold mb-2 text-center">
                {{ formName }}
            </h1>

            <p class="text-base mb-2">{{ formDescription }}</p>

            <FormKit type="form" @submit="submit">
                <FormKitSchema :schema="schema" />
            </FormKit>
        </div>

        <div v-if="submittedSuccess">
            <h1 class="text-2xl font-bold mb-2 text-center">
                Thank you, your submission has been received.
            </h1>
        </div>
    </div>
</template>
