import api from './api'

export function registerUser(userInfo) {
  return api.post('/auth/register', userInfo)
}

export async function getCurrentUser() {
  const response = await api.get('/auth/me')

  return response.data.user ?? respose.data
}

export async function logout() {
  try {
    await api.post('/auth/logout')
  } catch (error) {
    console.error(error.message)
  } finally {
    //finaliza a sessão mesmo que tenha retornado erro na requisição
    //isso faz com que o usuário não fique preso ao sistema
    sessionStorage.removeItem('token')
  }
}
