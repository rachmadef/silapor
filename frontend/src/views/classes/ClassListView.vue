<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Struktur Akademik</span>
        <h2 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight mt-0.5">Daftar Kelas</h2>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">Kelola rombongan belajar dan tahun ajaran aktif</p>
      </div>

      <button
        @click="openModal()"
        class="px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-2xl shadow-sm shadow-teal-900/15 transition-all flex items-center cursor-pointer"
      >
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Kelas
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-stone-700">
          <thead class="bg-[#FAF8F5] text-[11px] font-bold text-stone-500 uppercase tracking-wider border-y border-stone-200/80">
            <tr>
              <th class="px-4 py-3.5">Nama Kelas</th>
              <th class="px-4 py-3.5">Tingkat</th>
              <th class="px-4 py-3.5">Tahun Akademik</th>
              <th class="px-4 py-3.5">Jumlah Siswa</th>
              <th class="px-4 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100">
            <tr v-for="cls in classes" :key="cls.id" class="hover:bg-[#FAF8F5]/80 transition-colors">
              <td class="px-4 py-3.5 font-bold text-stone-900">{{ cls.name }}</td>
              <td class="px-4 py-3.5">
                <span class="px-2.5 py-1 rounded-xl bg-teal-50 text-teal-800 font-mono font-bold text-xs">
                  Kelas {{ cls.grade }}
                </span>
              </td>
              <td class="px-4 py-3.5 text-stone-600 font-medium text-xs">{{ cls.academic_year }}</td>
              <td class="px-4 py-3.5 font-mono text-xs text-stone-600 font-semibold">{{ cls.students_count || 0 }} Siswa</td>
              <td class="px-4 py-3.5 text-right space-x-2">
                <button @click="openModal(cls)" class="px-3 py-1.5 bg-[#FAF8F5] text-stone-700 border border-stone-200 rounded-xl text-xs font-semibold hover:bg-stone-100 transition-all cursor-pointer">Edit</button>
                <button @click="deleteClass(cls.id)" class="px-3 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold hover:bg-rose-100 transition-all cursor-pointer">Hapus</button>
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
          <h3 class="text-lg font-extrabold text-stone-900">{{ editingId ? 'Perbarui Kelas' : 'Tambah Kelas Baru' }}</h3>
          <form @submit.prevent="saveClass" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Nama Kelas</label>
              <input v-model="form.name" type="text" required placeholder="Contoh: X RPL 1" class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all" />
            </div>
            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Tingkat (Grade)</label>
              <input v-model="form.grade" type="text" required placeholder="Contoh: X, XI, XII" class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all" />
            </div>
            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Tahun Akademik</label>
              <input v-model="form.academic_year" type="text" required placeholder="Contoh: 2025/2026" class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all" />
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

const classes = ref([])
const showModal = ref(false)
const editingId = ref(null)
const form = ref({ name: '', grade: '', academic_year: '2025/2026' })

const fetchClasses = async () => {
  const res = await api.get('/classes')
  classes.value = res.data.data
}

const openModal = (cls = null) => {
  if (cls) {
    editingId.value = cls.id
    form.value = { name: cls.name, grade: cls.grade, academic_year: cls.academic_year }
  } else {
    editingId.value = null
    form.value = { name: '', grade: '', academic_year: '2025/2026' }
  }
  showModal.value = true
}

const saveClass = async () => {
  if (editingId.value) {
    await api.put(`/classes/${editingId.value}`, form.value)
  } else {
    await api.post('/classes', form.value)
  }
  showModal.value = false
  fetchClasses()
}

const deleteClass = async (id) => {
  if (!confirm('Hapus data kelas ini?')) return
  await api.delete(`/classes/${id}`)
  fetchClasses()
}

onMounted(() => fetchClasses())
</script>
