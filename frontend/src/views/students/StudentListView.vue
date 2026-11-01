<template>
  <div class="space-y-5 sm:space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-2xs surface-card-hover">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Direktori Siswa</span>
        <h2 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight mt-0.5">Data Peserta Didik & Kartu QR</h2>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">Kelola identitas siswa, relasi wali murid, serta cetak kartu QR Code peminjaman</p>
      </div>

      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
        <div class="relative flex-1 sm:w-60">
          <input
            v-model="search"
            type="text"
            placeholder="Cari NIS atau nama..."
            @input="fetchStudents"
            class="w-full bg-[#FAF8F5] border border-stone-200 rounded-xl sm:rounded-2xl pl-9 pr-3.5 py-2.5 text-stone-900 text-xs focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-medium"
          />
          <svg class="w-4 h-4 text-stone-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <button
          type="button"
          @click="openModal()"
          class="cursor-pointer px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-xl sm:rounded-2xl shadow-xs transition-all flex items-center justify-center shrink-0"
        >
          <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Tambah Siswa
        </button>
      </div>
    </div>

    <!-- Students Container -->
    <div class="bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-2xs space-y-4">
      <Transition name="fade-slide" mode="out-in">
        <div v-if="students.length === 0" key="empty" class="py-10 text-center text-stone-400 text-xs font-medium">
          Tidak ada data peserta didik yang ditemukan.
        </div>

        <div v-else key="list">
          <!-- Mobile Card List (< sm) -->
          <div class="sm:hidden space-y-3">
            <div
              v-for="student in students"
              :key="student.id"
              class="p-4 rounded-2xl bg-[#FAF8F5] border border-stone-200/80 space-y-3"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="flex items-center space-x-2.5">
                  <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/60 font-bold text-xs flex items-center justify-center shrink-0">
                    {{ student.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-bold text-stone-900 text-sm">{{ student.name }}</p>
                    <p class="text-[11px] font-mono text-stone-500">NIS: {{ student.nis }} • {{ student.class?.name || '-' }}</p>
                  </div>
                </div>
                <span class="px-2 py-0.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-mono text-[10px] font-bold">
                  {{ student.qr_code?.token || '-' }}
                </span>
              </div>

              <div class="pt-2.5 border-t border-stone-200/70 flex items-center justify-between text-xs">
                <div class="truncate pr-2">
                  <p class="text-[10px] text-stone-400 uppercase font-bold">Wali Murid</p>
                  <p class="font-semibold text-stone-700 truncate">{{ student.parent?.name || '-' }}</p>
                </div>
                <div class="flex items-center space-x-1.5 shrink-0">
                  <button type="button" @click="viewQrCode(student)" class="cursor-pointer px-2.5 py-1.5 bg-teal-700 text-white rounded-xl text-xs font-bold">QR</button>
                  <button type="button" @click="openModal(student)" class="cursor-pointer px-2.5 py-1.5 bg-white text-stone-700 border border-stone-200 rounded-xl text-xs font-semibold">Edit</button>
                  <button type="button" @click="deleteStudent(student.id)" class="cursor-pointer px-2.5 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold">Hapus</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Desktop & Tablet Table (sm+) -->
          <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-sm text-stone-700">
              <thead class="bg-[#FAF8F5] text-[11px] font-bold text-stone-500 uppercase tracking-wider border-y border-stone-200/80">
                <tr>
                  <th class="px-4 py-3.5">Peserta Didik</th>
                  <th class="px-4 py-3.5">Kelas</th>
                  <th class="px-4 py-3.5">Wali Murid</th>
                  <th class="px-4 py-3.5">Token QR</th>
                  <th class="px-4 py-3.5 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-stone-100">
                <tr v-for="student in students" :key="student.id" class="hover:bg-[#FAF8F5]/80 transition-colors">
                  <td class="px-4 py-3.5">
                    <div class="flex items-center space-x-3">
                      <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/60 font-bold text-xs flex items-center justify-center shrink-0">
                        {{ student.name.charAt(0).toUpperCase() }}
                      </div>
                      <div>
                        <p class="font-bold text-stone-900">{{ student.name }}</p>
                        <p class="text-xs font-mono text-stone-500">NIS: {{ student.nis }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3.5">
                    <span class="px-2.5 py-1 rounded-xl bg-stone-100 text-stone-700 font-semibold text-xs">
                      {{ student.class?.name || '-' }}
                    </span>
                  </td>
                  <td class="px-4 py-3.5 text-xs">
                    <p class="text-stone-800 font-semibold">{{ student.parent?.name || '-' }}</p>
                    <p class="text-stone-500 font-mono">{{ student.parent?.whatsapp_number || '' }}</p>
                  </td>
                  <td class="px-4 py-3.5 font-mono text-amber-800 font-semibold text-xs">{{ student.qr_code?.token || '-' }}</td>
                  <td class="px-4 py-3.5 text-right space-x-2">
                    <button
                      type="button"
                      @click="viewQrCode(student)"
                      class="cursor-pointer px-3 py-1.5 bg-teal-50 text-teal-800 border border-teal-200/80 rounded-xl text-xs font-bold hover:bg-teal-100/80 transition-all"
                    >
                      Kartu QR
                    </button>
                    <button
                      type="button"
                      @click="openModal(student)"
                      class="cursor-pointer px-3 py-1.5 bg-[#FAF8F5] text-stone-700 border border-stone-200 rounded-xl text-xs font-semibold hover:bg-stone-100 transition-all"
                    >
                      Edit
                    </button>
                    <button
                      type="button"
                      @click="deleteStudent(student.id)"
                      class="cursor-pointer px-3 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold hover:bg-rose-100 transition-all"
                    >
                      Hapus
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Animated QR Code Preview & Download Modal -->
    <Transition name="modal">
      <div v-if="selectedQrStudent" class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4" @click.self="selectedQrStudent = null">
        <div class="modal-box bg-white border border-stone-200 rounded-3xl p-5 sm:p-7 max-w-md w-full max-h-[90vh] overflow-y-auto text-center space-y-5 shadow-2xl relative">
          <button
            type="button"
            @click="selectedQrStudent = null"
            class="cursor-pointer absolute top-5 right-5 text-stone-400 hover:text-stone-700"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <h3 class="text-lg font-extrabold text-stone-900">Kartu Identitas QR Siswa</h3>

          <div id="printable-qr-card" class="bg-[#FAF8F5] border-2 border-stone-200 p-5 sm:p-6 rounded-3xl space-y-4">
            <div class="border-b border-stone-200 pb-3">
              <h4 class="text-sm font-extrabold text-teal-800 tracking-wider">SILAPOR SCHOOL ID</h4>
              <p class="text-xs text-stone-500 font-medium">Kartu Akses Laptop Peserta Didik</p>
            </div>

            <div class="bg-white p-4 rounded-2xl inline-block shadow-xs border border-stone-200/80">
              <img
                :src="selectedQrStudent.qr_code?.qr_image_url"
                alt="QR Code Siswa"
                class="w-40 h-40 sm:w-44 sm:h-44 mx-auto"
              />
            </div>

            <div>
              <h3 class="text-base font-extrabold text-stone-900">{{ selectedQrStudent.name }}</h3>
              <p class="text-xs text-stone-600 font-mono">NIS: {{ selectedQrStudent.nis }} • Kelas: {{ selectedQrStudent.class?.name || '-' }}</p>
              <p class="text-xs font-mono text-amber-800 font-bold mt-1">{{ selectedQrStudent.qr_code?.token }}</p>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-2 pt-1">
            <button
              type="button"
              @click="downloadQrCode(selectedQrStudent)"
              class="cursor-pointer px-3 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-2xl transition-all flex items-center justify-center"
            >
              Unduh PNG
            </button>

            <button
              type="button"
              @click="printQrCard"
              class="cursor-pointer px-3 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-2xl transition-all flex items-center justify-center"
            >
              Cetak Kartu
            </button>

            <button
              type="button"
              @click="regenerateQrCode(selectedQrStudent)"
              class="cursor-pointer px-3 py-2.5 bg-[#FAF8F5] hover:bg-stone-100 text-stone-700 text-xs font-semibold rounded-2xl border border-stone-200 transition-all flex items-center justify-center"
            >
              Buat Ulang
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Animated Add/Edit Student Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4" @click.self="showModal = false">
        <div class="modal-box bg-white border border-stone-200 rounded-3xl p-5 sm:p-7 max-w-md w-full max-h-[90vh] overflow-y-auto space-y-5 shadow-2xl">
          <h3 class="text-lg font-extrabold text-stone-900">{{ editingId ? 'Perbarui Data Siswa' : 'Tambah Siswa Baru' }}</h3>

          <form @submit.prevent="saveStudent" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Nomor Induk Siswa (NIS)</label>
              <input v-model="form.nis" type="text" required class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all" />
            </div>

            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Nama Lengkap</label>
              <input v-model="form.name" type="text" required class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all" />
            </div>

            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Kelas</label>
              <select v-model="form.class_id" required class="cursor-pointer w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-medium">
                <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }} ({{ cls.grade }})</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Wali Murid</label>
              <select v-model="form.parent_id" class="cursor-pointer w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-medium">
                <option :value="null">-- Belum Ada Wali Murid --</option>
                <option v-for="p in parents" :key="p.id" :value="p.id">{{ p.name }} ({{ p.whatsapp_number }})</option>
              </select>
            </div>

            <div class="flex items-center justify-end space-x-2.5 pt-4 border-t border-stone-100">
              <button type="button" @click="showModal = false" class="cursor-pointer px-4 py-2.5 bg-[#FAF8F5] text-stone-700 text-xs font-semibold rounded-2xl border border-stone-200 hover:bg-stone-100 transition-all">Batal</button>
              <button type="submit" class="cursor-pointer px-5 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-2xl shadow-sm transition-all">Simpan Data</button>
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

const students = ref([])
const classes = ref([])
const parents = ref([])
const search = ref('')
const selectedQrStudent = ref(null)

const showModal = ref(false)
const editingId = ref(null)
const form = ref({ nis: '', name: '', class_id: '', parent_id: null })

const fetchStudents = async () => {
  try {
    const params = {}
    if (search.value && search.value.trim() !== '') {
      params.search = search.value.trim()
    }
    const res = await api.get('/students', { params })
    students.value = res.data.data
  } catch (err) {
    console.error('Error fetching students:', err)
  }
}

const fetchDependencies = async () => {
  try {
    const [cRes, pRes] = await Promise.all([api.get('/classes'), api.get('/parents')])
    classes.value = cRes.data.data
    parents.value = pRes.data.data
  } catch (err) {
    console.error('Error fetching dependencies:', err)
  }
}

const openModal = (student = null) => {
  if (student) {
    editingId.value = student.id
    form.value = { nis: student.nis, name: student.name, class_id: student.class?.id, parent_id: student.parent?.id || null }
  } else {
    editingId.value = null
    form.value = { nis: '', name: '', class_id: classes.value[0]?.id || '', parent_id: null }
  }
  showModal.value = true
}

const saveStudent = async () => {
  try {
    if (editingId.value) {
      await api.put(`/students/${editingId.value}`, form.value)
    } else {
      await api.post('/students', form.value)
    }
    showModal.value = false
    fetchStudents()
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menyimpan data siswa.')
  }
}

const deleteStudent = async (id) => {
  if (!confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) return
  try {
    await api.delete(`/students/${id}`)
    fetchStudents()
  } catch (err) {
    alert('Gagal menghapus siswa.')
  }
}

const viewQrCode = (student) => {
  selectedQrStudent.value = student
}

const downloadQrCode = (student) => {
  if (!student.qr_code?.qr_image_url) return
  const link = document.createElement('a')
  link.href = student.qr_code.qr_image_url
  link.download = `QR_${student.nis}_${student.name.replace(/\s+/g, '_')}.png`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const printQrCard = () => {
  const cardElement = document.getElementById('printable-qr-card')
  if (!cardElement) return

  const printWindow = window.open('', '_blank')
  printWindow.document.write(`
    <html>
      <head>
        <title>Kartu QR ${selectedQrStudent.value?.name}</title>
        <style>
          body { font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #fff; }
          .card { border: 2px solid #0f766e; padding: 24px; border-radius: 20px; text-align: center; width: 280px; }
          .header { font-size: 16px; font-weight: 800; color: #0f766e; margin-bottom: 4px; }
          .sub { font-size: 11px; color: #78716c; margin-bottom: 16px; }
          .qr { width: 180px; height: 180px; margin: 0 auto 16px auto; }
          .name { font-size: 16px; font-weight: 700; color: #1c1917; margin-bottom: 4px; }
          .meta { font-size: 12px; color: #57534e; font-family: monospace; }
          .token { font-size: 12px; font-weight: 700; color: #b45309; font-family: monospace; margin-top: 6px; }
        </style>
      </head>
      <body>
        <div class="card">
          <div class="header">SILAPOR SCHOOL ID</div>
          <div class="sub">Kartu Akses Laptop Peserta Didik</div>
          <img src="${selectedQrStudent.value?.qr_code?.qr_image_url}" class="qr" />
          <div class="name">${selectedQrStudent.value?.name}</div>
          <div class="meta">NIS: ${selectedQrStudent.value?.nis} | Kelas: ${selectedQrStudent.value?.class?.name || '-'}</div>
          <div class="token">${selectedQrStudent.value?.qr_code?.token}</div>
        </div>
      </body>
    </html>
  `)
  printWindow.document.close()
  printWindow.focus()
  setTimeout(() => {
    printWindow.print()
    printWindow.close()
  }, 300)
}

const regenerateQrCode = async (student) => {
  if (!confirm(`Generate ulang QR Code untuk ${student.name}? Token lama tidak akan bisa digunakan lagi.`)) return
  try {
    const res = await api.post(`/students/${student.id}/generate-qr`)
    student.qr_code = res.data.data
    selectedQrStudent.value = { ...student, qr_code: res.data.data }
    alert('QR Code baru berhasil digenerate.')
    fetchStudents()
  } catch (err) {
    alert('Gagal meng-generate ulang QR Code.')
  }
}

onMounted(() => {
  fetchStudents()
  fetchDependencies()
})
</script>
