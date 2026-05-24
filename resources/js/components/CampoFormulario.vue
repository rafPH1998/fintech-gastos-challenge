<template>
  <div class="mb-4">
    <label class="mb-1 block text-sm font-medium text-slate-700">{{ rotulo }}</label>
    <slot />
    <p v-if="erro" class="mt-1 text-sm text-rose-600">{{ erro }}</p>
  </div>
</template>

<script setup>
defineProps({
  rotulo: { type: String, required: true },
  erro: { type: String, default: '' },
})
</script>
