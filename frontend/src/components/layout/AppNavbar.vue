<template>
  <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-200 px-4 py-3 sm:px-6 shadow-xs">
    <div class="flex items-center justify-between">
      <!-- Left: Logo & App Title -->
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center shadow-md shadow-indigo-500/15">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <div>
          <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">SILAPOR</h1>
          <p class="text-xs text-indigo-600 font-semibold">Monitoring Laptop Siswa</p>
        </div>
      </div>

      <!-- Center: Time & Active Session Status -->
      <div class="hidden md:flex items-center space-x-4 bg-slate-100/80 border border-slate-200 rounded-full px-4 py-1.5 text-xs shadow-inner">
        <span class="flex items-center text-slate-700 font-medium">
          <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ currentTime }}
        </span>
        <span class="text-slate-300">|</span>
        <span :class="sessionBadge.color" class="font-semibold flex items-center">
          <span class="w-2 h-2 rounded-full mr-1.5 animate-ping bg-current"></span>
          {{ sessionBadge.text }}
        </span>
      </div>

      <!-- Right: User Profile & Logout -->
      <div class="flex items-center space-x-3">
        <div class="text-right hidden sm:block">
          <p class="text-sm font-bold text-slate-800">{{ authStore.user?.name || 'Petugas' }}</p>
          <p class="text-xs text-slate-500 capitalize font-medium">{{ authStore.user?.role || 'Officer' }}</p>
        </div>
        <button
          @click="handleLogout"
          class="p-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-200 transition-colors shadow-xs"
          title="Keluar / Logout"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const currentTime = ref('')
let timer = null

const updateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB'
}

onMounted(() => {
  updateTime()
  timer = setInterval(updateTime, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

const sessionBadge = computed(() => {
  const now = new Date()
  const hours = now.getHours() + now.getMinutes() / 60

  if (hours >= 7.5 && hours < 9.0) {
    return { text: 'Sesi Pengambilan (07.30 - 09.00)', color: 'text-indigo-600' }
  } else if (hours >= 13.0 && hours < 15.0) {
    return { text: 'Sesi Pengembalian (13.00 - 15.00)', color: 'text-emerald-600' }
  } else if (hours >= 15.0) {
    return { text: 'Batas Pengembalian Berakhir (> 15.00)', color: 'text-rose-600' }
  } else {
    return { text: 'Sesi Penggunaan Laptop', color: 'text-amber-600' }
  }
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
