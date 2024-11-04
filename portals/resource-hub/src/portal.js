/*
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
*/
import { createApp, defineCustomElement, getCurrentInstance, h } from 'vue';
import '@/portal.css';
import App from './App.vue';
import { createMemoryHistory, createRouter, createWebHistory } from 'vue-router';
import Home from '@/Pages/Home.vue';
import ViewCategory from '@/Pages/ViewCategory.vue';
import ViewArticle from '@/Pages/ViewArticle.vue';
import { defaultConfig, plugin } from '@formkit/vue';
import config from './formkit.config.js';
import getAppContext from '@/Services/GetAppContext.js';
import { createPinia } from 'pinia';

customElements.define(
    'resource-hub-portal-embed',
    defineCustomElement({
        setup(props) {
            const app = createApp();
            const pinia = createPinia();

            app.use(pinia);

            const { isEmbeddedInAdvisingApp, baseUrl } = getAppContext(props.accessUrl);

            const router = createRouter({
                history: isEmbeddedInAdvisingApp ? createWebHistory() : createMemoryHistory(),
                routes: [
                    {
                        path: baseUrl + '/',
                        name: 'home',
                        component: Home,
                    },
                    {
                        path: baseUrl + '/categories/:categoryId',
                        name: 'view-category',
                        component: ViewCategory,
                    },
                    {
                        path: baseUrl + '/categories/:categoryId/articles/:articleId',
                        name: 'view-article',
                        component: ViewArticle,
                    },
                ],
            });

            app.use(router);

            app.config.devtools = true;

            // FormKit plugin
            app.use(plugin, defaultConfig(config));

            const inst = getCurrentInstance();
            Object.assign(inst.appContext, app._context);
            Object.assign(inst.provides, app._context.provides);

            return () => h(App, props);
        },
        props: ['url', 'userAuthenticationUrl', 'accessUrl', 'searchUrl', 'appUrl', 'apiUrl'],
    }),
);
