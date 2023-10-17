import { defineCustomElement } from 'vue';
import './widget.css';
import App from './App.vue';

const container = document.getElementById('form-embed');

const AppElement = defineCustomElement(App)
customElements.define('form-embed', AppElement)

container.appendChild(
    new AppElement()
)
