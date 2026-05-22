import './bootstrap';
import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import axios from 'axios'

// Configuração global do Axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content
axios.defaults.baseURL = '/'

// Interceptor global de erros
axios.interceptors.response.use(
  response => response,
  error => {
    return Promise.reject(error)
  }
)

// Rotas
import Teste from './pages/Teste.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/teste' },
    { path: '/teste', component: Teste },
  ],
})

const app = createApp(App)
app.use(router)

// Disponibilizar axios globalmente
app.config.globalProperties.$axios = axios

app.mount('#app')