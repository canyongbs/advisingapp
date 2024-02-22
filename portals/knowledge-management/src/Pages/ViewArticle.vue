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
import { defineProps, ref, watch, onMounted } from 'vue';
import { useRoute, onBeforeRouteUpdate } from 'vue-router';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Loading from '@/Components/Loading.vue';
import DOMPurify from 'dompurify';

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

const loading = ref(true);
const category = ref(null);
const article = ref(null);

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
    loading.value = true;

    fetch(props.apiUrl + '/categories/' + route.params.categoryId + '/articles/' + route.params.articleId)
        .then((response) => response.json())
        .then((json) => {
            category.value = json.category;
            article.value = json.article;
            loading.value = false;
        });
}
</script>

<template>
    <div v-if="loading">
        <Loading />
    </div>
    <div v-else>
        <Breadcrumbs
            :currentCrumb="article.name"
            :breadcrumbs="[
                { name: 'Help Center', route: 'home' },
                { name: category.name, route: 'view-category', params: { categoryId: category.id } },
            ]"
        ></Breadcrumbs>

        <div class="w-full mt-4 flex justify-center">
            <div class="prose">
                <h1 class="text-3xl font-semibold mt-4">{{ article.name }}</h1>
                <span>Last Updated: {{ article.lastUpdated }}</span>
                <div v-html="DOMPurify.sanitize(article.content)"></div>
            </div>
        </div>
    </div>
</template>
