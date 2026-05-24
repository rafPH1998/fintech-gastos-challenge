<template>
  <router-view
    v-slot="{ Component }"
    @autenticado="atualizarUsuario"
    @sair="realizarLogout"
  >
    <component
      :is="Component"
      :usuario="usuario_logado"
      @autenticado="atualizarUsuario"
      @sair="realizarLogout"
    />
  </router-view>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api, { configurarTokenAutenticacao } from './servicos/api'

const router = useRouter()
const usuario_logado = ref(null)

function atualizarUsuario(usuario) {
  usuario_logado.value = usuario
}

async function realizarLogout() {
  try {
    await api.post('/sair')
  } catch {
    // ignora erro de logout remoto
  }

  localStorage.removeItem('token_acesso')
  localStorage.removeItem('usuario_logado')
  configurarTokenAutenticacao(null)
  usuario_logado.value = null
  router.push('/login')
}

onMounted(() => {
  const usuarioSalvo = localStorage.getItem('usuario_logado')
  if (usuarioSalvo) {
    usuario_logado.value = JSON.parse(usuarioSalvo)
  }
})
</script>
