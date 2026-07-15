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
import { createPinia } from 'pinia';
import PrimeVue from 'primevue/config';
import { createApp, defineCustomElement, getCurrentInstance, h } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import VueSignaturePad from 'vue-signature-pad';
import App from './App.vue';
import config from './formkit.config.js';
import './portal.css';
import getAppContext from './Services/GetAppContext.js';
import { useNavigationStore } from './Stores/navigation.js';
import { useResourceHubStore } from './Stores/resourceHub.js';

customElements.define(
    'resource-hub-portal-embed',
    defineCustomElement({
        setup(props) {
            const app = createApp();
            const pinia = createPinia();

            app.use(pinia);
            app.use(VueSignaturePad);
            app.use(PrimeVue, {
                theme: 'none',
            });

            const { baseUrl } = getAppContext(props.accessUrl);

            const router = createRouter({
                history: createWebHistory(),
                routes: [
                    {
                        path: baseUrl + '/',
                        name: 'home',
                        component: () => import('./Pages/Home.vue'),
                        meta: {
                            load: (to, store) => store.loadHome(),
                        },
                    },
                    {
                        path: baseUrl + '/categories/:categoryId',
                        name: 'view-category',
                        component: () => import('./Pages/ViewCategory.vue'),
                        meta: {
                            load: (to, store) => store.loadCategory(to.params.categoryId),
                        },
                    },
                    {
                        path: baseUrl + '/categories/:parentCategoryId/:categoryId',
                        name: 'view-subcategory',
                        component: () => import('./Pages/ViewCategory.vue'),
                        meta: {
                            load: (to, store) => store.loadCategory(to.params.categoryId),
                        },
                    },
                    {
                        path: baseUrl + '/categories/:categoryId/articles/:articleId',
                        name: 'view-article',
                        component: () => import('./Pages/ViewArticle.vue'),
                    },
                ],
            });

            app.use(router);

            const resourceHubStore = useResourceHubStore(pinia);
            const navigationStore = useNavigationStore(pinia);

            router.beforeEach((to, from) => {
                // Only show the progress bar for real client-side navigations between
                // different pages. The initial load is covered by the boot spinner, and
                // in-page query changes (pagination / tab switches) should not trigger it.
                if (from.name && to.path !== from.path) {
                    navigationStore.start();
                }
            });

            router.beforeResolve(async (to, from) => {
                // The initial route's data is fetched during app boot, behind the
                // full-screen spinner, so there is nothing to load here for it.
                if (!from.name) {
                    return;
                }

                if (typeof to.meta.load === 'function') {
                    try {
                        await to.meta.load(to, resourceHubStore);
                    } catch (error) {
                        console.error(`Resource Hub Portal navigation ${error}`);
                    }
                }
            });

            router.afterEach(() => {
                navigationStore.done();
            });

            router.onError(() => {
                navigationStore.done();
            });

            app.config.devtools = true;

            // FormKit plugin
            app.use(plugin, defaultConfig(config));

            const inst = getCurrentInstance();
            Object.assign(inst.appContext, app._context);
            Object.assign(inst.provides, app._context.provides);

            return () => h(App, props);
        },
        props: [
            'url',
            'userAuthenticationUrl',
            'accessUrl',
            'searchUrl',
            'appUrl',
            'apiUrl',
            'tags',
            'cssUrl',
            'appTitle',
        ],
    }),
);
