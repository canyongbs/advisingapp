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

onMounted(async () => {
    try {
        const response = await fetch(props.url);
        const json = await response.json();
        if (json.error) throw new Error(json.error);
        sendMessageUrl.value = json.send_message_url;
        chatId.value = json.chat_id;

        // Initialize Laravel Echo for websockets
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
                        currentResponse.value += `\n\nError: ${data.error}`;
                        isLoading.value = false;
                        return;
                    }

                    if (data.content) {
                        currentResponse.value += data.content;
                    }

                    if (data.is_complete) {
                        // Add the completed agent response to messages
                        messages.value.push({
                            from: 'agent',
                            content: currentResponse.value,
                        });
                        currentResponse.value = '';
                        isLoading.value = false;
                    }
                })
                .listen('.advisor-message.next-request-options', (data) => {
                    // Store options for use in next request
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

    // Add user message to conversation history
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

        // Include next request options if available
        if (nextRequestOptions.value) {
            requestBody.options = nextRequestOptions.value;
        }

        const fetchResponse = await fetch(sendMessageUrl.value, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestBody),
        });

        const data = await fetchResponse.json();

        // Clear the message input
        message.value = '';
    } catch (error) {
        console.error('Send message error:', error);
        isLoading.value = false;
    }
}
</script>

<template>
    <div v-show="sendMessageUrl !== null">
        <div v-if="messages.length > 0">
            <div v-for="(message, index) in messages" :key="index">
                <div v-if="message.from === 'user'"><strong>User:</strong> {{ message.content }}</div>
                <div v-else><strong>Agent:</strong> {{ message.content }}</div>
            </div>
        </div>

        <div v-if="isLoading && currentResponse"><strong>Agent:</strong> {{ currentResponse }}</div>

        <div v-if="isLoading">
            <p>AI is typing...</p>
        </div>

        <div>
            <textarea v-model="message" placeholder="Ask your question..." :disabled="isLoading"></textarea>
            <button @click="sendMessage" :disabled="isLoading || !message.trim()">Send</button>
        </div>
    </div>
</template>
