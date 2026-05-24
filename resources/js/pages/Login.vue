<template>
  <div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">
      <h1 class="mb-2 text-center text-2xl font-bold text-blue-800">Bem-vindo de volta</h1>
      <p class="mb-4 text-center text-sm text-slate-500">Entre para controlar seus gastos</p>

      <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-900">
        <p class="mb-2 font-semibold">Credenciais de demonstração</p>
        <p><span class="text-blue-600">E-mail:</span> {{ credenciais_demo.email }}</p>
        <p><span class="text-blue-600">Senha:</span> {{ credenciais_demo.senha }}</p>
        <button
          type="button"
          class="mt-2 text-xs font-semibold text-blue-700 underline hover:text-blue-900"
          @click="preencherCredenciaisDemo"
        >
          Preencher automaticamente
        </button>
      </div>

      <AlertaMensagem :texto="mensagem_erro" tipo="erro" />

      <form @submit.prevent="enviarLogin">
        <CampoFormulario rotulo="E-mail" :erro="erros.email?.[0]">
          <input
            v-model="formulario.email"
            type="email"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
            placeholder="seu@email.com"
          />
        </CampoFormulario>

        <CampoFormulario rotulo="Senha" :erro="erros.senha?.[0]">
          <input
            v-model="formulario.senha"
            type="password"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
            placeholder="••••••••"
          />
        </CampoFormulario>

        <button
          type="submit"
          class="w-full rounded-xl bg-blue-600 py-3 font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
          :disabled="carregando"
        >
          {{ carregando ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-slate-600">
        Não tem conta?
        <router-link to="/registro" class="font-semibold text-blue-600 hover:underline">
          Criar conta
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import api, { configurarTokenAutenticacao, obterErrosValidacao, obterMensagemErro } from '../servicos/api'
import AlertaMensagem from '../components/AlertaMensagem.vue'
import CampoFormulario from '../components/CampoFormulario.vue'

const router = useRouter()
const carregando = ref(false)
const mensagem_erro = ref('')
const erros = reactive({})

const credenciais_demo = {
  email: 'user1@teste.local',
  senha: 'senha123',
}

const formulario = reactive({
  email: '',
  senha: '',
})

const emit = defineEmits(['autenticado'])

function preencherCredenciaisDemo() {
  formulario.email = credenciais_demo.email
  formulario.senha = credenciais_demo.senha
}

async function enviarLogin() {
  carregando.value = true
  mensagem_erro.value = ''
  Object.keys(erros).forEach((chave) => delete erros[chave])

  try {
    const { data } = await api.post('/entrar', {
      email: formulario.email,
      senha: formulario.senha,
    })

    localStorage.setItem('token_acesso', data.token)
    localStorage.setItem('usuario_logado', JSON.stringify(data.usuario))
    configurarTokenAutenticacao(data.token)
    emit('autenticado', data.usuario)
    router.push('/dashboard')
  } catch (erro) {
    Object.assign(erros, obterErrosValidacao(erro))
    mensagem_erro.value = obterMensagemErro(erro)
  } finally {
    carregando.value = false
  }
}
</script>
