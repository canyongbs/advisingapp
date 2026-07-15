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
    import BaseButton from '@common/BaseButton.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import Page from '@common/portal/Page.vue';
    import { ClockIcon, EyeIcon } from '@heroicons/vue/20/solid';
    import { HandThumbDownIcon, HandThumbUpIcon } from '@heroicons/vue/24/solid';
    import DOMPurify from 'dompurify';
    import truncate from 'lodash/truncate';
    import { computed, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import EmptyState from '../Components/EmptyState.vue';
    import PageCard from '../Components/PageCard.vue';
    import { apiPost } from '../Services/api.js';
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

    async function toggleFeedback(type) {
        try {
            const data = await apiPost('/resource_hub_article_vote/store', {
                article_vote: feedback.value === type ? null : type,
                article_id: route.params.articleId,
            });

            if (Object.prototype.hasOwnProperty.call(data, 'is_helpful') && data.is_helpful !== null) {
                feedback.value = data.is_helpful;
            } else {
                feedback.value = null;
            }

            helpfulVotePercentage.value = data.helpful_vote_percentage;
        } catch (error) {
            console.error('Error submitting feedback:', error);
        }
    }
</script>

<template>
    <Page v-if="category && article">
        <template #heading>
            {{ article.name }}
        </template>

        <template #description>
            <div class="flex flex-col sm:flex-row sm:items-center gap-y-1 gap-x-4">
                <div class="flex items-center gap-x-1.5">
                    <EyeIcon class="size-4 shrink-0" aria-hidden="true" />
                    <span>{{ portalViewCount }} Views</span>
                </div>
                <div class="flex items-center gap-x-1.5">
                    <ClockIcon class="size-4 shrink-0" aria-hidden="true" />
                    <span>Last updated: {{ article.lastUpdated }}</span>
                </div>
            </div>
        </template>

        <template #breadcrumbs>
            <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="currentCrumb" />
        </template>

        <PageCard>
            <div
                class="prose max-w-5xl w-full mx-auto prose-p:leading-snug! prose-p:my-2.5! prose-headings:my-4! prose-hr:my-5! prose-ul:my-3! prose-ol:my-3! prose-li:my-0! [&_li>p]:my-1! [&_td_p]:my-3! [&_th_p]:my-3! prose-table:w-full! prose-table:my-6 prose-table:border-separate prose-table:border-spacing-0 prose-table:rounded-lg prose-table:border prose-table:border-gray-200 prose-table:overflow-hidden prose-table:shadow-xs prose-td:border-b prose-td:border-gray-100 prose-td:align-middle prose-td:px-6 prose-td:py-2 prose-td:text-left prose-td:text-gray-700 prose-th:border-none prose-th:bg-brand-600 prose-th:px-6 prose-th:py-2 prose-th:text-left prose-th:font-bold prose-th:text-white [&_tr:last-child_td]:border-b-[3px]! [&_tr:last-child_td]:border-brand-600! even:prose-tr:bg-gray-50"
                v-html="
                    DOMPurify.sanitize(article.content, {
                        ADD_TAGS: ['iframe', 'video', 'source'],
                        ADD_ATTR: [
                            'allow',
                            'allowfullscreen',
                            'frameborder',
                            'controls',
                            'target',
                            'rel',
                            'data-video-embed',
                            'data-video-type',
                            'data-video-src',
                            'data-video-width',
                            'data-video-height',
                            'data-cols',
                            'data-col-span',
                            'data-from-breakpoint',
                            'data-color',
                            'data-id',
                        ],
                    })
                "
            ></div>

            <div
                class="max-w-5xl mx-auto w-full flex flex-wrap items-center gap-4 border border-gray-200 rounded-lg p-4 bg-white"
            >
                <span class="text-sm font-medium text-gray-700">Was this content helpful?</span>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="toggleFeedback(true)"
                        class="relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 focus-visible:ring-2"
                        :class="
                            feedback === true
                                ? 'bg-brand-600 text-white hover:bg-brand-500 focus-visible:ring-brand-500/50'
                                : 'bg-white text-gray-950 ring-1 ring-gray-950/10 hover:bg-gray-50'
                        "
                    >
                        <HandThumbUpIcon class="size-5" />
                        Yes
                    </button>

                    <button
                        type="button"
                        @click="toggleFeedback(false)"
                        class="relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 focus-visible:ring-2"
                        :class="
                            feedback === false
                                ? 'bg-brand-600 text-white hover:bg-brand-500 focus-visible:ring-brand-500/50'
                                : 'bg-white text-gray-950 ring-1 ring-gray-950/10 hover:bg-gray-50'
                        "
                    >
                        <HandThumbDownIcon class="size-5" />
                        No
                    </button>
                </div>

                <span v-if="helpfulVotePercentage" class="text-sm text-gray-500">
                    {{ helpfulVotePercentage }}% of visitors found this helpful.
                </span>
            </div>
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
