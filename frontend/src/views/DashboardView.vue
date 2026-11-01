<template>
  <div class="space-y-5 sm:space-y-6">
    <!-- Top Asymmetric Bento Hero Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-5">
      <!-- Left: Humanist Greeting & Daily Story (7 cols on xl) -->
      <div class="xl:col-span-7 bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-2xs surface-card-hover flex flex-col justify-between relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-teal-500/8 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-16 -bottom-16 w-40 h-40 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="space-y-2.5 sm:space-y-3 relative z-10">
          <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-stone-100 text-stone-700 text-[11px] sm:text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-teal-600 shrink-0"></span>
            <span class="truncate">Ringkasan Hari Ini • {{ formattedToday }}</span>
          </div>

          <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-stone-900 tracking-tight leading-tight">
            {{ greetingText }}, {{ authStore.user?.name || 'Petugas' }}
          </h2>

          <p class="text-xs sm:text-sm text-stone-600 leading-relaxed max-w-2xl">
            Pantau aktivitas peminjaman dan pengembalian laptop peserta didik dengan tenang. Sistem akan membantu mengingatkan wali murid jika ada laptop yang belum dikembalikan selepas pukul 15.00.
          </p>
        </div>

        <!-- Balanced Action Bar (2x2 Grid on Mobile, Inline Flex on Desktop) -->
        <div class="pt-5 mt-5 border-t border-stone-100 grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 sm:gap-2.5 relative z-10">
          <RouterLink
            to="/pickup"
            class="cursor-pointer px-3.5 py-2.5 rounded-xl sm:rounded-2xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold shadow-xs transition-all flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Scan Ambil</span>
          </RouterLink>

          <RouterLink
            to="/return"
            class="cursor-pointer px-3.5 py-2.5 rounded-xl sm:rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200/80 text-xs font-bold transition-all flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-1.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Scan Kembali</span>
          </RouterLink>

          <button
            type="button"
            @click="refreshData"
            class="cursor-pointer px-3.5 py-2.5 rounded-xl sm:rounded-2xl bg-stone-100 hover:bg-stone-200/80 text-stone-700 text-xs font-semibold transition-all flex items-center justify-center"
          >
            <svg class="w-3.5 h-3.5 mr-1.5 text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Segarkan</span>
          </button>

          <button
            type="button"
            @click="triggerOverdueCheck"
            :disabled="checkingOverdue"
            class="cursor-pointer px-3.5 py-2.5 rounded-xl sm:rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200/90 text-xs font-bold transition-all flex items-center justify-center disabled:opacity-50"
          >
            <svg class="w-4 h-4 mr-1.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="truncate">{{ checkingOverdue ? 'Memeriksa...' : 'Cek Overdue WA' }}</span>
          </button>
        </div>
      </div>

      <!-- Right: Daily Rhythm Timeline Card (5 cols on xl) -->
      <div class="xl:col-span-5 bg-gradient-to-br from-stone-900 via-stone-800 to-teal-950 text-white rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-sm surface-card-hover flex flex-col justify-between">
        <div class="flex items-center justify-between gap-2">
          <div>
            <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-amber-300/90">Ritme Harian Sekolah</p>
            <h3 class="text-base sm:text-lg font-bold text-white mt-0.5">Alur Penggunaan Laptop</h3>
          </div>
          <span class="px-3 py-1 rounded-full bg-white/10 text-xs font-mono text-stone-200 shrink-0">
            {{ stats.total_students || 0 }} Siswa Aktif
          </span>
        </div>

        <div class="my-4 sm:my-5 space-y-2.5">
          <div class="flex items-center justify-between bg-white/8 border border-white/10 rounded-xl sm:rounded-2xl px-3.5 py-2.5">
            <div class="flex items-center space-x-2.5">
              <span class="w-2 h-2 rounded-full bg-teal-400 shrink-0"></span>
              <span class="text-xs font-semibold text-stone-100">Pengambilan Pagi</span>
            </div>
            <span class="text-[11px] sm:text-xs font-mono text-stone-300">07.30 – 09.00 WIB</span>
          </div>

          <div class="flex items-center justify-between bg-white/8 border border-white/10 rounded-xl sm:rounded-2xl px-3.5 py-2.5">
            <div class="flex items-center space-x-2.5">
              <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
              <span class="text-xs font-semibold text-stone-100">Pengembalian Siang</span>
            </div>
            <span class="text-[11px] sm:text-xs font-mono text-stone-300">13.00 – 15.00 WIB</span>
          </div>

          <div class="flex items-center justify-between bg-white/8 border border-white/10 rounded-xl sm:rounded-2xl px-3.5 py-2.5">
            <div class="flex items-center space-x-2.5">
              <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
              <span class="text-xs font-semibold text-stone-100">Notifikasi Wali Murid</span>
            </div>
            <span class="text-[11px] sm:text-xs font-mono text-stone-300">Otomatis > 15.00 WIB</span>
          </div>
        </div>

        <div class="flex items-center justify-between text-xs text-stone-300 pt-2.5 border-t border-white/10">
          <span>Belum mendata laptop siswa?</span>
          <RouterLink to="/form-laptop" class="cursor-pointer font-bold text-amber-300 hover:text-amber-200 underline underline-offset-4">
            Buka Form Siswa →
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- 4 Bento KPI Cards (2x2 on Mobile & Tablet, 4 cols on Desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Taken -->
      <div class="bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-2xs surface-card-hover flex flex-col justify-between">
        <div class="flex items-start justify-between gap-2">
          <span class="text-[11px] sm:text-xs font-bold text-stone-500 leading-snug">Sudah Mengambil</span>
          <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl sm:rounded-2xl bg-teal-50 text-teal-700 border border-teal-200/60 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </span>
        </div>
        <div class="mt-3">
          <h3 class="text-2xl sm:text-3xl font-extrabold text-stone-900 font-mono">{{ stats.total_taken }}</h3>
          <p class="text-[11px] sm:text-xs text-stone-500 mt-1 line-clamp-1">Siswa ambil pagi ini</p>
        </div>
      </div>

      <!-- Returned -->
      <div class="bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-2xs surface-card-hover flex flex-col justify-between">
        <div class="flex items-start justify-between gap-2">
          <span class="text-[11px] sm:text-xs font-bold text-stone-500 leading-snug">Sudah Kembali</span>
          <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </span>
        </div>
        <div class="mt-3">
          <h3 class="text-2xl sm:text-3xl font-extrabold text-stone-900 font-mono">{{ stats.total_returned }}</h3>
          <p class="text-[11px] sm:text-xs text-stone-500 mt-1 line-clamp-1">Laptop telah kembali</p>
        </div>
      </div>

      <!-- Still Borrowed -->
      <div class="bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-2xs surface-card-hover flex flex-col justify-between">
        <div class="flex items-start justify-between gap-2">
          <span class="text-[11px] sm:text-xs font-bold text-stone-500 leading-snug">Masih Dipakai</span>
          <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </span>
        </div>
        <div class="mt-3">
          <h3 class="text-2xl sm:text-3xl font-extrabold text-stone-900 font-mono">{{ stats.total_borrowed }}</h3>
          <p class="text-[11px] sm:text-xs text-stone-500 mt-1 line-clamp-1">Menunggu dikembalikan</p>
        </div>
      </div>

      <!-- Return Progress -->
      <div class="bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-2xs surface-card-hover flex flex-col justify-between">
        <div class="flex items-start justify-between gap-1.5">
          <span class="text-[11px] sm:text-xs font-bold text-stone-500 leading-snug">Pengembalian</span>
          <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-stone-100 text-stone-700 shrink-0">
            {{ stats.total_notifications_sent }} WA
          </span>
        </div>
        <div class="mt-3 space-y-2">
          <h3 class="text-2xl sm:text-3xl font-extrabold text-stone-900 font-mono">{{ stats.percentage_returned }}%</h3>
          <div class="w-full h-1.5 sm:h-2 bg-stone-100 rounded-full overflow-hidden">
            <div
              class="h-full bg-gradient-to-r from-teal-600 to-emerald-500 rounded-full transition-all duration-500"
              :style="{ width: `${Math.min(100, stats.percentage_returned || 0)}%` }"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Active / Overdue Loans Card -->
    <div class="bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-2xs space-y-4 sm:space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
          <h3 class="text-base sm:text-lg font-extrabold text-stone-900">Pantauan Siswa yang Membawa Laptop</h3>
          <p class="text-xs text-stone-500 mt-0.5">
            Daftar peserta didik yang saat ini masih memegang laptop atau belum melakukan scan pengembalian
          </p>
        </div>
        <span
          class="self-start sm:self-auto px-3 py-1 rounded-full text-xs font-bold border shrink-0"
          :class="(stats.overdue_list?.length || 0) > 0
            ? 'bg-amber-50 text-amber-900 border-amber-200/80'
            : 'bg-emerald-50 text-emerald-800 border-emerald-200/80'"
        >
          {{ stats.overdue_list?.length || 0 }} Siswa Membawa Laptop
        </span>
      </div>

      <Transition name="fade-slide" mode="out-in">
        <!-- Calm Humanist Empty State -->
        <div
          v-if="!stats.overdue_list || stats.overdue_list.length === 0"
          key="empty"
          class="bg-[#FAF8F5] border border-stone-200/70 rounded-2xl p-6 sm:p-8 text-center space-y-2"
        >
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200/70 flex items-center justify-center mx-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h4 class="text-sm font-bold text-stone-900">Semua Laptop Terkendali</h4>
          <p class="text-xs text-stone-500 max-w-md mx-auto leading-relaxed">
            Saat ini tidak ada siswa yang terlambat atau belum mengembalikan laptop. Gunakan menu <strong>Scan Ambil Laptop</strong> saat sesi pagi dimulai.
          </p>
        </div>

        <div v-else key="populated">
          <!-- Mobile Card List (< sm) -->
          <div class="sm:hidden space-y-3">
            <div
              v-for="loan in stats.overdue_list"
              :key="loan.id"
              class="p-4 rounded-2xl bg-[#FAF8F5] border border-stone-200/80 space-y-2.5"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="flex items-center space-x-2.5">
                  <div class="w-8 h-8 rounded-xl bg-teal-700 text-white font-bold text-xs flex items-center justify-center shrink-0">
                    {{ loan.student?.name?.charAt(0) || 'S' }}
                  </div>
                  <div>
                    <p class="font-bold text-stone-900 text-sm">{{ loan.student?.name }}</p>
                    <p class="text-[11px] font-mono text-stone-500">NIS: {{ loan.student?.nis }} • {{ loan.student?.class_name || '-' }}</p>
                  </div>
                </div>
                <StatusBadge :status="loan.status" />
              </div>
              <div class="pt-2 border-t border-stone-200/70 grid grid-cols-2 gap-2 text-xs">
                <div>
                  <p class="text-[10px] text-stone-400 uppercase font-bold">Laptop</p>
                  <p class="font-semibold text-stone-800">{{ loan.laptop ? `${loan.laptop.brand || ''} ${loan.laptop.model || ''}` : '-' }}</p>
                </div>
                <div>
                  <p class="text-[10px] text-stone-400 uppercase font-bold">Wali Murid</p>
                  <p class="font-semibold text-stone-800 truncate">{{ loan.student?.parent_name || '-' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Tablet & Desktop Table (sm+) -->
          <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-sm text-stone-700">
              <thead class="bg-[#FAF8F5] text-[11px] font-bold text-stone-500 uppercase tracking-wider border-y border-stone-200/80">
                <tr>
                  <th class="px-4 py-3.5">Peserta Didik</th>
                  <th class="px-4 py-3.5">Kelas</th>
                  <th class="px-4 py-3.5">Perangkat Laptop</th>
                  <th class="px-4 py-3.5">Waktu Diambil</th>
                  <th class="px-4 py-3.5">Wali Murid & WhatsApp</th>
                  <th class="px-4 py-3.5">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-stone-100">
                <tr v-for="loan in stats.overdue_list" :key="loan.id" class="hover:bg-[#FAF8F5]/80 transition-colors">
                  <td class="px-4 py-3.5">
                    <div class="flex items-center space-x-3">
                      <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/60 font-bold text-xs flex items-center justify-center shrink-0">
                        {{ loan.student?.name?.charAt(0) || 'S' }}
                      </div>
                      <div>
                        <p class="font-bold text-stone-900">{{ loan.student?.name }}</p>
                        <p class="text-xs font-mono text-stone-500">NIS: {{ loan.student?.nis }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3.5 font-semibold text-stone-700 text-xs">{{ loan.student?.class_name || '-' }}</td>
                  <td class="px-4 py-3.5 text-xs">
                    <template v-if="loan.laptop">
                      <p class="font-bold text-stone-800">{{ loan.laptop.brand || '-' }}{{ loan.laptop.model ? ' ' + loan.laptop.model : '' }}</p>
                      <p class="font-mono text-stone-500 text-[11px]">{{ loan.laptop.asset_code }}</p>
                    </template>
                    <span v-else class="text-stone-400">-</span>
                  </td>
                  <td class="px-4 py-3.5 text-xs font-mono text-stone-600">{{ formatDate(loan.taken_at) }}</td>
                  <td class="px-4 py-3.5 text-xs">
                    <p class="text-stone-800 font-semibold">{{ loan.student?.parent_name || '-' }}</p>
                    <p class="text-stone-500 font-mono">{{ loan.student?.parent_whatsapp || '-' }}</p>
                  </td>
                  <td class="px-4 py-3.5">
                    <StatusBadge :status="loan.status" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useMonitoringStore } from '@/stores/monitoring'
import { useAuthStore } from '@/stores/auth'
import StatusBadge from '@/components/common/StatusBadge.vue'

const monitoringStore = useMonitoringStore()
const authStore = useAuthStore()
const checkingOverdue = ref(false)

const stats = computed(() => monitoringStore.stats)

const greetingText = computed(() => {
  const hour = new Date().getHours()
  if (hour < 11) return 'Selamat Pagi'
  if (hour < 15) return 'Selamat Siang'
  if (hour < 18) return 'Selamat Sore'
  return 'Selamat Malam'
})

const formattedToday = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
})

const refreshData = async () => {
  await monitoringStore.fetchStats()
}

const triggerOverdueCheck = async () => {
  checkingOverdue.value = true
  try {
    const res = await monitoringStore.triggerCheckOverdue()
    alert(res.message || 'Pemeriksaan keterlambatan selesai.')
  } catch (err) {
    alert('Gagal mengeksekusi pemeriksaan overdue.')
  } finally {
    checkingOverdue.value = false
  }
}

const formatDate = (isoStr) => {
  if (!isoStr) return '-'
  const d = new Date(isoStr)
  return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
}

onMounted(() => {
  refreshData()
})
</script>
