<template>
  <div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">
      <h1 class="mb-2 text-center text-2xl font-bold text-blue-800">Criar conta</h1>
      <p class="mb-6 text-center text-sm text-slate-500">Comece a organizar suas finanças</p>

      <AlertaMensagem :texto="mensagem_erro" tipo="erro" />

      <form @submit.prevent="enviarRegistro">
        <CampoFormulario rotulo="Nome" :erro="erros.nome?.[0]">
          <input
            v-model="formulario.nome"
            type="text"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
          />
        </CampoFormulario>

        <CampoFormulario rotulo="E-mail" :erro="erros.email?.[0]">
          <input
            v-model="formulario.email"
            type="email"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
          />
        </CampoFormulario>

        <CampoFormulario rotulo="Senha" :erro="erros.senha?.[0]">
          <input
            v-model="formulario.senha"
            type="password"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
          />
        </CampoFormulario>

        <CampoFormulario rotulo="Confirmar senha" :erro="erros.senha?.[0]">
          <input
            v-model="formulario.senha_confirmacao"
            type="password"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
          />
        </CampoFormulario>

        <button
          type="submit"
          class="w-full rounded-xl bg-blue-600 py-3 font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
          :disabled="carregando"
        >
          {{ carregando ? 'Cadastrando...' : 'Cadastrar' }}
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-slate-600">
        Já tem conta?
        <router-link to="/login" class="font-semibold text-blue-600 hover:underline">
          Fazer login
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

const formulario = reactive({
  nome: '',
  email: '',
  senha: '',
  senha_confirmacao: '',
})

const emit = defineEmits(['autenticado'])

async function enviarRegistro() {
  carregando.value = true
  mensagem_erro.value = ''
  Object.keys(erros).forEach((chave) => delete erros[chave])

  try {
    const { data } = await api.post('/registrar', {
      nome: formulario.nome,
      email: formulario.email,
      senha: formulario.senha,
      senha_confirmation: formulario.senha_confirmacao,
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
