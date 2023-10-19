<script setup>
import { ref } from "vue";

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

const submit = async (fields) => {
    await new Promise((r) => setTimeout(r, 1000));
    alert(JSON.stringify(fields));
};
</script>

<template>
    <div v-if="display" class="w-auto max-w-md font-sans">
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
</template>