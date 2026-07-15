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
import { defaultConfig, plugin } from '@formkit/vue';
import { PiniaColada } from '@pinia/colada';
import { createPinia } from 'pinia';
import PrimeVue from 'primevue/config';
import { createApp, defineCustomElement, getCurrentInstance, h } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { DataLoaderPlugin } from 'vue-router/experimental';
import VueSignaturePad from 'vue-signature-pad';
import App from './App.vue';
import { bootPortal } from './Composables/usePortalBoot.js';
import config from './formkit.config.js';
import { useArticleData, useCategoriesData, useCategoryData } from './Pages/loaders.js';
import './portal.css';
import getAppContext from './Services/GetAppContext.js';

customElements.define(
    'resource-hub-portal-embed',
    defineCustomElement({
        setup(props) {
            const app = createApp();
            const pinia = createPinia();

            app.use(pinia);
            app.use(PiniaColada);
            app.use(VueSignaturePad);
            app.use(PrimeVue, {
                theme: 'none',
            });

            const { baseUrl } = getAppContext(props.accessUrl);

            const router = createRouter({
                history: createWebHistory(),
                scrollBehavior(to, from, savedPosition) {
                    // Restore the previous position on back/forward navigation.
                    if (savedPosition) {
                        return savedPosition;
                    }

                    // Same page, only the query changed (pagination / tab switches) — leave
                    // the scroll position where it is.
                    if (to.path === from.path) {
                        return false;
                    }

                    // Visiting a new page — start at the top.
                    return { top: 0 };
                },
                routes: [
                    {
                        path: baseUrl + '/',
                        name: 'home',
                        component: () => import('./Pages/Home.vue'),
                        meta: {
                            loaders: [useCategoriesData],
                        },
                    },
                    {
                        path: baseUrl + '/categories/:categoryId',
                        name: 'view-category',
                        component: () => import('./Pages/ViewCategory.vue'),
                        meta: {
                            loaders: [useCategoryData],
                        },
                    },
                    {
                        path: baseUrl + '/categories/:parentCategoryId/:categoryId',
                        name: 'view-subcategory',
                        component: () => import('./Pages/ViewCategory.vue'),
                        meta: {
                            loaders: [useCategoryData],
                        },
                    },
                    {
                        path: baseUrl + '/categories/:categoryId/articles/:articleId',
                        name: 'view-article',
                        component: () => import('./Pages/ViewArticle.vue'),
                        meta: {
                            loaders: [useArticleData],
                        },
                    },
                ],
            });

            // Boot the portal (config + auth) exactly once, and gate every navigation —
            // including the initial one — on it so the route data loaders always run with
            // a resolved API base URL and auth state. Registered before DataLoaderPlugin
            // so it runs ahead of the loader guard.
            const configReady = bootPortal(props, pinia);
            router.beforeEach(() => configReady);

            app.use(DataLoaderPlugin, { router });
            app.use(router);

            app.config.devtools = true;

            // FormKit plugin
            app.use(plugin, defaultConfig(config));

            const inst = getCurrentInstance();
            Object.assign(inst.appContext, app._context);
            Object.assign(inst.provides, app._context.provides);

            return () => h(App, props);
        },
        props: ['url', 'userAuthenticationUrl', 'accessUrl', 'searchUrl', 'appUrl', 'apiUrl', 'cssUrl', 'appTitle'],
    }),
);
