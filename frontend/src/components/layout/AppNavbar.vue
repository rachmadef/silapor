<template>
  <header class="sticky top-0 z-30 bg-[#FAF8F5]/90 backdrop-blur-xl border-b border-stone-200/80 px-3.5 py-3 sm:px-6 lg:px-8">
    <div class="max-w-[1600px] mx-auto flex items-center justify-between gap-2 sm:gap-4">
      <!-- Left: Mobile/Tablet Hamburger + Brand Identity -->
      <div class="flex items-center space-x-2.5 sm:space-x-3.5 min-w-0">
        <button
          type="button"
          @click="$emit('toggle-sidebar')"
          class="cursor-pointer lg:hidden p-2 rounded-xl bg-white border border-stone-200/90 text-stone-700 hover:bg-stone-100 transition-all shrink-0"
          aria-label="Buka Menu"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <RouterLink to="/dashboard" class="cursor-pointer flex items-center space-x-2.5 sm:space-x-3 group min-w-0">
          <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-br from-teal-700 via-teal-600 to-emerald-600 flex items-center justify-center shadow-sm shadow-teal-900/10 shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="truncate">
            <div class="flex items-center space-x-2">
              <h1 class="text-sm sm:text-base font-extrabold text-stone-900 tracking-tight leading-none">SILAPOR</h1>
              <span class="hidden sm:inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-teal-50 text-teal-800 border border-teal-200/70">
                Sekolah
              </span>
            </div>
            <p class="text-[11px] sm:text-xs text-stone-500 font-medium mt-0.5 truncate">Monitoring Laptop Siswa</p>
          </div>
        </RouterLink>
      </div>

      <!-- Center: Live Clock & Session Pill (Visible from md upwards) -->
      <div class="hidden md:flex items-center space-x-2.5 lg:space-x-3 bg-white border border-stone-200/90 rounded-full px-3.5 py-1.5 text-xs shadow-2xs shrink-0">
        <span class="hidden xl:inline text-stone-500 font-medium">{{ currentDate }}</span>
        <span class="hidden xl:inline text-stone-300">•</span>
        <span class="flex items-center text-stone-800 font-mono font-semibold">
          <svg class="w-3.5 h-3.5 mr-1.5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ currentTime }}
        </span>
        <span class="text-stone-300">|</span>
        <span :class="sessionBadge.color" class="font-semibold flex items-center">
          <span class="w-2 h-2 rounded-full mr-1.5 animate-pulse bg-current"></span>
          {{ sessionBadge.text }}
        </span>
      </div>

      <!-- Right: User Profile Pill & Logout -->
      <div class="flex items-center space-x-2 shrink-0">
        <!-- Compact Time Badge on Mobile (< md) -->
        <div class="flex md:hidden items-center bg-white border border-stone-200/90 rounded-xl px-2.5 py-1.5 text-[11px] font-mono font-semibold text-stone-700">
          <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-teal-600 animate-pulse"></span>
          {{ shortTime }}
        </div>

        <div class="flex items-center space-x-2.5 bg-white border border-stone-200/90 rounded-xl sm:rounded-2xl p-1 sm:pl-2 sm:pr-3.5 sm:py-1.5 shadow-2xs">
          <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-amber-100/80 border border-amber-200/80 text-amber-900 font-bold text-xs flex items-center justify-center">
            {{ userInitials }}
          </div>
          <div class="hidden sm:block text-left">
            <p class="text-xs font-bold text-stone-900 leading-tight">{{ authStore.user?.name || 'Petugas' }}</p>
            <p class="text-[11px] text-stone-500 capitalize font-medium leading-tight">{{ authStore.user?.role === 'admin' ? 'Administrator' : 'Petugas Lab' }}</p>
          </div>
        </div>

        <button
          type="button"
          @click="handleLogout"
          class="cursor-pointer p-2 sm:p-2.5 rounded-xl sm:rounded-2xl bg-white hover:bg-rose-50 text-stone-500 hover:text-rose-700 border border-stone-200/90 hover:border-rose-200 transition-all shadow-2xs"
          title="Keluar dari sesi"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

defineEmits(['toggle-sidebar'])

const authStore = useAuthStore()
const router = useRouter()

const currentTime = ref('')
const shortTime = ref('')
const currentDate = ref('')
let timer = null

const updateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB'
  shortTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
  currentDate.value = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(() => {
  updateTime()
  timer = setInterval(updateTime, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

const userInitials = computed(() => {
  const name = authStore.user?.name || 'Petugas'
  return name
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
})

const sessionBadge = computed(() => {
  const now = new Date()
  const hours = now.getHours() + now.getMinutes() / 60

  if (hours >= 7.5 && hours < 9.0) {
    return { text: 'Sesi Ambil Pagi (07.30 - 09.00)', color: 'text-teal-700' }
  } else if (hours >= 13.0 && hours < 15.0) {
    return { text: 'Sesi Kembali Siang (13.00 - 15.00)', color: 'text-emerald-700' }
  } else if (hours >= 15.0) {
    return { text: 'Pemeriksaan Batas Waktu (> 15.00)', color: 'text-rose-700' }
  } else {
    return { text: 'Jam Belajar & Penggunaan Laptop', color: 'text-amber-700' }
  }
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
