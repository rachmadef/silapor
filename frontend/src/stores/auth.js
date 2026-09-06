import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/auth/login', credentials)
        this.token = response.data.token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal login. Periksa email dan password.'
        throw err
      } finally {
        this.loading = false
      }
    },

    async fetchUser() {
      if (!this.token) return null
      try {
        const response = await api.get('/auth/me')
        this.user = response.data.user
        localStorage.setItem('user', JSON.stringify(this.user))
        return this.user
      } catch (err) {
        this.logout()
      }
    },

    async logout() {
      try {
        if (this.token) {
          await api.post('/auth/logout')
        }
      } catch (err) {
        // ignore logout errors
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
      }
    },
  },
})
