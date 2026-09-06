<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50/70 via-slate-50 to-cyan-50/70 flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Ambient Glow Background Effects -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-cyan-200/40 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl p-8 shadow-xl backdrop-blur-xl relative z-10">
      <!-- Header Logo -->
      <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-indigo-500/20">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">SILAPOR</h2>
        <p class="text-sm text-slate-500 mt-1">Sistem Monitoring Laptop Siswa Berbasis QR Code</p>
      </div>

      <!-- Error Alert -->
      <div v-if="error" class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-start space-x-3">
        <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ error }}</span>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-5">
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Email Petugas</label>
          <div class="relative">
            <input
              v-model="email"
              type="email"
              required
              placeholder="admin@silapor.com"
              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Password</label>
          <div class="relative">
            <input
              v-model="password"
              type="password"
              required
              placeholder="••••••••"
              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all"
            />
          </div>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-700 hover:to-cyan-700 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Memproses Login...' : 'Masuk ke Sistem' }}
        </button>
      </form>

      <!-- Quick Credentials Hint -->
      <div class="mt-8 pt-6 border-t border-slate-200 text-center text-xs text-slate-500 space-y-1.5">
        <p class="font-medium text-slate-600">Gunakan kredensial pengujian:</p>
        <p><code class="bg-slate-100 text-indigo-700 px-2 py-0.5 rounded border border-slate-200 font-mono">admin@silapor.com</code> atau <code class="bg-slate-100 text-indigo-700 px-2 py-0.5 rounded border border-slate-200 font-mono">petugas@silapor.com</code></p>
        <p>Password: <code class="bg-slate-100 text-indigo-700 px-2 py-0.5 rounded border border-slate-200 font-mono">password</code></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const email = ref('admin@silapor.com')
const password = ref('password')
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    await authStore.login({
      email: email.value,
      password: password.value,
    })
    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'Email atau password salah.'
  } finally {
    loading.value = false
  }
}
</script>
