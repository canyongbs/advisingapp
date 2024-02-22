import { ref } from 'vue';
import { defineStore } from 'pinia';

export const useTokenStore = defineStore('token', () => {
    const token = ref(null);

    async function setToken(tokenToSet) {
        console.log('setToken', tokenToSet);
        token.value = tokenToSet;
        localStorage.setItem('token', token.value);
    }

    async function getToken() {
        return localStorage.getItem('token');
    }

    async function removeToken() {
        token.value = null;
        localStorage.removeItem('token');
    }

    return { token, getToken, setToken, removeToken };
});
