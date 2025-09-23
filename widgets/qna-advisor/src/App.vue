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
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js/dist/web/pusher';
import { defineProps, onMounted, onUnmounted, ref } from 'vue';
import advisorDefaultAvatarUrl from '../../../resources/images/canyon-ai-headshot.jpg?url';
import loadingSpinner from '../public/images/loading-spinner.svg?url';
import userAvatar from '../public/images/user-default-avatar.svg?url';
import { useAuthStore } from './stores/auth';

const props = defineProps(['url']);
const authStore = useAuthStore();
const requiresAuthentication = ref(false);
const authentication = ref({
    promptToAuthenticate: false,
    requestUrl: null,
    refreshUrl: null,
    email: null,
    code: null,
    isRequested: false,
    requestedMessage: null,
    confirmationUrl: null,
    registrationAllowed: false,
});
const loadingError = ref(null);
const sendMessageUrl = ref(null);
const threadId = ref(null);
const finishThreadUrl = ref(null);
const message = ref('');
const messages = ref([]);
const currentResponse = ref('');
const isLoading = ref(false);
const isThreadFinished = ref(false);
const isSplashScreenVisible = ref(true);
const advisor = ref({
    name: null,
    description: null,
    avatar_url: null,
});
let websocketChannel = null;

const scriptUrl = new URL(document.currentScript.getAttribute('src'));
const protocol = scriptUrl.protocol;
const scriptHostname = scriptUrl.hostname;
const hostUrl = `${protocol}//${scriptHostname}`;

onMounted(async () => {
    axios
        .post(props.url)
        .then((response) => {
            const json = response.data;

            requiresAuthentication.value = json.requires_authentication;
            authentication.value.requestUrl = json.authentication_url;
            authentication.value.refreshUrl = json.refresh_url;
            sendMessageUrl.value = json.send_message_url;
            advisor.value = json.advisor;

            if (requiresAuthentication.value === true && authStore.accessToken === null) {
                authentication.value.promptToAuthenticate = true;
            } else {
                // If authentication is not required or we already have a token, ensure prompt is false and setup websockets
                authentication.value.promptToAuthenticate = false;
                setupWebsockets(json.websockets_config);
            }
        })
        .catch((error) => {
            if (error.response && error.response.data.error) {
                loadingError.value = error.response.data.error;
            } else {
                loadingError.value = 'An error occurred while loading the advisor.';
            }
        });
});

onUnmounted(() => {
    if (websocketChannel) {
        websocketChannel.stopListening('advisor-message.chunk');
    }
    if (window.Echo) {
        window.Echo.disconnect();
    }
});

function setupWebsockets(config) {
    try {
        window.Pusher = Pusher;

        window.Echo = new Echo({
            ...config,
            authorizer: (channel, options) => {
                return {
                    authorize: (socketId, callback) => {
                        axios
                            .post(
                                config.authEndpoint,
                                {
                                    socket_id: socketId,
                                    channel_name: channel.name,
                                },
                                {
                                    headers: {
                                        'Content-Type': 'application/json',
                                        ...(requiresAuthentication.value && authStore.getAccessToken
                                            ? { Authorization: `Bearer ${authStore.getAccessToken}` }
                                            : {}),
                                    },
                                },
                            )
                            .then((response) => {
                                callback(false, response.data);
                            })
                            .catch((error) => {
                                callback(true, error);
                            });
                    },
                };
            },
        });
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
            thread_id: threadId.value,
        };

        const sendMessageResponse = await authorizedPost(sendMessageUrl.value, requestBody);

        const data = sendMessageResponse.data;

        if (!threadId.value) {
            threadId.value = data.thread_id;
            finishThreadUrl.value = data.finish_thread_url;

            let channelName = `qna-advisor-thread-${threadId.value}`;

            websocketChannel = requiresAuthentication.value
                ? window.Echo.private(channelName)
                : window.Echo.channel(channelName);

            websocketChannel.listen('.qna-advisor-message.chunk', (data) => {
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
                        from: 'advisor',
                        content: currentResponse.value,
                    });
                    currentResponse.value = '';
                    isLoading.value = false;
                }
            });
        }

        message.value = '';
    } catch (error) {
        console.error('Send message error:', error);
        isLoading.value = false;
    }
}

async function finishThread() {
    if (!finishThreadUrl.value) return;

    try {
        isThreadFinished.value = true;

        await authorizedPost(finishThreadUrl.value);
    } catch (error) {
        console.error('Finish thread error:', error);
    }
}

async function authenticate(formData, node) {
    node.clearErrors();

    if (authentication.value.isRequested) {
        const data = {
            code: formData.code,
        };

        axios
            .post(authentication.value.confirmationUrl, data)
            .then((response) => {
                if (response.errors) {
                    node.setErrors([], response.errors);

                    return;
                }

                if (typeof response.data.access_token !== 'undefined') {
                    authStore.$patch({ accessToken: response.data.access_token });
                    authentication.value.promptToAuthenticate = false;

                    setupWebsockets(response.data.websockets_config);
                }
            })
            .catch((error) => {
                node.setErrors([], error.response.data.errors);
            });

        return;
    }

    if (authentication.value.registrationAllowed) {
        axios
            .post(authentication.value.requestUrl, {
                email: formData.email,
                first_name: formData.first_name,
                last_name: formData.last_name,
                preferred: formData.preferred,
                mobile: formData.mobile,
                birthdate: formData.birthdate,
                address: formData.address,
                address_2: formData.address_2,
                city: formData.city,
                state: formData.state,
                postal: formData.postal,
            })
            .then((response) => {
                if (!response.data.authentication_url) {
                    node.setErrors([response.data.message]);

                    return;
                }

                authentication.value.isRequested = true;
                authentication.value.requestedMessage = response.data.message;
                authentication.value.confirmationUrl = response.data.authentication_url;
            })
            .catch((error) => {
                const data = error.response.data;

                node.setErrors([], data.errors);
            });

        return;
    }

    axios
        .post(authentication.value.requestUrl, {
            email: formData.email,
        })
        .then((response) => {
            if (!response.data.authentication_url) {
                node.setErrors([response.data.message]);

                return;
            }

            authentication.value.isRequested = true;
            authentication.value.requestedMessage = response.data.message;
            authentication.value.confirmationUrl = response.data.authentication_url;
        })
        .catch((error) => {
            let status = error.response.status;
            let data = error.response.data;

            if (status === 404 && data.registration_allowed) {
                authentication.value.registrationAllowed = true;
                authentication.value.isRequested = false;
                authentication.value.requestedMessage = data.message;
                authentication.value.requestUrl = data.authentication_url;

                return;
            }

            node.setErrors([], data.errors);
        });
}

function startNewChat() {
    isSplashScreenVisible.value = false;
}

async function authorizedPost(url, data) {
    const headers = {
        'Content-Type': 'application/json',
    };

    if (authStore.getAccessToken) {
        headers['Authorization'] = `Bearer ${authStore.getAccessToken}`;
    }

    try {
        return await axios.post(url, data, { headers });
    } catch (error) {
        if (error.response && error.response.status === 401) {
            // Token expired, try to refresh
            try {
                const refreshResponse = await axios.post(
                    authentication.value.refreshUrl,
                    {},
                    {
                        withCredentials: true,
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    },
                );

                if (refreshResponse.data && refreshResponse.data.access_token) {
                    // Save new token
                    authStore.$patch({ accessToken: refreshResponse.data.access_token });

                    // Retry original request with new token
                    const newHeaders = {
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${authStore.getAccessToken}`,
                    };

                    return await axios.post(url, data, { headers: newHeaders });
                }
            } catch (refreshError) {
                console.error('Token refresh failed:', refreshError);
                // If refresh fails, throw original error
                throw error;
            }
        }

        // If not a 401 error, throw the original error
        throw error;
    }
}
</script>

<template>
    <div
        class="h-full ring-1 ring-gray-300/50 rounded"
        style="
            --primary-50: 255, 251, 235;
            --primary-100: 254, 243, 199;
            --primary-200: 253, 230, 138;
            --primary-300: 252, 211, 77;
            --primary-400: 251, 191, 36;
            --primary-500: 245, 158, 11;
            --primary-600: 217, 119, 6;
            --primary-700: 180, 83, 9;
            --primary-800: 146, 64, 14;
            --primary-900: 120, 53, 15;
            --rounding-sm: 0.25rem;
            --rounding: 0.375rem;
            --rounding-md: 0.5rem;
            --rounding-lg: 0.75rem;
            --rounding-full: 9999px;
        "
    >
        <div
            class="flex h-full items-center justify-center"
            v-if="isSplashScreenVisible && sendMessageUrl !== null && advisor.name"
        >
            <div class="w-full max-w-4xl mx-auto p-8">
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex-shrink-0">
                        <img
                            v-if="advisor.avatar_url"
                            class="h-32 w-32 md:h-48 md:w-48 object-cover rounded-full shadow-lg"
                            :src="advisor.avatar_url"
                            :alt="advisor.name"
                            :title="advisor.name"
                        />
                        <img
                            v-else
                            class="h-32 w-32 md:h-48 md:w-48 object-cover rounded-full shadow-lg"
                            :src="advisorDefaultAvatarUrl"
                            :alt="advisor.name"
                            :title="advisor.name"
                        />
                    </div>

                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                            {{ advisor.name }}
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                            {{ advisor.description }}
                        </p>
                        <button
                            @click="startNewChat"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-primary-500 hover:bg-primary-400 focus:bg-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-400/50 rounded-md shadow-md transition-colors duration-200"
                        >
                            Start New Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex h-full items-center justify-center"
            v-if="!isSplashScreenVisible && authentication.promptToAuthenticate"
        >
            <div class="w-full max-w-sm">
                <FormKit type="form" @submit="authenticate" v-model="authentication">
                    <FormKit
                        type="email"
                        label="Your email address"
                        name="email"
                        validation="required|email"
                        validation-visibility="submit"
                        :disabled="authentication.isRequested"
                    />

                    <div v-if="authentication.registrationAllowed">
                        <p class="text-gray-700 font-medium text-xs my-3">
                            You are not registered yet. Please fill in the form below to register.
                        </p>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="text"
                                    label="First Name"
                                    name="first_name"
                                    validation="required|alpha|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="text"
                                    label="Last Name"
                                    name="last_name"
                                    validation="required|alpha|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="text"
                                    label="Preferred Name"
                                    name="preferred"
                                    validation="required|alpha|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="date"
                                    label="Birth Date"
                                    name="birthdate"
                                    validation="required"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="tel"
                                    label="Mobile"
                                    name="mobile"
                                    placeholder="xxx-xxx-xxxx"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="text"
                                    label="Address"
                                    name="address"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="text"
                                    label="Apartment/Unit Number"
                                    name="address_2"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="text"
                                    label="City"
                                    name="city"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <FormKit
                                    type="text"
                                    label="State"
                                    name="state"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <FormKit
                                    type="text"
                                    label="Postal"
                                    name="postal"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>
                    </div>

                    <p v-if="authentication.requestedMessage" class="text-sm">
                        {{ authentication.requestedMessage }}
                    </p>

                    <FormKit
                        type="otp"
                        digits="6"
                        label="Authentication code"
                        name="code"
                        help="We've sent a code to your email address."
                        validation="required"
                        validation-visibility="submit"
                        v-if="authentication.isRequested"
                    />
                </FormKit>
            </div>
        </div>

        <div
            v-show="!isSplashScreenVisible && sendMessageUrl !== null && !authentication.promptToAuthenticate"
            class="flex flex-col gap-y-3 w-11/12 mx-auto"
        >
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
                                    :class="{ 'dark:invert': message.from !== 'advisor' }"
                                    style="border-radius: 40px"
                                    :src="
                                        message.from === 'advisor'
                                            ? advisor.avatar_url || advisorDefaultAvatarUrl
                                            : userAvatar
                                    "
                                    :alt="message.from === 'advisor' ? advisor.name : 'User'"
                                    :title="message.from === 'advisor' ? advisor.name : 'User'"
                                />
                            </div>
                            <div class="relative flex w-full flex-col gap-1 md:gap-3 tex-gray-900 dark:text-white">
                                {{ message.content }}
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    v-if="!isThreadFinished"
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
                                class="relative rounded-md font-semibold outline-none focus-visible:ring-2 px-3 py-2 text-sm bg-gray-600 text-white hover:bg-gray-500 focus-visible:ring-gray-500/50 w-full sm:w-auto dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus-visible:ring-primary-400/50"
                            >
                                Send
                            </button>

                            <button
                                v-if="finishThreadUrl"
                                @click="finishThread"
                                :disabled="isLoading"
                                class="relative rounded-md font-semibold outline-none focus-visible:ring-2 px-3 py-2 text-sm bg-white text-gray-900 hover:bg-gray-100 focus-visible:ring-gray-500/50 w-full sm:w-auto dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus-visible:ring-gray-500/50 ring-1 ring-gray-300/50"
                            >
                                End chat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative h-screen" v-if="sendMessageUrl === null">
            <div v-if="!loadingError" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center">
                <img
                    class="inline h-8 w-8 animate-spin text-gray-200 dark:text-gray-600 dark:invert"
                    style="border-radius: 40px"
                    :src="loadingSpinner"
                    alt="spinner"
                    title="spinner"
                />
                <span class="sr-only">Loading...</span>
            </div>
            <div
                v-if="loadingError"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center text-red-600 dark:text-red-400"
            >
                <p>Error loading the advisor: {{ loadingError }}</p>
            </div>
        </div>
    </div>
</template>
