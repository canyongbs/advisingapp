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
import headshotAgent from '../../../resources/images/canyon-ai-headshot.jpg?url';
import loadingSpinner from '../public/images/loading-spinner.svg?url';
import userAvatar from '../public/images/user-default-avatar.svg?url';

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

        if (chatId.value) {
            privateChannel = window.Echo.private(`qna-advisor-chat-${chatId.value}`)
                .listen('.advisor-message.chunk', (data) => {
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
    <div class="h-full bg-gray-50 dark:bg-gray-950">
        <div v-show="sendMessageUrl !== null" class="flex flex-col gap-y-3 w-11/12 mx-auto">
            <link rel="stylesheet" v-bind:href="hostUrl + '/js/widgets/qna-advisor/style.css'" />
            <div class="flex h-[calc(100dvh-16rem)] flex-col gap-y-3">
                <div
                    class="flex flex-1 flex-col-reverse overflow-y-auto rounded-xl border border-gray-950/5 text-sm shadow-sm dark:border-white/10 dark:bg-gray-800"
                >
                    <div class="divide-y dark:divide-gray-800" v-if="messages.length > 0">
                        <div
                            class="mx-auto flex gap-4 text-base w-full items-start bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-10 py-3"
                            v-for="(message, index) in messages"
                            :key="index"
                        >
                            <div class="relative flex flex-shrink-0 flex-col items-end">
                                <img
                                    class="h-8 w-8 object-cover object-center"
                                    :class="{ 'dark:invert': message.from !== 'agent' }"
                                    style="border-radius: 40px"
                                    :src="message.from === 'agent' ? headshotAgent : userAvatar"
                                    alt="Canyon AI"
                                    title="Canyon AI"
                                />
                            </div>
                            <div class="relative flex w-full flex-col gap-1 md:gap-3 tex-gray-900 dark:text-white">
                                {{ message.content }}
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="w-full overflow-hidden rounded-xl border border-gray-950/5 bg-gray-50 shadow-sm dark:border-white/10 dark:bg-gray-700"
                >
                    <div
                        v-if="isLoading"
                        class="justify-center px-4 py-4 text-base md:gap-6 md:py-6 tex-gray-900 dark:text-white"
                    >
                        <p>AI is typing...</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800">
                        <label class="sr-only" for="message_input">Type here</label>
                        <textarea
                            v-model="message"
                            placeholder="Ask your question..."
                            :disabled="isLoading"
                            class="min-h-20 w-full resize-none border-0 bg-white p-4 text-sm text-gray-900 focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                            style="height: min(80px, 25dvh)"
                        ></textarea>
                    </div>
                    <div
                        class="flex flex-col items-center border-t px-3 py-2 dark:border-gray-600 sm:flex-row sm:justify-between"
                    >
                        <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                            <button
                                @click="sendMessage"
                                :disabled="isLoading || !message.trim()"
                                style="border-radius: 12px"
                                class="relative font-semibold outline-none focus-visible:ring-2 px-3 py-2 text-sm bg-gray-600 text-white hover:bg-gray-500 focus-visible:ring-gray-500/50 w-full sm:w-auto dark:bg-amber-500 dark:hover:bg-amber-400 dark:focus-visible:ring-amber-400/50"
                            >
                                Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative h-screen" v-if="sendMessageUrl === null">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center">
                <img
                    class="inline h-8 w-8 animate-spin text-gray-200 dark:text-gray-600 dark:invert"
                    style="border-radius: 40px"
                    :src="loadingSpinner"
                    alt="spinner"
                    title="spinner"
                />
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</template>
