<template>
  <div class="min-h-screen">
    <header class="border-b border-slate-200 bg-white shadow-sm">
      <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
        <router-link
          to="/dashboard"
          class="flex items-center gap-2 text-xl font-bold text-blue-700"
        >
          <Icone nome="dinheiro" classe="h-6 w-6" />
          Meus Gastos
        </router-link>

        <nav class="flex items-center gap-2 text-sm">
          <router-link
            v-for="item in itens_menu"
            :key="item.rota"
            :to="item.rota"
            class="flex items-center gap-1.5 rounded-lg px-3 py-2 transition hover:bg-blue-50"
            active-class="bg-blue-100 font-semibold text-blue-800"
          >
            <Icone :nome="item.icone" classe="h-4 w-4" />
            {{ item.rotulo }}
          </router-link>

          <button
            type="button"
            class="ml-2 flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-2 text-slate-700 transition hover:bg-slate-200"
            @click="$emit('sair')"
          >
            <Icone nome="sair" classe="h-4 w-4" />
            Sair
          </button>
        </nav>
      </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
import Icone from '../components/Icone.vue'

defineEmits(['sair'])

const itens_menu = [
  { rota: '/dashboard', rotulo: 'Dashboard', icone: 'dashboard' },
  { rota: '/categorias', rotulo: 'Categorias', icone: 'categorias' },
  { rota: '/despesas', rotulo: 'Despesas', icone: 'despesas' },
]
</script>
