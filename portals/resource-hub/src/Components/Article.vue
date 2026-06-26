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
    import { ChevronRightIcon } from '@heroicons/vue/20/solid/index.js';
    import Tags from './Tags.vue';

    defineProps({
        article: {
            type: Object,
            required: true,
        },
    });
</script>

<template>
    <router-link
        :to="{
            name: 'view-article',
            params: {
                categorySlug: article.categorySlug,
                articleId: article.id,
            },
        }"
        class="group flex items-center gap-x-3 px-6 py-3 transition duration-75 hover:bg-gray-50"
    >
        <div class="flex-1 min-w-0 flex flex-col gap-y-1.5">
            <span class="text-sm font-medium text-gray-700">{{ article.name }}</span>
            <Tags :tags="article.tags" :featured="article.featured" />
        </div>
        <ChevronRightIcon
            class="size-5 shrink-0 text-gray-400 opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100"
        />
    </router-link>
</template>
