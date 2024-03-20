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
import { defineProps } from 'vue';
import axios from '@/Globals/Axios.js';
import { useTokenStore } from '@/Stores/token.js';
import { useAuthStore } from '@/Stores/auth.js';

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
    axios.post(props.apiUrl + '/authenticate/logout').then((response) => {
        removeToken();
        window.location.href = response.data.redirect_url;
    });
};
</script>

<template>
    <nav class="flex flex-1 flex-col mt-4">
        <div class="grid gap-y-2 justify-center">
            <router-link :to="{ name: 'home' }">
                <h3 class="text-xl text-primary-700">Help Center</h3>
            </router-link>

            <button
                v-if="portalRequiresAuthentication === true"
                @click="logout"
                type="button"
                class="p-2 font-bold rounded border-2 bg-white text-primary-600 dark:text-primary-400 border-primary-600 dark:border-primary-400"
            >
                Logout
            </button>
        </div>

        <ul role="list" class="flex flex-1 flex-col gap-y-7 mt-4">
            <li v-for="category in categories" :key="category.id">
                <div
                    class="bg-gray-100 text-gray-800 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                >
                    <span
                        v-if="category.icon"
                        v-html="category.icon"
                        class="text-primary-600 dark:text-primary-400"
                        aria-hidden="true"
                    >
                    </span>
                    <router-link
                        :to="{ name: 'view-category', params: { categoryId: category.id } }"
                        active-class="font-bold"
                    >
                        {{ category.name }}
                    </router-link>
                </div>
            </li>
        </ul>
    </nav>
</template>
