<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
    import Footer from '@common/portal/Footer.vue';
    import Heading from '@common/portal/Heading.vue';
    import { FormKit } from '@formkit/vue';
    import { ref } from 'vue';

    const authentication = defineModel('authentication', {
        type: Object,
        required: true,
    });

    const props = defineProps({
        requiresAuthentication: {
            type: Boolean,
            required: true,
        },
        headerLogo: {
            type: String,
            required: true,
        },
        footerLogo: {
            type: String,
            required: true,
        },
        appName: {
            type: String,
            required: true,
        },
    });

    const emit = defineEmits(['authenticate', 'cancel']);

    const submitting = ref(false);

    function handleSubmit(formData, node) {
        submitting.value = true;
        emit('authenticate', formData, node, () => {
            submitting.value = false;
        });
    }
</script>

<template>
    <div class="flex min-h-screen w-full flex-col bg-gray-50 text-gray-950 antialiased">
        <div
            class="sticky top-0 z-10 flex justify-center items-center w-full border-b border-gray-200 flex-shrink-0 p-4 bg-gray-50"
        >
            <img :src="headerLogo" class="h-9 block" />
        </div>

        <main class="mx-auto flex flex-1 justify-center items-center w-full px-4 md:px-6 lg:px-8 max-w-screen-lg py-8">
            <div class="w-full max-w-md">
                <Heading title="Login to Resource Hub" class="text-center" />

                <FormKit type="form" @submit="handleSubmit" v-model="authentication" :actions="false">
                    <div class="mt-8 flex flex-col gap-6">
                        <div class="-mb-4">
                            <FormKit
                                type="email"
                                label="Email address"
                                name="email"
                                validation="required|email"
                                validation-visibility="submit"
                                :disabled="authentication.isRequested || authentication.registrationAllowed"
                            />
                        </div>

                        <div v-if="authentication.registrationAllowed" class="flex flex-col gap-6">
                            <p class="text-sm text-gray-500">
                                You are not registered yet. Please fill in the form below to register.
                            </p>

                            <div class="-mb-4">
                                <FormKit
                                    type="text"
                                    label="First Name*"
                                    name="first_name"
                                    validation="required|alpha|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>

                            <div class="-mb-4">
                                <FormKit
                                    type="text"
                                    label="Last Name*"
                                    name="last_name"
                                    validation="required|alpha|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>

                            <div class="-mb-4">
                                <FormKit
                                    type="text"
                                    label="Preferred Name"
                                    name="preferred"
                                    validation="alpha|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>

                            <div class="-mb-4">
                                <FormKit
                                    type="tel"
                                    label="Mobile*"
                                    name="mobile"
                                    placeholder="xxx-xxx-xxxx"
                                    validation="required|length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>

                            <div class="-mb-4">
                                <FormKit
                                    type="tel"
                                    label="Other Phone"
                                    name="phone"
                                    placeholder="xxx-xxx-xxxx"
                                    validation="length:0,255"
                                    validation-visibility="submit"
                                />
                            </div>

                            <div class="-mb-4">
                                <FormKit
                                    type="select"
                                    label="SMS Opt Out"
                                    name="sms_opt_out"
                                    :value="0"
                                    :options="[
                                        { value: false, label: 'No' },
                                        { value: true, label: 'Yes' },
                                    ]"
                                    validation-visibility="submit"
                                />
                            </div>
                        </div>

                        <p v-if="authentication.requestedMessage" class="text-sm text-gray-500">
                            {{ authentication.requestedMessage }}
                        </p>

                        <div v-if="authentication.isRequested" class="-mb-4">
                            <FormKit
                                type="otp"
                                digits="6"
                                label="Enter the code here"
                                name="code"
                                validation="required"
                                validation-visibility="submit"
                            />
                        </div>

                        <div class="flex flex-col gap-3">
                            <button
                                type="submit"
                                :disabled="submitting"
                                :class="[
                                    'relative inline-grid w-full grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3.5 py-2.5 text-sm font-medium outline-none transition duration-75 bg-brand-600 text-white hover:bg-brand-500 focus-visible:ring-2 focus-visible:ring-brand-500/50',
                                    submitting && 'cursor-wait opacity-70',
                                ]"
                            >
                                <svg
                                    v-if="submitting"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="size-5 animate-spin"
                                >
                                    <path
                                        clip-rule="evenodd"
                                        d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                        fill-rule="evenodd"
                                        fill="currentColor"
                                        opacity="0.2"
                                    />
                                    <path
                                        d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z"
                                        fill="currentColor"
                                    />
                                </svg>
                                <span>
                                    {{ authentication.isRequested ? 'Sign in' : 'Send login code' }}
                                </span>
                            </button>

                            <button
                                v-if="!requiresAuthentication"
                                type="button"
                                class="inline-flex items-center justify-center gap-1.5 text-sm font-medium text-gray-700 outline-none hover:underline focus-visible:underline"
                                @click="emit('cancel')"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </FormKit>
            </div>
        </main>

        <Footer :logo="footerLogo" :appName="appName" />
    </div>
</template>
