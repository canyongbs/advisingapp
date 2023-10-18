import { createApp, defineCustomElement, getCurrentInstance, h } from 'vue';
import './widget.css';
import App from './App.vue';
import { defaultConfig, plugin } from '@formkit/vue';
import config from './formkit.config.js';

customElements.define(
    'form-embed',
    defineCustomElement({
        render: () => h(App),
        setup() {
            const app = createApp();

            // install plugins
            app.use(plugin, defaultConfig(config));

            app.config.devtools = true;

            const inst = getCurrentInstance();
            Object.assign(inst.appContext, app._context);
            Object.assign(inst.provides, app._context.provides);
        },
    }),
);
