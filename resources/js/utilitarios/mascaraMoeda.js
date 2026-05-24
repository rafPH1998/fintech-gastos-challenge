export function aplicarMascaraMoeda(valorDigitado) {
  const apenasNumeros = String(valorDigitado).replace(/\D/g, '')

  if (!apenasNumeros) {
    return ''
  }

  const valorEmCentavos = Number(apenasNumeros)
  const valorDecimal = valorEmCentavos / 100

  return valorDecimal.toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  })
}

export function converterMascaraMoedaParaNumero(valorMascarado) {
  if (!valorMascarado) {
    return 0
  }

  const apenasNumeros = String(valorMascarado).replace(/\D/g, '')

  return Number(apenasNumeros) / 100
}

export function formatarNumeroComoMoeda(valorNumerico) {
  if (!valorNumerico && valorNumerico !== 0) {
    return ''
  }

  return Number(valorNumerico).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  })
}
