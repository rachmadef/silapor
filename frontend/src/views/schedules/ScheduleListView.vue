<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
      <div>
        <h2 class="text-xl font-bold text-slate-900">Manajemen Sesi Jadwal</h2>
        <p class="text-xs text-slate-500 mt-1">Pengaturan jadwal waktu sesi pengambilan (pagi) & pengembalian (siang)</p>
      </div>

      <button
        @click="openModal()"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all flex items-center"
      >
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Buat Jadwal Sesi
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700">
          <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
            <tr>
              <th class="px-4 py-3">Tipe Sesi</th>
              <th class="px-4 py-3">Target Kelas</th>
              <th class="px-4 py-3">Waktu Mulai</th>
              <th class="px-4 py-3">Waktu Selesai</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="sch in schedules" :key="sch.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 font-bold uppercase text-xs" :class="sch.type === 'pickup' ? 'text-indigo-600' : 'text-emerald-600'">
                {{ sch.type === 'pickup' ? 'Pengambilan (Pagi)' : 'Pengembalian (Siang)' }}
              </td>
              <td class="px-4 py-3 text-slate-600">{{ sch.class?.name || 'Semua Kelas' }}</td>
              <td class="px-4 py-3 text-xs font-mono text-slate-600 font-medium">{{ formatDate(sch.start_at) }}</td>
              <td class="px-4 py-3 text-xs font-mono text-slate-600 font-medium">{{ formatDate(sch.end_at) }}</td>
              <td class="px-4 py-3">
                <StatusBadge :status="sch.status" />
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <button @click="deleteSchedule(sch.id)" class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold hover:bg-rose-100 transition-all shadow-xs">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white border border-slate-200 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900">Buat Sesi Jadwal Baru</h3>
        <form @submit.prevent="saveSchedule" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tipe Sesi</label>
            <select v-model="form.type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all font-medium">
              <option value="pickup">Pengambilan Laptop (Pagi)</option>
              <option value="return">Pengembalian Laptop (Siang)</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Waktu Mulai</label>
            <input v-model="form.start_at" type="datetime-local" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all font-medium" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Waktu Selesai</label>
            <input v-model="form.end_at" type="datetime-local" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all font-medium" />
          </div>
          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-200 transition-all">Batal</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import StatusBadge from '@/components/common/StatusBadge.vue'

const schedules = ref([])
const showModal = ref(false)
const form = ref({ type: 'pickup', start_at: '', end_at: '' })

const fetchSchedules = async () => {
  const res = await api.get('/schedules')
  schedules.value = res.data.data
}

const openModal = () => {
  const now = new Date()
  const todayStr = now.toISOString().split('T')[0]
  form.value = {
    type: 'pickup',
    start_at: `${todayStr}T07:30`,
    end_at: `${todayStr}T09:00`,
  }
  showModal.value = true
}

const saveSchedule = async () => {
  await api.post('/schedules', form.value)
  showModal.value = false
  fetchSchedules()
}

const deleteSchedule = async (id) => {
  if (!confirm('Hapus jadwal ini?')) return
  await api.delete(`/schedules/${id}`)
  fetchSchedules()
}

const formatDate = (isoStr) => {
  if (!isoStr) return '-'
  return new Date(isoStr).toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
}

onMounted(() => fetchSchedules())
</script>
