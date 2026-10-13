import api from './api'

export function registerUser(userInfo) {
  return api.post('/auth/register', userInfo)
}
