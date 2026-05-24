import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    Accept: 'application/json',
  },
})

export function configurarTokenAutenticacao(token) {
  if (token) {
    api.defaults.headers.common.Authorization = `Bearer ${token}`
  } else {
    delete api.defaults.headers.common.Authorization
  }
}

export function obterErrosValidacao(erro) {
  if (erro.response?.status === 422) {
    return erro.response.data.errors || {}
  }

  return {}
}

export function obterMensagemErro(erro) {
  if (erro.response?.data?.message) {
    return erro.response.data.message
  }

  if (erro.response?.data?.mensagem) {
    return erro.response.data.mensagem
  }

  return 'Ocorreu um erro. Tente novamente.'
}

export default api
