<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Pengaturan Waktu</span>
        <h2 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight mt-0.5">Jadwal Sesi Harian</h2>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">Atur rentang waktu sesi pengambilan pagi dan pengembalian siang</p>
      </div>

      <button
        @click="openModal()"
        class="px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-2xl shadow-sm shadow-teal-900/15 transition-all flex items-center cursor-pointer"
      >
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Buat Jadwal Sesi
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-stone-700">
          <thead class="bg-[#FAF8F5] text-[11px] font-bold text-stone-500 uppercase tracking-wider border-y border-stone-200/80">
            <tr>
              <th class="px-4 py-3.5">Jenis Sesi</th>
              <th class="px-4 py-3.5">Target Kelas</th>
              <th class="px-4 py-3.5">Waktu Mulai</th>
              <th class="px-4 py-3.5">Waktu Selesai</th>
              <th class="px-4 py-3.5">Status</th>
              <th class="px-4 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100">
            <tr v-for="sch in schedules" :key="sch.id" class="hover:bg-[#FAF8F5]/80 transition-colors">
              <td class="px-4 py-3.5 font-bold text-xs" :class="sch.type === 'pickup' ? 'text-teal-800' : 'text-emerald-800'">
                {{ sch.type === 'pickup' ? 'Pengambilan (Pagi)' : 'Pengembalian (Siang)' }}
              </td>
              <td class="px-4 py-3.5 text-stone-600 text-xs font-semibold">{{ sch.class?.name || 'Semua Kelas' }}</td>
              <td class="px-4 py-3.5 text-xs font-mono text-stone-600 font-medium">{{ formatDate(sch.start_at) }}</td>
              <td class="px-4 py-3.5 text-xs font-mono text-stone-600 font-medium">{{ formatDate(sch.end_at) }}</td>
              <td class="px-4 py-3.5">
                <StatusBadge :status="sch.status" />
              </td>
              <td class="px-4 py-3.5 text-right space-x-2">
                <button @click="deleteSchedule(sch.id)" class="px-3 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold hover:bg-rose-100 transition-all cursor-pointer">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4" @click.self="showModal = false">
        <div class="modal-box bg-white border border-stone-200 rounded-[2rem] p-6 sm:p-7 max-w-md w-full space-y-5 shadow-2xl">
          <h3 class="text-lg font-extrabold text-stone-900">Buat Jadwal Sesi Baru</h3>
          <form @submit.prevent="saveSchedule" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Jenis Sesi</label>
              <select v-model="form.type" class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-medium cursor-pointer">
                <option value="pickup">Pengambilan Laptop (Pagi)</option>
                <option value="return">Pengembalian Laptop (Siang)</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Waktu Mulai</label>
              <input v-model="form.start_at" type="datetime-local" required class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-medium cursor-pointer" />
            </div>
            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Waktu Selesai</label>
              <input v-model="form.end_at" type="datetime-local" required class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-medium cursor-pointer" />
            </div>
            <div class="flex items-center justify-end space-x-2.5 pt-4 border-t border-stone-100">
              <button type="button" @click="showModal = false" class="px-4 py-2.5 bg-[#FAF8F5] text-stone-700 text-xs font-semibold rounded-2xl border border-stone-200 hover:bg-stone-100 transition-all cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-2xl shadow-sm transition-all cursor-pointer">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
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
