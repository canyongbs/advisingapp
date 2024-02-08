<script setup>
import { defineProps, ref, watch, onMounted } from 'vue';
import { useRoute, onBeforeRouteUpdate } from 'vue-router';

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
    <div v-if="loading">Loading...</div>
    <div v-else>
        <h1>View {{ article.name }}</h1>
    </div>
</template>
