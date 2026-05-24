<template>
  <LayoutPrincipal @sair="$emit('sair')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
      <h2 class="text-2xl font-bold text-slate-800">Categorias de gasto</h2>
      <button
        type="button"
        class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
        @click="abrirFormularioCriar"
      >
        + Nova categoria
      </button>
    </div>

    <AlertaMensagem :texto="mensagem_sucesso" tipo="sucesso" />
    <AlertaMensagem :texto="mensagem_erro" tipo="erro" />

    <div v-if="exibir_formulario" class="mb-6 rounded-2xl bg-white p-6 shadow-md">
      <h3 class="mb-4 font-semibold text-slate-800">
        {{ categoria_editando ? 'Editar categoria' : 'Nova categoria' }}
      </h3>
      <form @submit.prevent="salvarCategoria">
        <CampoFormulario rotulo="Nome" :erro="erros.name?.[0]">
          <input
            v-model="formulario.name"
            type="text"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none"
            placeholder="Ex: Alimentação"
          />
        </CampoFormulario>
        <div class="flex gap-2">
          <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
            Salvar
          </button>
          <button type="button" class="rounded-xl bg-slate-100 px-4 py-2 text-slate-700" @click="fecharFormulario">
            Cancelar
          </button>
        </div>
      </form>
    </div>

    <div v-if="carregando" class="text-slate-500">Carregando categorias...</div>

    <ul v-else class="grid gap-3 sm:grid-cols-2">
      <li
        v-for="categoria in categorias"
        :key="categoria.id"
        class="flex items-center justify-between rounded-2xl bg-white p-4 shadow-md"
      >
        <span class="font-medium text-slate-800">{{ categoria.name }}</span>
        <div class="flex gap-2">
          <button class="text-sm text-blue-600 hover:underline" @click="abrirFormularioEditar(categoria)">
            Editar
          </button>
          <button class="text-sm text-rose-600 hover:underline" @click="excluirCategoria(categoria)">
            Excluir
          </button>
        </div>
      </li>
    </ul>

    <p v-if="!carregando && !categorias.length" class="text-slate-500">
      Nenhuma categoria cadastrada. Crie a primeira acima.
    </p>
  </LayoutPrincipal>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import api, { obterErrosValidacao, obterMensagemErro } from '../servicos/api'
import LayoutPrincipal from '../layouts/LayoutPrincipal.vue'
import AlertaMensagem from '../components/AlertaMensagem.vue'
import CampoFormulario from '../components/CampoFormulario.vue'

defineEmits(['sair'])

const categorias = ref([])
const carregando = ref(true)
const exibir_formulario = ref(false)
const categoria_editando = ref(null)
const mensagem_sucesso = ref('')
const mensagem_erro = ref('')
const erros = reactive({})

const formulario = reactive({ name: '' })

async function carregarCategorias() {
  carregando.value = true
  try {
    const { data } = await api.get('/categorias')
    categorias.value = data.categories
  } finally {
    carregando.value = false
  }
}

function abrirFormularioCriar() {
  categoria_editando.value = null
  formulario.name = ''
  exibir_formulario.value = true
  mensagem_erro.value = ''
}

function abrirFormularioEditar(categoria) {
  categoria_editando.value = categoria
  formulario.name = categoria.name
  exibir_formulario.value = true
  mensagem_erro.value = ''
}

function fecharFormulario() {
  exibir_formulario.value = false
  categoria_editando.value = null
}

async function salvarCategoria() {
  mensagem_erro.value = ''
  mensagem_sucesso.value = ''
  Object.keys(erros).forEach((chave) => delete erros[chave])

  try {
    if (categoria_editando.value) {
      await api.put(`/categorias/${categoria_editando.value.id}`, { name: formulario.name })
      mensagem_sucesso.value = 'Categoria atualizada com sucesso.'
    } else {
      await api.post('/categorias', { name: formulario.name })
      mensagem_sucesso.value = 'Categoria criada com sucesso.'
    }

    fecharFormulario()
    await carregarCategorias()
  } catch (erro) {
    Object.assign(erros, obterErrosValidacao(erro))
    mensagem_erro.value = obterMensagemErro(erro)
  }
}

async function excluirCategoria(categoria) {
  if (!confirm(`Excluir a categoria "${categoria.name}"?`)) return

  try {
    await api.delete(`/categorias/${categoria.id}`)
    mensagem_sucesso.value = 'Categoria excluída com sucesso.'
    await carregarCategorias()
  } catch (erro) {
    mensagem_erro.value = obterMensagemErro(erro)
  }
}

onMounted(carregarCategorias)
</script>
