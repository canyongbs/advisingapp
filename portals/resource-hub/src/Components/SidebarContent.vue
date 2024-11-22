<!--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
import { consumer } from '@/Services/Consumer.js';
import { useAuthStore } from '@/Stores/auth.js';
import { useTokenStore } from '@/Stores/token.js';
import { defineProps } from 'vue';

const props = defineProps({
    categories: {
        type: Object,
        default: {},
    },
    apiUrl: {
        type: String,
        required: true,
    },
});

const { removeToken } = useTokenStore();
const { portalRequiresAuthentication } = useAuthStore();

const logout = () => {
    const { post } = consumer();

    post(props.apiUrl + '/authenticate/logout').then((response) => {
        if (!response.data.success) {
            return;
        }

        removeToken();
        window.location.href = response.data.redirect_url;
    });
};
</script>

<template>
    <nav>
        <div class="flex flex-col gap-1 px-6 py-4 border-b">
            <router-link :to="{ name: 'home' }">
                <h3 class="text-2xl text-primary-800 font-semibold">
                    <span class="mr-1">🛟</span> <span>Help Center</span>
                </h3>
            </router-link>

            <div>
                <button
                    v-if="portalRequiresAuthentication === true"
                    @click="logout"
                    type="button"
                    class="text-gray-700 hover:text-primary-700 text-sm font-medium hover:underline focus:underline"
                >
                    Sign out
                </button>
            </div>
        </div>

        <ul role="list" class="my-2 flex flex-col gap-y-1">
            <li v-for="category in categories" :key="category.id">
                <router-link
                    :to="{ name: 'view-category', params: { categoryId: category.id } }"
                    active-class="text-primary-950 bg-gray-100"
                    class="w-full text-gray-700 group flex items-start gap-x-3 px-6 py-2 text-sm font-medium transition hover:bg-gray-100 hover:text-primary-950"
                >
                    <span
                        v-if="category.icon"
                        v-html="category.icon"
                        class="text-primary-700"
                        aria-hidden="true"
                    ></span>

                    <span class="mt-0.5">
                        {{ category.name }}
                    </span>
                </router-link>
            </li>
        </ul>
    </nav>
</template>
