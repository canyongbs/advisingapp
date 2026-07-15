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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
    import BaseButton from '@common/BaseButton.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import Page from '@common/portal/Page.vue';
    import PageCard from '@common/portal/PageCard.vue';
    import ArticleAttachments from '@common/portal/article/ArticleAttachments.vue';
    import ArticleContent from '@common/portal/article/ArticleContent.vue';
    import ArticleMeta from '@common/portal/article/ArticleMeta.vue';
    // import ArticleFeedback from '@common/portal/article/ArticleFeedback.vue';
    import truncate from 'lodash/truncate';
    import { computed, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { useArticleData } from './loaders.js';

    const route = useRoute();
    const router = useRouter();

    const { data: articleData } = useArticleData();

    const category = computed(() => articleData.value?.category ?? null);
    const article = computed(() => articleData.value?.article ?? null);
    const parentCategory = computed(() => articleData.value?.category?.parentCategory ?? null);
    const portalViewCount = computed(() => articleData.value?.portal_view_count ?? 0);

    const feedback = ref(null);
    const helpfulVotePercentage = ref(0);

    watch(
        articleData,
        (data) => {
            feedback.value = data?.article?.vote ? data.article.vote.is_helpful : null;
            helpfulVotePercentage.value = data?.helpful_vote_percentage ?? 0;

            // The API may resolve to a canonical category for the article; keep the URL in sync.
            if (data?.category && data.category.id !== route.params.categoryId) {
                router.replace({
                    name: 'view-article',
                    params: { categoryId: data.category.id, articleId: route.params.articleId },
                });
            }
        },
        { immediate: true },
    );

    const breadcrumbs = computed(() => {
        if (article.value && category.value) {
            const breadcrumbsList = [];

            if (parentCategory.value) {
                breadcrumbsList.push({
                    name: parentCategory.value.name,
                    route: 'view-category',
                    params: { categoryId: parentCategory.value.id },
                });
            }

            breadcrumbsList.push({
                name: category.value.name,
                route: parentCategory.value ? 'view-subcategory' : 'view-category',
                params: parentCategory.value
                    ? { parentCategoryId: parentCategory.value.id, categoryId: category.value.id }
                    : { categoryId: category.value.id },
            });

            return breadcrumbsList;
        }

        return [];
    });

    const currentCrumb = computed(() => (article.value ? truncate(article.value.name, { length: 16 }) : 'Not Found'));

    // async function toggleFeedback(type) {
    //     try {
    //         const data = await apiPost('/resource_hub_article_vote/store', {
    //             article_vote: feedback.value === type ? null : type,
    //             article_id: route.params.articleId,
    //         });
    //
    //         if (Object.prototype.hasOwnProperty.call(data, 'is_helpful') && data.is_helpful !== null) {
    //             feedback.value = data.is_helpful;
    //         } else {
    //             feedback.value = null;
    //         }
    //
    //         helpfulVotePercentage.value = data.helpful_vote_percentage;
    //     } catch (error) {
    //         console.error('Error submitting feedback:', error);
    //     }
    // }
</script>

<template>
    <Page v-if="category && article">
        <template #heading>
            {{ article.name }}
        </template>

        <template #description>
            <ArticleMeta :viewCount="portalViewCount" :lastUpdated="article.lastUpdated" />
        </template>

        <template #breadcrumbs>
            <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="currentCrumb" />
        </template>

        <PageCard v-if="article.attachments && article.attachments.length > 0">
            <ArticleAttachments :attachments="article.attachments" />
        </PageCard>

        <PageCard>
            <ArticleContent :content="article.content" />

            <!-- <ArticleFeedback
                :feedback="feedback"
                :helpfulVotePercentage="helpfulVotePercentage"
                @toggle-feedback="toggleFeedback"
            /> -->
        </PageCard>
    </Page>

    <Page v-if="!category || !article">
        <template #heading> 404 Not Found </template>

        <PageCard>
            <EmptyState>
                <template #heading>Article Not Found</template>
                <template #description>The article you are looking for does not exist or has been removed.</template>
                <template #actions>
                    <BaseButton tag="router-link" :to="{ name: 'home' }" color="gray" size="md">
                        Return Home
                    </BaseButton>
                </template>
            </EmptyState>
        </PageCard>
    </Page>
</template>
