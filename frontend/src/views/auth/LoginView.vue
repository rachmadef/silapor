<template>
  <div class="min-h-screen bg-[#FAF8F5] flex items-center justify-center p-4 sm:p-6 lg:p-10 relative overflow-hidden">
    <!-- Warm Organic Ambient Shapes -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-teal-500/8 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-12 bg-white border border-stone-200/90 rounded-[2rem] shadow-xl shadow-stone-900/5 overflow-hidden relative z-10">
      <!-- Left Editorial / Humanist Welcome Panel (5 cols) -->
      <div class="lg:col-span-5 bg-gradient-to-br from-teal-800 via-teal-700 to-stone-900 text-white p-7 sm:p-10 flex flex-col justify-between relative overflow-hidden">
        <div class="absolute -right-16 -bottom-16 w-56 h-56 bg-amber-400/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="space-y-6 relative z-10">
          <div class="inline-flex items-center space-x-2.5 bg-white/12 backdrop-blur-xs border border-white/15 px-3.5 py-1.5 rounded-full">
            <span class="w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="text-xs font-bold tracking-wide text-stone-100">SILAPOR • Sekolah Digital</span>
          </div>

          <div class="space-y-3">
            <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight tracking-tight">
              Pendampingan Penggunaan Laptop Siswa yang Lebih Tenang & Teratur.
            </h1>
            <p class="text-xs sm:text-sm text-stone-200/90 leading-relaxed">
              Pencatatan QR Code cepat saat pengambilan pagi dan pengembalian siang, terhubung langsung dengan informasi wali murid.
            </p>
          </div>
        </div>

        <!-- Student Self-Service Card Inside Left Panel -->
        <div class="mt-8 pt-6 border-t border-white/15 relative z-10 space-y-3">
          <p class="text-[11px] font-bold uppercase tracking-wider text-amber-300">Khusus Peserta Didik</p>
          <RouterLink
            to="/form-laptop"
            class="cursor-pointer block p-4 rounded-2xl bg-white/10 hover:bg-white/18 border border-white/15 transition-all group"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-bold text-white">Form Pendataan Laptop</p>
                <p class="text-xs text-stone-200 mt-0.5">Isi nama, merk & tipe laptop Anda di sini</p>
              </div>
              <span class="w-8 h-8 rounded-xl bg-amber-400 text-stone-950 font-bold flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                →
              </span>
            </div>
          </RouterLink>
        </div>
      </div>

      <!-- Right Login Form Panel (7 cols) -->
      <div class="lg:col-span-7 p-7 sm:p-10 lg:p-12 flex flex-col justify-center">
        <div class="mb-7">
          <h2 class="text-2xl font-extrabold text-stone-900 tracking-tight">Selamat Datang Kembali</h2>
          <p class="text-xs sm:text-sm text-stone-500 mt-1">Masuk menggunakan akun petugas atau administrator sekolah</p>
        </div>

        <!-- Animated Error Alert -->
        <Transition name="fade-slide">
          <div v-if="error" class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start space-x-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ error }}</span>
          </div>
        </Transition>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label class="block text-xs font-bold text-stone-700 mb-2">Alamat Email Petugas</label>
            <input
              v-model="email"
              type="email"
              required
              placeholder="nama@silapor.com"
              class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-4 py-3 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white focus:ring-3 focus:ring-teal-700/10 transition-all font-medium"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-stone-700 mb-2">Kata Sandi</label>
            <input
              v-model="password"
              type="password"
              required
              placeholder="••••••••"
              class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-4 py-3 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white focus:ring-3 focus:ring-teal-700/10 transition-all font-medium"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="cursor-pointer w-full py-3.5 px-5 rounded-2xl bg-teal-700 hover:bg-teal-800 text-white font-bold text-sm shadow-md shadow-teal-900/15 transition-all disabled:opacity-50 flex items-center justify-center"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Memverifikasi Akun...' : 'Masuk ke Ruang Kerja' }}
          </button>
        </form>

        <!-- One-Click Demo Role Switcher -->
        <div class="mt-8 pt-6 border-t border-stone-100">
          <p class="text-[11px] font-bold text-stone-400 uppercase tracking-wider mb-2.5">Akses Cepat Pengujian</p>
          <div class="grid grid-cols-2 gap-2.5">
            <button
              type="button"
              @click="fillCredentials('admin@silapor.com')"
              class="cursor-pointer p-2.5 rounded-xl bg-[#FAF8F5] hover:bg-stone-100 border border-stone-200/80 text-left transition-all"
            >
              <p class="text-xs font-bold text-stone-800">Administrator</p>
              <p class="text-[11px] font-mono text-stone-500 truncate">admin@silapor.com</p>
            </button>
            <button
              type="button"
              @click="fillCredentials('petugas@silapor.com')"
              class="cursor-pointer p-2.5 rounded-xl bg-[#FAF8F5] hover:bg-stone-100 border border-stone-200/80 text-left transition-all"
            >
              <p class="text-xs font-bold text-stone-800">Petugas Lab</p>
              <p class="text-[11px] font-mono text-stone-500 truncate">petugas@silapor.com</p>
            </button>
          </div>
        </div>
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

const fillCredentials = (selectedEmail) => {
  email.value = selectedEmail
  password.value = 'password'
}

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
    error.value = err.response?.data?.message || 'Email atau kata sandi tidak sesuai.'
  } finally {
    loading.value = false
  }
}
</script>
