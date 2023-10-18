<script setup>
import { ref } from "vue";

const scriptUrl = new URL(document.currentScript.getAttribute("src"));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const scriptQuery = Object.fromEntries(scriptUrl.searchParams);

const hostUrl = `${protocol}//${scriptHostname}`;

const schema = ref([]);
let display = ref(false);

fetch(`${hostUrl}/api/forms/${scriptQuery.form}`)
    .then((response) => response.json())
    .then((json) => {
        schema.value = json;
        display.value = true;
    });

const submit = async (fields) => {
    await new Promise((r) => setTimeout(r, 1000));
    alert(JSON.stringify(fields));
};
</script>

<template>
    <link
        rel="stylesheet"
        v-bind:href="hostUrl + '/js/widgets/form/style.css'"
    />

    <FormKit v-if="display" type="form" @submit="submit">
        <FormKitSchema :schema="schema" />
    </FormKit>
</template>