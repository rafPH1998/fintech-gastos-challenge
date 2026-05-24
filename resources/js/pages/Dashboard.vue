<template>
  <LayoutPrincipal @sair="$emit('sair')">
    <h2 class="mb-6 text-2xl font-bold text-slate-800">Olá, {{ nome_usuario }} 👋</h2>

    <div v-if="carregando" class="text-slate-500">Carregando resumo...</div>

    <template v-else>
      <div class="mb-8 rounded-2xl bg-blue-600 p-6 text-white shadow-lg">
        <p class="text-sm text-blue-100">Total gasto no mês</p>
        <p class="text-4xl font-bold">{{ formatarMoeda(resumo.total_gasto_mes) }}</p>
      </div>

      <div class="grid gap-6 md:grid-cols-2">
        <section class="rounded-2xl bg-white p-6 shadow-md">
          <h3 class="mb-4 text-lg font-semibold text-slate-800">Últimas despesas</h3>
          <ul v-if="resumo.ultimas_despesas.length" class="space-y-3">
            <li
              v-for="despesa in resumo.ultimas_despesas"
              :key="despesa.id"
              class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3"
            >
              <div>
                <p class="font-medium text-slate-800">{{ despesa.description }}</p>
                <p class="text-xs text-slate-500">{{ despesa.category_name }} · {{ formatarData(despesa.date) }}</p>
              </div>
              <span class="font-semibold text-rose-600">{{ formatarMoeda(despesa.amount) }}</span>
            </li>
          </ul>
          <p v-else class="text-sm text-slate-500">Nenhuma despesa registrada ainda.</p>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-md">
          <h3 class="mb-4 text-lg font-semibold text-slate-800">Gastos por categoria (mês)</h3>
          <ul v-if="resumo.gastos_por_categoria.length" class="space-y-3">
            <li
              v-for="item in resumo.gastos_por_categoria"
              :key="item.category_name"
              class="rounded-xl bg-blue-50 px-4 py-3"
            >
              <div class="mb-1 flex justify-between text-sm">
                <span class="font-medium text-slate-700">{{ item.category_name }}</span>
                <span class="font-semibold text-blue-700">{{ formatarMoeda(item.total) }}</span>
              </div>
              <div class="h-2 overflow-hidden rounded-full bg-blue-100">
                <div
                  class="h-full rounded-full bg-blue-500"
                  :style="{ width: calcularLarguraBarra(item.total) }"
                />
              </div>
            </li>
          </ul>
          <p v-else class="text-sm text-slate-500">Sem gastos neste mês.</p>
        </section>
      </div>
    </template>
  </LayoutPrincipal>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import api from '../servicos/api'
import LayoutPrincipal from '../layouts/LayoutPrincipal.vue'

defineEmits(['sair'])

const props = defineProps({
  usuario: { type: Object, default: null },
})

const carregando = ref(true)
const resumo = reactive({
  total_gasto_mes: 0,
  ultimas_despesas: [],
  gastos_por_categoria: [],
})

const nome_usuario = computed(() => props.usuario?.nome || 'usuário')

function formatarMoeda(valor) {
  return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function formatarData(data) {
  const [ano, mes, dia] = data.split('-')
  return `${dia}/${mes}/${ano}`
}

function calcularLarguraBarra(total) {
  if (!resumo.total_gasto_mes) return '0%'
  const percentual = Math.min(100, (total / resumo.total_gasto_mes) * 100)
  return `${percentual}%`
}

async function carregarResumo() {
  carregando.value = true
  try {
    const { data } = await api.get('/dashboard')
    resumo.total_gasto_mes = data.totalMonthAmount
    resumo.ultimas_despesas = data.latestExpenses
    resumo.gastos_por_categoria = data.amountByCategory.map((item) => ({
      category_name: item.categoryName,
      total: item.total,
    }))
  } finally {
    carregando.value = false
  }
}

onMounted(carregarResumo)
</script>
