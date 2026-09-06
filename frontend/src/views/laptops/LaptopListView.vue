<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
      <div>
        <h2 class="text-xl font-bold text-slate-900">Master Data Aset Laptop</h2>
        <p class="text-xs text-slate-500 mt-1">Kelola data inventaris & status laptop sekolah</p>
      </div>

      <button
        @click="openModal()"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all flex items-center"
      >
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Laptop
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700">
          <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
            <tr>
              <th class="px-4 py-3">Kode Aset</th>
              <th class="px-4 py-3">Serial Number</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="laptop in laptops" :key="laptop.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 font-mono text-indigo-600 font-bold text-xs">{{ laptop.asset_code }}</td>
              <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ laptop.serial_number || '-' }}</td>
              <td class="px-4 py-3">
                <StatusBadge :status="laptop.status" />
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <button @click="openModal(laptop)" class="px-2.5 py-1 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold hover:bg-slate-200 transition-all shadow-xs">Edit</button>
                <button @click="deleteLaptop(laptop.id)" class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold hover:bg-rose-100 transition-all shadow-xs">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white border border-slate-200 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900">{{ editingId ? 'Edit Laptop' : 'Tambah Laptop Baru' }}</h3>
        <form @submit.prevent="saveLaptop" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kode Aset Laptop</label>
            <input v-model="form.asset_code" type="text" required placeholder="e.g. LAP-001" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 font-mono text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Serial Number</label>
            <input v-model="form.serial_number" type="text" placeholder="e.g. SN-LENOVO-1001" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 font-mono text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Aset</label>
            <select v-model="form.status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all font-medium">
              <option value="available">Tersedia (Available)</option>
              <option value="borrowed">Sedang Dipinjam (Borrowed)</option>
              <option value="maintenance">Perbaikan (Maintenance)</option>
            </select>
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

const laptops = ref([])
const showModal = ref(false)
const editingId = ref(null)
const form = ref({ asset_code: '', serial_number: '', status: 'available' })

const fetchLaptops = async () => {
  const res = await api.get('/laptops')
  laptops.value = res.data.data
}

const openModal = (laptop = null) => {
  if (laptop) {
    editingId.value = laptop.id
    form.value = { asset_code: laptop.asset_code, serial_number: laptop.serial_number, status: laptop.status }
  } else {
    editingId.value = null
    form.value = { asset_code: '', serial_number: '', status: 'available' }
  }
  showModal.value = true
}

const saveLaptop = async () => {
  if (editingId.value) {
    await api.put(`/laptops/${editingId.value}`, form.value)
  } else {
    await api.post('/laptops', form.value)
  }
  showModal.value = false
  fetchLaptops()
}

const deleteLaptop = async (id) => {
  if (!confirm('Hapus data laptop ini?')) return
  await api.delete(`/laptops/${id}`)
  fetchLaptops()
}

onMounted(() => fetchLaptops())
</script>
