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
                    <div v-else></div>
                </div>
            </div>
        </div>
    </div>
</template>
