<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Komunikasi Wali Murid</span>
        <h2 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight mt-0.5">Riwayat Notifikasi WhatsApp</h2>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">Catatan pengiriman pesan pengingat kepada wali murid bagi siswa yang belum mengembalikan laptop</p>
      </div>

      <div class="flex items-center space-x-2.5">
        <select v-model="filterStatus" @change="fetchNotifications" class="bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-xs focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-semibold cursor-pointer">
          <option value="">Semua Status</option>
          <option value="SENT">Terkirim</option>
          <option value="FAILED">Gagal Kirim</option>
          <option value="PENDING">Menunggu</option>
        </select>
        <button @click="fetchNotifications" class="px-4 py-2.5 bg-[#FAF8F5] hover:bg-stone-100 text-stone-700 text-xs font-semibold rounded-2xl border border-stone-200 transition-all cursor-pointer">Segarkan</button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-stone-700">
          <thead class="bg-[#FAF8F5] text-[11px] font-bold text-stone-500 uppercase tracking-wider border-y border-stone-200/80">
            <tr>
              <th class="px-4 py-3.5">Waktu Kirim</th>
              <th class="px-4 py-3.5">Peserta Didik</th>
              <th class="px-4 py-3.5">Kelas</th>
              <th class="px-4 py-3.5">Wali Murid & WhatsApp</th>
              <th class="px-4 py-3.5">Status</th>
              <th class="px-4 py-3.5">Keterangan</th>
              <th class="px-4 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100">
            <tr v-if="notifications.length === 0">
              <td colspan="7" class="px-4 py-10 text-center text-stone-400 text-xs font-medium">
                Belum ada riwayat pengiriman pesan WhatsApp.
              </td>
            </tr>
            <tr v-for="notif in notifications" :key="notif.id" class="hover:bg-[#FAF8F5]/80 transition-colors">
              <td class="px-4 py-3.5 text-xs text-stone-500 font-mono font-medium">{{ formatDate(notif.sent_at || notif.created_at) }}</td>
              <td class="px-4 py-3.5 font-bold text-stone-900">{{ notif.student?.name }}</td>
              <td class="px-4 py-3.5 text-stone-600 text-xs font-semibold">{{ notif.student?.classRoom?.name || '-' }}</td>
              <td class="px-4 py-3.5 text-xs">
                <p class="text-stone-800 font-semibold">{{ notif.parent?.name }}</p>
                <p class="text-teal-700 font-mono font-semibold">{{ notif.parent?.whatsapp_number }}</p>
              </td>
              <td class="px-4 py-3.5">
                <StatusBadge :status="notif.status" />
              </td>
              <td class="px-4 py-3.5 text-xs text-stone-500 font-medium max-w-xs truncate">
                {{ notif.error_message || 'Pengingat pengembalian laptop siswa' }}
              </td>
              <td class="px-4 py-3.5 text-right">
                <button
                  v-if="notif.status === 'FAILED'"
                  @click="retryNotification(notif.id)"
                  class="px-3 py-1.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-bold hover:bg-amber-100 transition-all cursor-pointer"
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
