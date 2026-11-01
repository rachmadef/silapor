<template>
  <!-- Mobile & Tablet Animated Backdrop (< lg) -->
  <Transition name="drawer-backdrop">
    <div
      v-if="mobileOpen"
      @click="$emit('close')"
      class="cursor-pointer fixed inset-0 bg-stone-900/35 backdrop-blur-xs z-40 lg:hidden"
    ></div>
  </Transition>

  <!-- Mobile & Tablet Animated Slide-Over Drawer (< lg) -->
  <Transition name="drawer-slide">
    <aside
      v-if="mobileOpen"
      class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-[#FAF8F5] border-r border-stone-200 shadow-2xl p-4 overflow-y-auto flex flex-col justify-between lg:hidden"
    >
      <div class="space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-stone-200/80">
          <div class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-xl bg-teal-700 flex items-center justify-center text-white font-extrabold text-xs">
              S
            </div>
            <div>
              <p class="text-sm font-extrabold text-stone-900 leading-none">Menu Navigasi</p>
              <p class="text-[11px] text-stone-500 mt-0.5">SILAPOR Sekolah</p>
            </div>
          </div>
          <button
            type="button"
            @click="$emit('close')"
            class="cursor-pointer p-1.5 rounded-xl bg-white border border-stone-200 text-stone-500 hover:text-stone-800"
            aria-label="Tutup Menu"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Quick Scanner Section -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-2.5 shadow-2xs space-y-1">
          <p class="px-2.5 pt-1 pb-0.5 text-[10px] font-bold text-stone-400 uppercase tracking-wider">Aktivitas Harian</p>
          <RouterLink
            to="/pickup"
            class="cursor-pointer flex items-center px-3 py-2.5 rounded-xl text-xs font-bold transition-all"
            :class="isActive('/pickup')
              ? 'bg-teal-700 text-white shadow-xs'
              : 'text-stone-700 hover:bg-stone-100/80'"
          >
            <span
              class="w-7 h-7 rounded-lg flex items-center justify-center mr-2.5 shrink-0"
              :class="isActive('/pickup') ? 'bg-white/20 text-white' : 'bg-teal-50 text-teal-700'"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
              </svg>
            </span>
            <div class="min-w-0">
              <p class="leading-tight truncate">Scan Ambil Laptop</p>
              <p class="text-[10px] font-normal opacity-75">Sesi Pagi • 07.30–09.00</p>
            </div>
          </RouterLink>

          <RouterLink
            to="/return"
            class="cursor-pointer flex items-center px-3 py-2.5 rounded-xl text-xs font-bold transition-all"
            :class="isActive('/return')
              ? 'bg-emerald-700 text-white shadow-xs'
              : 'text-stone-700 hover:bg-stone-100/80'"
          >
            <span
              class="w-7 h-7 rounded-lg flex items-center justify-center mr-2.5 shrink-0"
              :class="isActive('/return') ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700'"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            <div class="min-w-0">
              <p class="leading-tight truncate">Scan Kembalikan</p>
              <p class="text-[10px] font-normal opacity-75">Sesi Siang • 13.00–15.00</p>
            </div>
          </RouterLink>
        </div>

        <!-- Pemantauan & Laporan -->
        <div>
          <h3 class="px-3 text-[10px] font-bold text-stone-400 uppercase tracking-wider mb-1.5">Pemantauan</h3>
          <nav class="space-y-1">
            <RouterLink
              v-for="item in monitoringLinks"
              :key="item.to"
              :to="item.to"
              class="cursor-pointer flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition-all"
              :class="isActive(item.to)
                ? 'bg-white text-teal-800 border border-stone-200/90 shadow-2xs font-bold'
                : 'text-stone-600 hover:bg-stone-200/50 hover:text-stone-900'"
            >
              <span
                class="w-1.5 h-1.5 rounded-full mr-2.5 shrink-0 transition-all"
                :class="isActive(item.to) ? 'bg-teal-600 scale-125' : 'bg-stone-300'"
              ></span>
              <span class="truncate">{{ item.label }}</span>
            </RouterLink>
          </nav>
        </div>

        <!-- Data Referensi Sekolah -->
        <div>
          <h3 class="px-3 text-[10px] font-bold text-stone-400 uppercase tracking-wider mb-1.5">Data Sekolah</h3>
          <nav class="space-y-1">
            <RouterLink
              v-for="item in masterLinks"
              :key="item.to"
              :to="item.to"
              class="cursor-pointer flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition-all"
              :class="isActive(item.to)
                ? 'bg-white text-teal-800 border border-stone-200/90 shadow-2xs font-bold'
                : 'text-stone-600 hover:bg-stone-200/50 hover:text-stone-900'"
            >
              <span
                class="w-1.5 h-1.5 rounded-full mr-2.5 shrink-0 transition-all"
                :class="isActive(item.to) ? 'bg-amber-600 scale-125' : 'bg-stone-300'"
              ></span>
              <span class="truncate">{{ item.label }}</span>
            </RouterLink>
          </nav>
        </div>
      </div>

      <div class="mt-6 mb-2 bg-gradient-to-br from-amber-50/90 via-orange-50/60 to-stone-50 border border-amber-200/70 rounded-2xl p-3.5 space-y-2 shrink-0">
        <div class="flex items-center space-x-2">
          <span class="w-6 h-6 rounded-lg bg-amber-500/15 text-amber-800 flex items-center justify-center font-bold text-xs">
            ✦
          </span>
          <p class="text-xs font-bold text-stone-900">Form Mandiri Siswa</p>
        </div>
        <p class="text-[11px] text-stone-600 leading-relaxed">
          Tautan pengisian merk & tipe laptop peserta didik.
        </p>
        <RouterLink
          to="/form-laptop"
          class="cursor-pointer w-full py-2 px-3 rounded-xl bg-white hover:bg-amber-50 text-amber-900 border border-amber-200/90 text-xs font-bold flex items-center justify-center transition-all shadow-2xs"
        >
          Buka Form Siswa →
        </RouterLink>
      </div>
    </aside>
  </Transition>

  <!-- Desktop Sticky Sidebar (lg+) -->
  <aside class="hidden lg:flex lg:w-64 shrink-0 flex-col justify-between lg:sticky lg:top-[65px] lg:h-[calc(100vh-65px)] lg:overflow-y-auto border-r border-stone-200/80 p-5">
    <div class="space-y-5">
      <!-- Quick Scanner Section -->
      <div class="bg-white border border-stone-200/80 rounded-2xl p-2.5 shadow-2xs space-y-1">
        <p class="px-2.5 pt-1 pb-0.5 text-[10px] font-bold text-stone-400 uppercase tracking-wider">Aktivitas Harian</p>
        <RouterLink
          to="/pickup"
          class="cursor-pointer flex items-center px-3 py-2.5 rounded-xl text-xs font-bold transition-all"
          :class="isActive('/pickup')
            ? 'bg-teal-700 text-white shadow-xs'
            : 'text-stone-700 hover:bg-stone-100/80'"
        >
          <span
            class="w-7 h-7 rounded-lg flex items-center justify-center mr-2.5 shrink-0"
            :class="isActive('/pickup') ? 'bg-white/20 text-white' : 'bg-teal-50 text-teal-700'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
            </svg>
          </span>
          <div class="min-w-0">
            <p class="leading-tight truncate">Scan Ambil Laptop</p>
            <p class="text-[10px] font-normal opacity-75">Sesi Pagi • 07.30–09.00</p>
          </div>
        </RouterLink>

        <RouterLink
          to="/return"
          class="cursor-pointer flex items-center px-3 py-2.5 rounded-xl text-xs font-bold transition-all"
          :class="isActive('/return')
            ? 'bg-emerald-700 text-white shadow-xs'
            : 'text-stone-700 hover:bg-stone-100/80'"
        >
          <span
            class="w-7 h-7 rounded-lg flex items-center justify-center mr-2.5 shrink-0"
            :class="isActive('/return') ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7" />
            </svg>
          </span>
          <div class="min-w-0">
            <p class="leading-tight truncate">Scan Kembalikan</p>
            <p class="text-[10px] font-normal opacity-75">Sesi Siang • 13.00–15.00</p>
          </div>
        </RouterLink>
      </div>

      <!-- Pemantauan & Laporan -->
      <div>
        <h3 class="px-3 text-[10px] font-bold text-stone-400 uppercase tracking-wider mb-1.5">Pemantauan</h3>
        <nav class="space-y-1">
          <RouterLink
            v-for="item in monitoringLinks"
            :key="item.to"
            :to="item.to"
            class="cursor-pointer flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition-all"
            :class="isActive(item.to)
              ? 'bg-white text-teal-800 border border-stone-200/90 shadow-2xs font-bold'
              : 'text-stone-600 hover:bg-stone-200/50 hover:text-stone-900'"
          >
            <span
              class="w-1.5 h-1.5 rounded-full mr-2.5 shrink-0 transition-all"
              :class="isActive(item.to) ? 'bg-teal-600 scale-125' : 'bg-stone-300'"
            ></span>
            <span class="truncate">{{ item.label }}</span>
          </RouterLink>
        </nav>
      </div>

      <!-- Data Referensi Sekolah -->
      <div>
        <h3 class="px-3 text-[10px] font-bold text-stone-400 uppercase tracking-wider mb-1.5">Data Sekolah</h3>
        <nav class="space-y-1">
          <RouterLink
            v-for="item in masterLinks"
            :key="item.to"
            :to="item.to"
            class="cursor-pointer flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition-all"
            :class="isActive(item.to)
              ? 'bg-white text-teal-800 border border-stone-200/90 shadow-2xs font-bold'
              : 'text-stone-600 hover:bg-stone-200/50 hover:text-stone-900'"
          >
            <span
              class="w-1.5 h-1.5 rounded-full mr-2.5 shrink-0 transition-all"
              :class="isActive(item.to) ? 'bg-amber-600 scale-125' : 'bg-stone-300'"
            ></span>
            <span class="truncate">{{ item.label }}</span>
          </RouterLink>
        </nav>
      </div>
    </div>

    <!-- Bottom Humanist Card: Form Siswa Shortcut -->
    <div class="mt-6 mb-2 bg-gradient-to-br from-amber-50/90 via-orange-50/60 to-stone-50 border border-amber-200/70 rounded-2xl p-3.5 space-y-2 shrink-0">
      <div class="flex items-center space-x-2">
        <span class="w-6 h-6 rounded-lg bg-amber-500/15 text-amber-800 flex items-center justify-center font-bold text-xs">
          ✦
        </span>
        <p class="text-xs font-bold text-stone-900">Form Mandiri Siswa</p>
      </div>
      <p class="text-[11px] text-stone-600 leading-relaxed">
        Tautan pengisian merk & tipe laptop peserta didik.
      </p>
      <RouterLink
        to="/form-laptop"
        class="cursor-pointer w-full py-2 px-3 rounded-xl bg-white hover:bg-amber-50 text-amber-900 border border-amber-200/90 text-xs font-bold flex items-center justify-center transition-all shadow-2xs"
      >
        Buka Form Siswa →
      </RouterLink>
    </div>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router'

defineProps({
  mobileOpen: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['close'])

const route = useRoute()
const isActive = (path) => route.path === path

const monitoringLinks = [
  { to: '/dashboard', label: 'Beranda Ringkasan' },
  { to: '/monitoring', label: 'Pantauan Real-time' },
  { to: '/notifications', label: 'Riwayat Pesan WA' },
]

const masterLinks = [
  { to: '/students', label: 'Peserta Didik & QR' },
  { to: '/laptops', label: 'Data Laptop Siswa' },
  { to: '/classes', label: 'Daftar Kelas' },
  { to: '/parents', label: 'Kontak Wali Murid' },
  { to: '/schedules', label: 'Jadwal Sesi' },
]
</script>
