import './bootstrap'
import { createApp, ref } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import api, { configurarTokenAutenticacao } from './servicos/api'

import Login from './pages/Login.vue'
import Registro from './pages/Registro.vue'
import Dashboard from './pages/Dashboard.vue'
import Categorias from './pages/Categorias.vue'
import Despesas from './pages/Despesas.vue'

const usuarioLogado = ref(
  localStorage.getItem('usuario_logado')
    ? JSON.parse(localStorage.getItem('usuario_logado'))
    : null
)

const tokenSalvo = localStorage.getItem('token_acesso')
if (tokenSalvo) {
  configurarTokenAutenticacao(tokenSalvo)
}

const rotas = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: Login, meta: { convidado: true } },
  { path: '/registro', component: Registro, meta: { convidado: true } },
  { path: '/dashboard', component: Dashboard, meta: { requerAutenticacao: true } },
  { path: '/categorias', component: Categorias, meta: { requerAutenticacao: true } },
  { path: '/despesas', component: Despesas, meta: { requerAutenticacao: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes: rotas,
})

router.beforeEach((para, de, proximo) => {
  const estaAutenticado = !!localStorage.getItem('token_acesso')

  if (para.meta.requerAutenticacao && !estaAutenticado) {
    proximo('/login')
    return
  }

  if (para.meta.convidado && estaAutenticado) {
    proximo('/dashboard')
    return
  }

  proximo()
})

const app = createApp(App)

app.provide('usuarioLogado', usuarioLogado)

app.config.globalProperties.$axios = api

app.use(router)

app.mount('#app')

export { usuarioLogado, configurarTokenAutenticacao }
