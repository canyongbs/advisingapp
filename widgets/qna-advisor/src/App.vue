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
import Echo from 'laravel-echo';
import Pusher from 'pusher-js/dist/web/pusher';
import { defineProps, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps(['url']);
const sendMessageUrl = ref(null);
const chatId = ref(null);
const message = ref('');
const messages = ref([]);
const currentResponse = ref('');
const isLoading = ref(false);
const nextRequestOptions = ref(null);
let privateChannel = null;

const scriptUrl = new URL(document.currentScript.getAttribute('src'));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const hostUrl = `${protocol}//${scriptHostname}`;

onMounted(async () => {
    try {
        const response = await fetch(props.url);
        const json = await response.json();
        if (json.error) throw new Error(json.error);
        sendMessageUrl.value = json.send_message_url;
        chatId.value = json.chat_id;

        setupWebsockets(json.websockets_config);
    } catch (error) {
        console.error(`Advising App Embed QnA Advisor ${error}`);
    }
});

onUnmounted(() => {
    if (privateChannel) {
        privateChannel.stopListening('advisor-message.chunk');
        privateChannel.stopListening('advisor-message.next-request-options');
    }
    if (window.Echo) {
        window.Echo.disconnect();
    }
});

function setupWebsockets(config) {
    try {
        window.Pusher = Pusher;
        window.Echo = new Echo(config);

        console.log(chatId.value)
        if (chatId.value) {
            privateChannel = window.Echo.private(`qna-advisor-chat-${chatId.value}`)
                .listen('.advisor-message.chunk', (data) => {
                    console.log(data)
                    if (data.error) {
                        console.error('Advisor message error:', data.error);
                        isLoading.value = false;
                        return;
                    }

                    if (data.content) {
                        currentResponse.value += data.content;
                    }

                    if (data.is_complete) {
                        messages.value.push({
                            from: 'agent',
                            content: currentResponse.value,
                        });
                        currentResponse.value = '';
                        isLoading.value = false;
                    }
                })
                .listen('.advisor-message.next-request-options', (data) => {
                    if (data.options) {
                        nextRequestOptions.value = data.options;
                    }
                });
        }
    } catch (error) {
        console.error('Failed to setup websockets:', error);
    }
}

async function sendMessage() {
    if (!sendMessageUrl.value || !message.value.trim()) return;

    messages.value.push({
        from: 'user',
        content: message.value,
    });

    isLoading.value = true;
    currentResponse.value = '';

    try {
        const requestBody = {
            content: message.value,
        };

        if (nextRequestOptions.value) {
            requestBody.options = nextRequestOptions.value;
        }

        const sendMessageResponse = await fetch(sendMessageUrl.value, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestBody),
        });

        const data = await sendMessageResponse.json();

        message.value = '';
    } catch (error) {
        console.error('Send message error:', error);
        isLoading.value = false;
    }
}
</script>

<template>
    <div v-show="sendMessageUrl !== null" class="flex h-[calc(100dvh-6rem)] flex-col gap-y-3 w-11/12 mx-auto bg-white">
        <link rel="stylesheet" v-bind:href="hostUrl + '/js/widgets/qna-advisor/style.css'" />
        <div  class="flex flex-1 flex-col justify-end overflow-y-scroll rounded-xl border border-gray-950/5 text-sm shadow-sm dark:border-white/10 dark:bg-gray-800">
            <!-- <div v-if="messages.length > 0" class="divide-y dark:divide-gray-800">
                <div v-for="(message, index) in messages" :key="index" class="m-auto justify-center px-4 py-4 text-base md:gap-6 md:py-6">
                    <div v-if="message.from === 'user'"><strong>User:</strong> {{ message.content }}</div>
                    <div v-else><strong>Agent:</strong> {{ message.content }}</div>
                </div>
            </div> -->
            <div v-if="messages.length > 0" >
            <div class="mx-auto flex gap-4 text-base w-full items-start bg-gray-50 border-t border-gray-200 px-10 py-3" v-for="(message, index) in messages" :key="index">
                <div class="relative flex flex-shrink-0 flex-col items-end">
                    <img class="h-8 w-8 object-cover object-center" style="border-radius:40px;" :src="hostUrl + '/images/canyon-ai-headshot.jpg'" alt="" title="" />
                </div>
                <div class="relative flex w-full flex-col gap-1 md:gap-3">
                    {{ message.content }}
                </div>
            </div>
            </div>


            <div v-if="isLoading && currentResponse" class="m-auto justify-center px-4 py-4 text-base md:gap-6 md:py-6"><strong>Agent:</strong> {{ currentResponse }}</div>

            <div v-if="isLoading" class="justify-center px-4 py-4 text-base md:gap-6 md:py-6">
                <p>AI is typing...</p>
            </div>
        </div>

        <div
            class="w-full overflow-hidden rounded-xl border border-gray-950/5 bg-gray-50 shadow-sm dark:border-white/10 dark:bg-gray-700">
            <div class="bg-white dark:bg-gray-800">
                <label
                    class="sr-only text-red-600"
                    for="message_input"
                >Type here</label>
                <textarea v-model="message" placeholder="Ask your question..." :disabled="isLoading"
                 class="min-h-20 w-full resize-none border-0 bg-white p-4 text-sm text-gray-900 focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"></textarea>
            </div>
            <div
                class="flex flex-col items-center border-t px-3 py-2 dark:border-gray-600 sm:flex-row sm:justify-between">
                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                   <button @click="sendMessage" :disabled="isLoading || !message.trim()" style="border-radius:12px;" class="relative font-semibold outline-none focus-visible:ring-2 px-3 py-2 text-sm bg-gray-600 text-white hover:bg-gray-500 focus-visible:ring-gray-500/50 dark:bg-gray-500 dark:hover:bg-gray-400 dark:focus-visible:ring-gray-400/50 w-full sm:w-auto">Send</button>
                </div>
            </div>
        </div>
    </div>
</template>
