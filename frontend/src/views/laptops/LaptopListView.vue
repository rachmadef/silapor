<template>
  <div class="space-y-5 sm:space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-2xs surface-card-hover">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Inventaris Sekolah</span>
        <h2 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight mt-0.5">Data Laptop Peserta Didik</h2>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">
          Data laptop terhubung langsung dengan masing-masing siswa melalui form mandiri atau input petugas
        </p>
      </div>

      <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 sm:gap-2.5">
        <button
          type="button"
          @click="copyFormLink"
          class="cursor-pointer px-3.5 py-2.5 bg-[#FAF8F5] hover:bg-stone-100 text-stone-700 border border-stone-200 text-xs font-bold rounded-xl sm:rounded-2xl transition-all flex items-center justify-center"
        >
          <svg class="w-4 h-4 mr-1.5 text-teal-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
          </svg>
          <span class="truncate">{{ copied ? 'Link Disalin!' : 'Salin Link Form' }}</span>
        </button>

        <RouterLink
          to="/form-laptop"
          class="cursor-pointer px-3.5 py-2.5 bg-amber-50 hover:bg-amber-100/80 text-amber-900 border border-amber-200 text-xs font-bold rounded-xl sm:rounded-2xl transition-all flex items-center justify-center"
        >
          <svg class="w-4 h-4 mr-1.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
          <span class="truncate">Buka Form Siswa</span>
        </RouterLink>

        <button
          type="button"
          @click="openModal()"
          class="cursor-pointer col-span-2 sm:col-span-1 px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-xl sm:rounded-2xl shadow-xs transition-all flex items-center justify-center"
        >
          <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Tambah Laptop</span>
        </button>
      </div>
    </div>

    <!-- Table & Search -->
    <div class="bg-white border border-stone-200/90 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-2xs space-y-4 sm:space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative w-full sm:w-84">
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama siswa, NIS, merk, atau kode aset..."
            @input="fetchLaptops"
            class="w-full bg-[#FAF8F5] border border-stone-200 rounded-xl sm:rounded-2xl pl-9 pr-4 py-2.5 text-stone-900 text-xs focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-medium"
          />
          <svg class="w-4 h-4 text-stone-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <span class="self-start sm:self-auto text-xs font-semibold text-stone-500 bg-[#FAF8F5] border border-stone-200/80 px-3.5 py-1.5 rounded-full">
          Terdaftar: <strong class="text-stone-900 font-mono">{{ laptops.length }}</strong> Perangkat
        </span>
      </div>

      <Transition name="fade-slide" mode="out-in">
        <!-- Empty State -->
        <div v-if="laptops.length === 0" key="empty" class="bg-[#FAF8F5] border border-stone-200/70 rounded-2xl p-6 sm:p-10 text-center">
          <div class="max-w-md mx-auto space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-100/80 text-amber-800 border border-amber-200 flex items-center justify-center mx-auto">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-base font-extrabold text-stone-900">Data Laptop Masih Kosong</h3>
            <p class="text-xs text-stone-500 leading-relaxed">
              Bagikan tautan <strong>Form Mandiri Peserta Didik</strong> ke grup kelas agar siswa dapat mengisi merk dan tipe laptop mereka masing-masing.
            </p>
            <div class="pt-2 flex flex-wrap items-center justify-center gap-2.5">
              <RouterLink
                to="/form-laptop"
                class="cursor-pointer px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-xl sm:rounded-2xl shadow-xs transition-all"
              >
                Buka Form Peserta Didik
              </RouterLink>
              <button
                type="button"
                @click="copyFormLink"
                class="cursor-pointer px-4 py-2.5 bg-white hover:bg-stone-100 text-stone-700 text-xs font-semibold rounded-xl sm:rounded-2xl border border-stone-200 transition-all"
              >
                {{ copied ? 'Tautan Disalin!' : 'Salin Tautan Form' }}
              </button>
            </div>
          </div>
        </div>

        <div v-else key="list">
          <!-- Mobile Card List (< sm) -->
          <div class="sm:hidden space-y-3">
            <div
              v-for="laptop in laptops"
              :key="laptop.id"
              class="p-4 rounded-2xl bg-[#FAF8F5] border border-stone-200/80 space-y-3"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="flex items-center space-x-2.5">
                  <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/60 font-bold text-xs flex items-center justify-center shrink-0">
                    {{ laptop.student?.name?.charAt(0) || 'L' }}
                  </div>
                  <div>
                    <p class="font-bold text-stone-900 text-sm">{{ laptop.student?.name || 'Belum dihubungkan' }}</p>
                    <p class="text-[11px] font-mono text-stone-500">
                      {{ laptop.student ? `NIS: ${laptop.student.nis} • ${laptop.student.class_name || '-'}` : laptop.asset_code }}
                    </p>
                  </div>
                </div>
                <StatusBadge :status="laptop.status" />
              </div>

              <div class="pt-2.5 border-t border-stone-200/70 flex items-center justify-between text-xs">
                <div>
                  <p class="font-bold text-stone-900">{{ laptop.brand || '-' }} {{ laptop.model || '' }}</p>
                  <p class="font-mono text-teal-800 text-[11px]">{{ laptop.asset_code }}</p>
                </div>
                <div class="flex items-center space-x-1.5">
                  <button type="button" @click="openModal(laptop)" class="cursor-pointer px-3 py-1.5 bg-white text-stone-700 border border-stone-200 rounded-xl text-xs font-semibold">Edit</button>
                  <button type="button" @click="deleteLaptop(laptop.id)" class="cursor-pointer px-3 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold">Hapus</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Desktop & Tablet Table (sm+) -->
          <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-sm text-stone-700">
              <thead class="bg-[#FAF8F5] text-[11px] font-bold text-stone-500 uppercase tracking-wider border-y border-stone-200/80">
                <tr>
                  <th class="px-4 py-3.5">Kode Aset</th>
                  <th class="px-4 py-3.5">Pemilik (Peserta Didik)</th>
                  <th class="px-4 py-3.5">Merk Laptop</th>
                  <th class="px-4 py-3.5">Tipe / Seri</th>
                  <th class="px-4 py-3.5">Status</th>
                  <th class="px-4 py-3.5 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-stone-100">
                <tr v-for="laptop in laptops" :key="laptop.id" class="hover:bg-[#FAF8F5]/80 transition-colors">
                  <td class="px-4 py-3.5 font-mono text-teal-800 font-bold text-xs">{{ laptop.asset_code }}</td>
                  <td class="px-4 py-3.5">
                    <div v-if="laptop.student" class="flex items-center space-x-3">
                      <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/60 font-bold text-xs flex items-center justify-center shrink-0">
                        {{ laptop.student.name?.charAt(0) || 'S' }}
                      </div>
                      <div>
                        <p class="font-bold text-stone-900 text-sm">{{ laptop.student.name }}</p>
                        <p class="text-xs text-stone-500 font-mono">NIS: {{ laptop.student.nis }} • {{ laptop.student.class_name || '-' }}</p>
                      </div>
                    </div>
                    <span v-else class="text-xs text-stone-400 italic">Belum dihubungkan ke siswa</span>
                  </td>
                  <td class="px-4 py-3.5 font-bold text-stone-900">{{ laptop.brand || '-' }}</td>
                  <td class="px-4 py-3.5 text-stone-600 text-xs font-medium">{{ laptop.model || '-' }}</td>
                  <td class="px-4 py-3.5">
                    <StatusBadge :status="laptop.status" />
                  </td>
                  <td class="px-4 py-3.5 text-right space-x-2">
                    <button type="button" @click="openModal(laptop)" class="cursor-pointer px-3 py-1.5 bg-[#FAF8F5] text-stone-700 border border-stone-200 rounded-xl text-xs font-semibold hover:bg-stone-100 transition-all">Edit</button>
                    <button type="button" @click="deleteLaptop(laptop.id)" class="cursor-pointer px-3 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold hover:bg-rose-100 transition-all">Hapus</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Animated Modal Add / Edit Laptop -->
    <Transition name="modal">
      <div v-if="showModal" class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4" @click.self="showModal = false">
        <div class="modal-box bg-white border border-stone-200 rounded-3xl p-5 sm:p-7 max-w-md w-full max-h-[90vh] overflow-y-auto space-y-5 shadow-2xl">
          <h3 class="text-lg font-extrabold text-stone-900">{{ editingId ? 'Perbarui Data Laptop' : 'Tambah Data Laptop Siswa' }}</h3>
          <form @submit.prevent="saveLaptop" class="space-y-4">
            <div class="relative">
              <label class="block text-xs font-bold text-stone-700 mb-1.5">
                Nama Peserta Didik <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="studentSearchQuery"
                  type="text"
                  placeholder="Cari nama siswa atau NIS..."
                  @focus="showStudentDropdown = true"
                  @input="showStudentDropdown = true"
                  class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all"
                />
                <button
                  v-if="form.student_id || studentSearchQuery"
                  type="button"
                  @click="clearModalStudent"
                  class="cursor-pointer absolute right-2.5 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <Transition name="fade-slide">
                <div
                  v-if="showStudentDropdown"
                  class="absolute z-20 mt-1.5 w-full bg-white border border-stone-200 rounded-2xl shadow-xl max-h-48 overflow-y-auto divide-y divide-stone-100"
                >
                  <div v-if="filteredStudents.length === 0" class="p-3 text-center text-xs text-stone-400">
                    Siswa tidak ditemukan
                  </div>
                  <button
                    v-for="s in filteredStudents"
                    :key="s.id"
                    type="button"
                    @click="selectModalStudent(s)"
                    class="cursor-pointer w-full text-left px-3.5 py-2.5 hover:bg-[#FAF8F5] transition-colors flex items-center justify-between text-xs"
                  >
                    <div>
                      <p class="font-bold text-stone-900">{{ s.name }}</p>
                      <p class="text-stone-500 font-mono">NIS: {{ s.nis }} • {{ s.class?.name || '-' }}</p>
                    </div>
                    <span v-if="form.student_id === s.id" class="text-teal-700 font-bold">Terpilih</span>
                  </button>
                </div>
              </Transition>
            </div>

            <div>
              <label class="block text-xs font-bold text-stone-700 mb-1.5">
                Merk Laptop <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.brand"
                type="text"
                required
                placeholder="Contoh: Lenovo, Asus, Acer, HP..."
                class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all"
              />
            </div>

            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold text-stone-700">Tipe / Seri Laptop</label>
                <span class="text-[11px] text-stone-400">Opsional</span>
              </div>
              <input
                v-model="form.model"
                type="text"
                placeholder="Contoh: ThinkPad X280, Vivobook 14..."
                class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all"
              />
            </div>

            <div v-if="editingId">
              <label class="block text-xs font-bold text-stone-700 mb-1.5">Status Perangkat</label>
              <select v-model="form.status" class="cursor-pointer w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3 py-2.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-medium">
                <option value="available">Tersedia</option>
                <option value="borrowed">Sedang Dipakai</option>
                <option value="maintenance">Perbaikan</option>
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
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import StatusBadge from '@/components/common/StatusBadge.vue'

const laptops = ref([])
const students = ref([])
const search = ref('')
const copied = ref(false)

const showModal = ref(false)
const editingId = ref(null)
const studentSearchQuery = ref('')
const showStudentDropdown = ref(false)

const form = ref({
  student_id: null,
  brand: '',
  model: '',
  status: 'available',
})

const filteredStudents = computed(() => {
  const q = studentSearchQuery.value.trim().toLowerCase()
  if (!q) return students.value
  return students.value.filter(
    (s) =>
      s.name.toLowerCase().includes(q) ||
      s.nis.toLowerCase().includes(q) ||
      (s.class?.name && s.class.name.toLowerCase().includes(q))
  )
})

const fetchLaptops = async () => {
  const params = {}
  if (search.value.trim()) {
    params.search = search.value.trim()
  }
  const res = await api.get('/laptops', { params })
  laptops.value = res.data.data
}

const fetchStudents = async () => {
  const res = await api.get('/students')
  students.value = res.data.data || []
}

const copyFormLink = async () => {
  const url = `${window.location.origin}/form-laptop`
  try {
    await navigator.clipboard.writeText(url)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2500)
  } catch {
    prompt('Salin link form peserta didik berikut:', url)
  }
}

const selectModalStudent = (student) => {
  form.value.student_id = student.id
  studentSearchQuery.value = `${student.name} (${student.nis} - ${student.class?.name || '-'})`
  showStudentDropdown.value = false
}

const clearModalStudent = () => {
  form.value.student_id = null
  studentSearchQuery.value = ''
  showStudentDropdown.value = true
}

const openModal = (laptop = null) => {
  showStudentDropdown.value = false
  if (laptop) {
    editingId.value = laptop.id
    form.value = {
      student_id: laptop.student_id,
      brand: laptop.brand || '',
      model: laptop.model || '',
      status: laptop.status || 'available',
    }
    studentSearchQuery.value = laptop.student
      ? `${laptop.student.name} (${laptop.student.nis} - ${laptop.student.class_name || '-'})`
      : ''
  } else {
    editingId.value = null
    form.value = {
      student_id: null,
      brand: '',
      model: '',
      status: 'available',
    }
    studentSearchQuery.value = ''
  }
  showModal.value = true
}

const saveLaptop = async () => {
  if (!form.value.student_id) {
    alert('Silakan pilih nama peserta didik terlebih dahulu.')
    return
  }
  try {
    if (editingId.value) {
      await api.put(`/laptops/${editingId.value}`, form.value)
    } else {
      await api.post('/laptops', form.value)
    }
    showModal.value = false
    fetchLaptops()
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menyimpan data laptop.')
  }
}

const deleteLaptop = async (id) => {
  if (!confirm('Hapus data laptop ini?')) return
  await api.delete(`/laptops/${id}`)
  fetchLaptops()
}

onMounted(() => {
  fetchLaptops()
  fetchStudents()
})
</script>
