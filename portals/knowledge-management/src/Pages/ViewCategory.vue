<!--
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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
import { defineProps, ref, watch, onMounted } from 'vue';
import { useRoute, onBeforeRouteUpdate } from 'vue-router';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Loading from '@/Components/Loading.vue';

const route = useRoute();

const props = defineProps({
    searchUrl: {
        type: String,
        required: true,
    },
    apiUrl: {
        type: String,
        required: true,
    },
    categories: {
        type: Object,
        required: true,
    },
});

const loadingResults = ref(true);
const category = ref(null);
const articles = ref(null);

watch(
    route,
    function (newRouteValue) {
        getData();
    },
    {
        immediate: true,
    },
);

onMounted(function () {
    getData();
});

function getData() {
    loadingResults.value = true;

    fetch(props.apiUrl + '/categories/' + route.params.categoryId)
        .then((response) => response.json())
        .then((json) => {
            category.value = json.category;
            articles.value = json.articles;
            loadingResults.value = false;
        });
}
</script>

<template>
    <div>
        <div v-if="loadingResults">
            <Loading />
        </div>
        <div v-else>
            <Breadcrumbs
                :currentCrumb="category.name"
                :breadcrumbs="[{ name: 'Help Center', route: 'home' }]"
            ></Breadcrumbs>

            <main class="py-10">
                <div class="border border-gray-200 rounded p-4 shadow">
                    <h4 class="text-lg font-bold text-gray-900">Articles</h4>
                    <div v-if="articles.length > 0">
                        <ul role="list" class="divide-y divide-gray-200 mt-2">
                            <li v-for="article in articles" :key="article.id" class="py-4 border-gray-200">
                                <router-link :to="{ name: 'view-article', params: { articleId: article.id } }">
                                    <h5 class="text-md text-gray-800">{{ article.name }}</h5>
                                </router-link>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <p class="text-gray-500 mt-2">No articles found for {{ category.name }}</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
