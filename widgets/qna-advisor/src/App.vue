<!--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
      committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
import { defineProps, onMounted, ref } from 'vue';

onMounted(async () => {
    await getData();
});

const props = defineProps(['url']);

const scriptUrl = new URL(document.currentScript.getAttribute('src'));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;

const hostUrl = `${protocol}//${scriptHostname}`;

const qnaAdvisor = ref([]);
const errorMessage = ref(null);

async function getData() {
    errorMessage.value = null;

    await fetch(props.url)
        .then(async (response) => {
            if (!response.ok) {
                if (response.status === 403) {
                    throw new Error(await response.json());
                }

                throw new Error('An error occurred while fetching the data.');
            }

            return response.json();
        })
        .then((json) => {
            qnaAdvisor.value = json.data || [];
        })
        .catch((error) => {
            errorMessage.value = error;
        });
}
</script>

<template>
    <div class="font-sans w-full sm:px-8 bg-white text-black pt-4">
        <link rel="stylesheet" v-bind:href="hostUrl + '/js/widgets/qna-advisor/style.css'" />
        <div class="mx-auto w-full max-w-7xl lg:px-8">
            <div class="relative px-4 sm:px-8 lg:px-12">
                <div class="mx-auto max-w-2xl lg:max-w-5xl grid grid-cols-1 divide-y gap-8">
                    <h1 v-if="errorMessage" class="text-2xl font-bold text-red-500 mx-auto">
                        {{ errorMessage }}
                    </h1>
                    <div v-else>Passed</div>
                </div>
            </div>
        </div>
    </div>
</template>
