<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
      <div>
        <h2 class="text-xl font-bold text-slate-900">Log Notifikasi WhatsApp Wali Murid</h2>
        <p class="text-xs text-slate-500 mt-1">Riwayat pengiriman pesan peringatan keterlambatan pengembalian laptop</p>
      </div>

      <div class="flex items-center space-x-3">
        <select v-model="filterStatus" @change="fetchNotifications" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 text-xs focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all font-medium">
          <option value="">Semua Status</option>
          <option value="SENT">Terkirim (SENT)</option>
          <option value="FAILED">Gagal (FAILED)</option>
          <option value="PENDING">Menunggu (PENDING)</option>
        </select>
        <button @click="fetchNotifications" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition-all shadow-xs">Refresh</button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700">
          <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
            <tr>
              <th class="px-4 py-3">Waktu Kirim</th>
              <th class="px-4 py-3">Nama Siswa</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Wali Murid & No. WA</th>
              <th class="px-4 py-3">Status WA</th>
              <th class="px-4 py-3">Pesan / Error</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="notifications.length === 0">
              <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs font-medium">
                Belum ada log notifikasi WhatsApp.
              </td>
            </tr>
            <tr v-for="notif in notifications" :key="notif.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 text-xs text-slate-500 font-mono font-medium">{{ formatDate(notif.sent_at || notif.created_at) }}</td>
              <td class="px-4 py-3 font-bold text-slate-900">{{ notif.student?.name }}</td>
              <td class="px-4 py-3 text-slate-600">{{ notif.student?.classRoom?.name || '-' }}</td>
              <td class="px-4 py-3 text-xs">
                <p class="text-slate-800 font-semibold">{{ notif.parent?.name }}</p>
                <p class="text-indigo-600 font-mono font-semibold">{{ notif.parent?.whatsapp_number }}</p>
              </td>
              <td class="px-4 py-3">
                <StatusBadge :status="notif.status" />
              </td>
              <td class="px-4 py-3 text-xs text-slate-500 font-medium max-w-xs truncate">
                {{ notif.error_message || 'Pesan peringatan keterlambatan pengembalian laptop' }}
              </td>
              <td class="px-4 py-3 text-right">
                <button
                  v-if="notif.status === 'FAILED'"
                  @click="retryNotification(notif.id)"
                  class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl text-xs font-semibold hover:bg-amber-100 transition-all shadow-xs"
                >
                  Kirim Ulang
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import StatusBadge from '@/components/common/StatusBadge.vue'

const notifications = ref([])
const filterStatus = ref('')

const fetchNotifications = async () => {
  const params = filterStatus.value ? { status: filterStatus.value } : {}
  const res = await api.get('/notifications', { params })
  notifications.value = res.data.data
}

const retryNotification = async (id) => {
  try {
    await api.post(`/notifications/${id}/retry`)
    alert('Notifikasi berhasil dikirim ulang.')
    fetchNotifications()
  } catch (err) {
    alert('Gagal mengirim ulang notifikasi.')
  }
}

const formatDate = (isoStr) => {
  if (!isoStr) return '-'
  return new Date(isoStr).toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
}

onMounted(() => fetchNotifications())
</script>
