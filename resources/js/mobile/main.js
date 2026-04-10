import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Configuration d'Axios
axios.defaults.baseURL = '/api';
const token = localStorage.getItem('auth_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Intercepteur pour gérer les erreurs 401 (token expiré)
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('auth_token');
            router.push('/login');
        }
        return Promise.reject(error);
    }
);

const app = createApp(App);
app.use(router);
app.mount('#app-mobile');
