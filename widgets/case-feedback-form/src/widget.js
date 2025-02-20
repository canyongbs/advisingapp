import { createApp, defineCustomElement, getCurrentInstance, h } from 'vue';
import './widget.css';
import App from './App.vue';
import { defaultConfig, plugin } from '@formkit/vue';
import config from './formkit.config.js';
import { createPinia } from 'pinia';

customElements.define(
    'case-feedback-form-embed',
    defineCustomElement({
        setup(props) {
            const app = createApp();
            const pinia = createPinia();

            app.use(pinia);

            app.use(plugin, defaultConfig(config));

            app.config.devtools = true;

            const inst = getCurrentInstance();
            Object.assign(inst.appContext, app._context);
            Object.assign(inst.provides, app._context.provides);

            return () => h(App, props);
        },
        props: ['url', 'userAuthenticationUrl', 'accessUrl', 'appUrl', 'apiUrl'],
    }),
);