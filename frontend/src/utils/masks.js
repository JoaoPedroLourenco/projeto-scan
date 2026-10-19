export function formatCpf(value) {
  let cpf = value.replace(/\D/g, '').slice(0, 11)

  cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2')
  cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2')
  cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2')

  return cpf
}

export function formatPhone(value) {
  let phone = value.replace(/\D/g, '').slice(0, 11)

  if (phone.length <= 2) {
    return phone.length ? `(${phone}` : ''
  }

  phone = phone.replace(/^(\d{2})(\d)/, '($1) $2')

  if (phone.replace(/\D/g, '').length === 11) {
    phone = phone.replace(/(\d{5})(\d{4})$/, '$1-$2')
  } else {
    phone = phone.replace(/(\d{4})(\d{4})$/, '$1-$2')
  }

  return phone
}
