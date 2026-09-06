<template>
  <div class="space-y-6">
    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
      <div>
        <h2 class="text-xl font-bold text-slate-900">Dashboard Monitoring Laptop</h2>
        <p class="text-sm text-slate-500 mt-1">Ringkasan status pengambilan & pengembalian laptop hari ini</p>
      </div>
      <div class="flex items-center space-x-3">
        <button
          @click="refreshData"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold border border-slate-200 transition-colors flex items-center shadow-xs"
        >
          <svg class="w-4 h-4 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Refresh Data
        </button>
        <button
          @click="triggerOverdueCheck"
          :disabled="checkingOverdue"
          class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold shadow-sm shadow-rose-600/20 transition-all flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          Cek Overdue & WA (Manual)
        </button>
      </div>
    </div>

    <!-- Indicator Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white border border-indigo-100 rounded-2xl p-5 shadow-xs relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl pointer-events-none"></div>
        <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Sudah mengambil</p>
        <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ stats.total_taken }}</h3>
        <p class="text-xs text-slate-500 mt-2">Siswa mengambil laptop pagi ini</p>
      </div>

      <div class="bg-white border border-emerald-100 rounded-2xl p-5 shadow-xs relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
        <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Sudah mengembalikan</p>
        <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ stats.total_returned }}</h3>
        <p class="text-xs text-slate-500 mt-2">Siswa telah mengembalikan laptop</p>
      </div>

      <div class="bg-white border border-amber-100 rounded-2xl p-5 shadow-xs relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-500/10 rounded-full blur-xl pointer-events-none"></div>
        <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Belum mengembalikan</p>
        <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ stats.total_borrowed }}</h3>
        <p class="text-xs text-slate-500 mt-2">Siswa masih membawa laptop</p>
      </div>

      <div class="bg-white border border-rose-100 rounded-2xl p-5 shadow-xs relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rose-500/10 rounded-full blur-xl pointer-events-none"></div>
        <p class="text-xs font-bold text-rose-600 uppercase tracking-wider">Persentase Pengembalian</p>
        <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ stats.percentage_returned }}%</h3>
        <p class="text-xs text-slate-500 mt-2">{{ stats.total_notifications_sent }} Notifikasi WA Terkirim</p>
      </div>
    </div>

    <!-- Overdue Students Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold text-slate-900">Daftar Siswa Belum Mengembalikan Laptop</h3>
          <p class="text-xs text-slate-500">Daftar siswa yang perlu dipantau atau dikirimkan notifikasi WhatsApp</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
          {{ stats.overdue_list?.length || 0 }} Siswa
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700">
          <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
            <tr>
              <th class="px-4 py-3">NIS</th>
              <th class="px-4 py-3">Nama Siswa</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Waktu Diambil</th>
              <th class="px-4 py-3">Wali Murid & No. WA</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="!stats.overdue_list || stats.overdue_list.length === 0">
              <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs font-medium">
                Tidak ada siswa yang membawa/terlambat mengembalikan laptop saat ini.
              </td>
            </tr>
            <tr v-for="loan in stats.overdue_list" :key="loan.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 font-mono text-indigo-600 font-semibold text-xs">{{ loan.student?.nis }}</td>
              <td class="px-4 py-3 font-bold text-slate-900">{{ loan.student?.name }}</td>
              <td class="px-4 py-3 text-slate-600">{{ loan.student?.class_name || '-' }}</td>
              <td class="px-4 py-3 text-xs text-slate-500 font-medium">{{ formatDate(loan.taken_at) }}</td>
              <td class="px-4 py-3 text-xs">
                <p class="text-slate-800 font-semibold">{{ loan.student?.parent_name || '-' }}</p>
                <p class="text-slate-500 font-mono">{{ loan.student?.parent_whatsapp || '-' }}</p>
              </td>
              <td class="px-4 py-3">
                <StatusBadge :status="loan.status" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useMonitoringStore } from '@/stores/monitoring'
import StatusBadge from '@/components/common/StatusBadge.vue'

const monitoringStore = useMonitoringStore()
const checkingOverdue = ref(false)

const stats = computed(() => monitoringStore.stats)

const refreshData = async () => {
  await monitoringStore.fetchStats()
}

const triggerOverdueCheck = async () => {
  checkingOverdue.value = true
  try {
    const res = await monitoringStore.triggerCheckOverdue()
    alert(res.message || 'Pemeriksaan overdue selesai.')
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
