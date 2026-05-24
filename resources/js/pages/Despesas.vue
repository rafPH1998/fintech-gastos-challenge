<template>
  <LayoutPrincipal @sair="$emit('sair')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
      <h2 class="text-2xl font-bold text-slate-800">Despesas</h2>
      <button
        type="button"
        class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
        @click="abrirFormularioCriar"
      >
        + Nova despesa
      </button>
    </div>

    <AlertaMensagem :texto="mensagem_sucesso" tipo="sucesso" />
    <AlertaMensagem :texto="mensagem_erro" tipo="erro" />

    <div v-if="exibir_formulario" class="mb-6 rounded-2xl bg-white p-6 shadow-md">
      <h3 class="mb-4 font-semibold text-slate-800">
        {{ despesa_editando ? 'Editar despesa' : 'Nova despesa' }}
      </h3>
      <form @submit.prevent="salvarDespesa">
        <CampoFormulario rotulo="Descrição" :erro="erros.description?.[0]">
          <input
            v-model="formulario.description"
            type="text"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none"
          />
        </CampoFormulario>

        <CampoFormulario rotulo="Valor (R$)" :erro="erros.amount?.[0]">
          <input
            :value="formulario.valor_mascarado"
            type="text"
            inputmode="numeric"
            placeholder="R$ 0,00"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none"
            @input="atualizarValorMascarado"
          />
        </CampoFormulario>

        <CampoFormulario rotulo="Data" :erro="erros.date?.[0]">
          <input
            v-model="formulario.date"
            type="date"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none"
          />
        </CampoFormulario>

        <CampoFormulario rotulo="Categoria" :erro="erros.expenseCategoryId?.[0]">
          <select
            v-model="formulario.expense_category_id"
            class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-400 focus:outline-none"
          >
            <option value="">Selecione...</option>
            <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
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

    <div v-if="carregando" class="text-slate-500">Carregando despesas...</div>

    <div v-else class="space-y-3">
      <article
        v-for="despesa in despesas"
        :key="despesa.id"
        class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-md"
      >
        <div>
          <p class="font-semibold text-slate-800">{{ despesa.description }}</p>
          <p class="text-sm text-slate-500">
            {{ despesa.category_name }} · {{ formatarData(despesa.date) }}
          </p>
        </div>
        <div class="flex items-center gap-4">
          <span class="text-lg font-bold text-rose-600">{{ formatarMoeda(despesa.amount) }}</span>
          <button class="text-sm text-blue-600 hover:underline" @click="abrirFormularioEditar(despesa)">
            Editar
          </button>
          <button class="text-sm text-rose-600 hover:underline" @click="excluirDespesa(despesa)">
            Excluir
          </button>
        </div>
      </article>
    </div>

    <p v-if="!carregando && !despesas.length" class="text-slate-500">
      Nenhuma despesa cadastrada.
    </p>
  </LayoutPrincipal>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import api, { obterErrosValidacao, obterMensagemErro } from '../servicos/api'
import {
  aplicarMascaraMoeda,
  converterMascaraMoedaParaNumero,
  formatarNumeroComoMoeda,
} from '../utilitarios/mascaraMoeda'
import LayoutPrincipal from '../layouts/LayoutPrincipal.vue'
import AlertaMensagem from '../components/AlertaMensagem.vue'
import CampoFormulario from '../components/CampoFormulario.vue'

defineEmits(['sair'])

const despesas = ref([])
const categorias = ref([])
const carregando = ref(true)
const exibir_formulario = ref(false)
const despesa_editando = ref(null)
const mensagem_sucesso = ref('')
const mensagem_erro = ref('')
const erros = reactive({})

const formulario = reactive({
  description: '',
  valor_mascarado: '',
  date: new Date().toISOString().slice(0, 10),
  expense_category_id: '',
})

function atualizarValorMascarado(evento) {
  formulario.valor_mascarado = aplicarMascaraMoeda(evento.target.value)
}

function formatarMoeda(valor) {
  return Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function formatarData(data) {
  const [ano, mes, dia] = data.split('-')
  return `${dia}/${mes}/${ano}`
}

async function carregarDados() {
  carregando.value = true
  try {
    const [respostaDespesas, respostaCategorias] = await Promise.all([
      api.get('/despesas'),
      api.get('/categorias'),
    ])
    despesas.value = respostaDespesas.data.expenses.map((d) => ({
      ...d,
      category_name: d.categoryName,
      expense_category_id: d.expenseCategoryId,
    }))
    categorias.value = respostaCategorias.data.categories
  } finally {
    carregando.value = false
  }
}

function abrirFormularioCriar() {
  despesa_editando.value = null
  formulario.description = ''
  formulario.valor_mascarado = ''
  formulario.date = new Date().toISOString().slice(0, 10)
  formulario.expense_category_id = categorias.value[0]?.id || ''
  exibir_formulario.value = true
}

function abrirFormularioEditar(despesa) {
  despesa_editando.value = despesa
  formulario.description = despesa.description
  formulario.valor_mascarado = formatarNumeroComoMoeda(despesa.amount)
  formulario.date = despesa.date
  formulario.expense_category_id = despesa.expense_category_id
  exibir_formulario.value = true
}

function fecharFormulario() {
  exibir_formulario.value = false
  despesa_editando.value = null
}

async function salvarDespesa() {
  mensagem_erro.value = ''
  mensagem_sucesso.value = ''
  Object.keys(erros).forEach((chave) => delete erros[chave])

  const payload = {
    description: formulario.description,
    amount: converterMascaraMoedaParaNumero(formulario.valor_mascarado),
    date: formulario.date,
    expenseCategoryId: Number(formulario.expense_category_id),
  }

  try {
    if (despesa_editando.value) {
      await api.put(`/despesas/${despesa_editando.value.id}`, payload)
      mensagem_sucesso.value = 'Despesa atualizada com sucesso.'
    } else {
      await api.post('/despesas', payload)
      mensagem_sucesso.value = 'Despesa criada com sucesso.'
    }

    fecharFormulario()
    await carregarDados()
  } catch (erro) {
    Object.assign(erros, obterErrosValidacao(erro))
    mensagem_erro.value = obterMensagemErro(erro)
  }
}

async function excluirDespesa(despesa) {
  if (!confirm(`Excluir a despesa "${despesa.description}"?`)) return

  try {
    await api.delete(`/despesas/${despesa.id}`)
    mensagem_sucesso.value = 'Despesa excluída com sucesso.'
    await carregarDados()
  } catch (erro) {
    mensagem_erro.value = obterMensagemErro(erro)
  }
}

onMounted(carregarDados)
</script>
