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

    fetch(
        'http://test.advisingapp.local/api/portal/knowledge-management/categories/' +
            route.params.categoryId +
            '/articles/' +
            route.params.articleId,
    )
        .then((response) => response.json())
        .then((json) => {
            console.log(json);
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
                <div v-html="article.content"></div>
            </div>
        </div>
    </div>
</template>
