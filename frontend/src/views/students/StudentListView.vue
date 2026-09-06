<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
      <div>
        <h2 class="text-xl font-bold text-slate-900">Master Data Siswa & QR Code</h2>
        <p class="text-xs text-slate-500 mt-1">Kelola data siswa, relasi wali murid, cetak & download QR Code unik siswa</p>
      </div>

      <div class="flex items-center space-x-3">
        <input
          v-model="search"
          type="text"
          placeholder="Cari NIS / Nama Siswa..."
          @input="fetchStudents"
          class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-900 text-xs focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all font-medium"
        />
        <button
          @click="openModal()"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all flex items-center shrink-0"
        >
          <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Tambah Siswa
        </button>
      </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700">
          <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
            <tr>
              <th class="px-4 py-3">NIS</th>
              <th class="px-4 py-3">Nama Siswa</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Wali Murid</th>
              <th class="px-4 py-3">QR Token</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="students.length === 0">
              <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs font-medium">
                Tidak ada data siswa ditemukan.
              </td>
            </tr>
            <tr v-for="student in students" :key="student.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 font-mono text-indigo-600 font-semibold text-xs">{{ student.nis }}</td>
              <td class="px-4 py-3 font-bold text-slate-900">{{ student.name }}</td>
              <td class="px-4 py-3 text-slate-600">{{ student.class?.name || '-' }}</td>
              <td class="px-4 py-3 text-xs">
                <p class="text-slate-800 font-semibold">{{ student.parent?.name || '-' }}</p>
                <p class="text-slate-500 font-mono">{{ student.parent?.whatsapp_number || '' }}</p>
              </td>
              <td class="px-4 py-3 font-mono text-amber-700 font-semibold text-xs">{{ student.qr_code?.token || '-' }}</td>
              <td class="px-4 py-3 text-right space-x-2">
                <button
                  @click="viewQrCode(student)"
                  class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-semibold hover:bg-indigo-100 transition-all shadow-xs"
                >
                  Kartu QR
                </button>
                <button
                  @click="openModal(student)"
                  class="px-2.5 py-1 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold hover:bg-slate-200 transition-all shadow-xs"
                >
                  Edit
                </button>
                <button
                  @click="deleteStudent(student.id)"
                  class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold hover:bg-rose-100 transition-all shadow-xs"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- QR Code Preview & Download Modal -->
    <div v-if="selectedQrStudent" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white border border-slate-200 rounded-3xl p-6 max-w-md w-full text-center space-y-5 shadow-2xl relative">
        <button
          @click="selectedQrStudent = null"
          class="absolute top-4 right-4 text-slate-400 hover:text-slate-700"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <h3 class="text-lg font-bold text-slate-900">Kartu Identitas QR Siswa</h3>

        <!-- Printable Card Container -->
        <div id="printable-qr-card" class="bg-slate-50 border-2 border-indigo-200 p-6 rounded-2xl space-y-4 shadow-sm">
          <div class="border-b border-slate-200 pb-3">
            <h4 class="text-sm font-extrabold text-indigo-600 tracking-wider">SILAPOR SCHOOL ID</h4>
            <p class="text-xs text-slate-500 font-medium">Monitoring Laptop Siswa</p>
          </div>

          <div class="bg-white p-4 rounded-xl inline-block shadow-md border border-slate-100">
            <img
              :src="selectedQrStudent.qr_code?.qr_image_url"
              alt="QR Code Siswa"
              class="w-44 h-44 mx-auto"
            />
          </div>

          <div>
            <h3 class="text-base font-bold text-slate-900">{{ selectedQrStudent.name }}</h3>
            <p class="text-xs text-slate-600 font-mono">NIS: {{ selectedQrStudent.nis }} | Kelas: {{ selectedQrStudent.class?.name || '-' }}</p>
            <p class="text-xs font-mono text-amber-700 font-bold mt-1">{{ selectedQrStudent.qr_code?.token }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-3 gap-2 pt-2">
          <button
            @click="downloadQrCode(selectedQrStudent)"
            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download
          </button>

          <button
            @click="printQrCard"
            class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Card
          </button>

          <button
            @click="regenerateQrCode(selectedQrStudent)"
            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition-all flex items-center justify-center shadow-xs"
          >
            <svg class="w-4 h-4 mr-1 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Regenerate
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Student Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white border border-slate-200 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900">{{ editingId ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h3>

        <form @submit.prevent="saveStudent" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">NIS Siswa</label>
            <input v-model="form.nis" type="text" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all" />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Lengkap</label>
            <input v-model="form.name" type="text" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all" />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kelas</label>
            <select v-model="form.class_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all font-medium">
              <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }} ({{ cls.grade }})</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Wali Murid</label>
            <select v-model="form.parent_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all font-medium">
              <option :value="null">-- Tanpa Wali Murid --</option>
              <option v-for="p in parents" :key="p.id" :value="p.id">{{ p.name }} ({{ p.whatsapp_number }})</option>
            </select>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-200 transition-all">Batal</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all">Simpan Data</button>
          </div>
        </form>
      </div>
    </div>
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
          .card { border: 2px solid #0891b2; padding: 24px; border-radius: 16px; text-align: center; width: 280px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
          .header { font-size: 16px; font-weight: 800; color: #0891b2; margin-bottom: 4px; }
          .sub { font-size: 11px; color: #64748b; margin-bottom: 16px; }
          .qr { width: 180px; height: 180px; margin: 0 auto 16px auto; }
          .name { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
          .meta { font-size: 12px; color: #475569; font-family: monospace; }
          .token { font-size: 12px; font-weight: 700; color: #d97706; font-family: monospace; margin-top: 6px; }
        </style>
      </head>
      <body>
        <div class="card">
          <div class="header">SILAPOR SCHOOL ID</div>
          <div class="sub">Monitoring Laptop Siswa</div>
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
