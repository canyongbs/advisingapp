/*
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
*/

/**
 * Route-level data loaders for the resource hub portal.
 *
 * These are Pinia Colada data loaders (Vue Router experimental data loaders API).
 * They are attached to routes via `meta.loaders` in `portal.js` and are awaited
 * *before* a navigation resolves, which keeps the previous page visible until the
 * destination's data is ready (no blank flash), and caches results via Pinia Colada
 * so repeat navigations are instant with background revalidation.
 */
import { defineColadaLoader } from 'vue-router/experimental/pinia-colada';
import { apiGet } from '../Services/api.js';

export const useCategoriesData = defineColadaLoader({
    key: () => ['resource-hub', 'categories'],
    query: () => apiGet('/categories'),
});

export const useCategoryData = defineColadaLoader({
    key: (to) => ['resource-hub', 'category', String(to.params.categoryId)],
    query: (to) => apiGet(`/categories/${to.params.categoryId}`),
});

export const useArticleData = defineColadaLoader({
    key: (to) => ['resource-hub', 'article', String(to.params.categoryId), String(to.params.articleId)],
    query: async (to) => {
        try {
            return await apiGet(`/categories/${to.params.categoryId}/articles/${to.params.articleId}`);
        } catch (error) {
            // Treat missing / unauthorized articles as "not found" so the navigation still
            // resolves and the page can render its own 404 state instead of failing.
            if (error?.response && [401, 404].includes(error.response.status)) {
                return null;
            }

            throw error;
        }
    },
});
