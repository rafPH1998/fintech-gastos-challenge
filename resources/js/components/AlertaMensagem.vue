<template>
  <div
    v-if="texto"
    class="mb-4 rounded-xl px-4 py-3 text-sm"
    :class="tipo === 'sucesso' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
  >
    {{ texto }}
  </div>
</template>

<script setup>
defineProps({
  texto: { type: String, default: '' },
  tipo: { type: String, default: 'sucesso' },
})
</script>
