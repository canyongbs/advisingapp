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

    // TODO Replace with actual API endpoint from props...
    fetch('http://test.advisingapp.local/api/portal/knowledge-management/categories/' + route.params.categoryId)
        .then((response) => response.json())
        .then((json) => {
            console.log(json);
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
