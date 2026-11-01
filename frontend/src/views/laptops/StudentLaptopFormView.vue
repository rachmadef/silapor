<template>
  <div class="min-h-screen bg-[#FAF8F5] flex flex-col justify-center items-center p-4 sm:p-6 relative overflow-hidden">
    <!-- Warm Organic Ambient Glow -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-teal-500/8 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Top Navigation Bar -->
    <div class="w-full max-w-xl mb-4 flex items-center justify-between gap-2 relative z-10">
      <RouterLink
        v-if="authStore.isAuthenticated"
        to="/laptops"
        class="cursor-pointer inline-flex items-center text-xs font-bold text-teal-800 bg-white hover:bg-stone-50 border border-stone-200/90 px-3.5 py-2 rounded-2xl shadow-2xs transition-all"
      >
        <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Data Laptop</span>
      </RouterLink>
      <RouterLink
        v-else
        to="/login"
        class="cursor-pointer inline-flex items-center text-xs font-semibold text-stone-600 hover:text-stone-900 bg-white border border-stone-200/90 px-3.5 py-2 rounded-2xl shadow-2xs transition-all"
      >
        <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Halaman Petugas</span>
      </RouterLink>

      <span class="text-[11px] sm:text-xs font-bold text-amber-900 bg-amber-100/80 border border-amber-200 px-3 py-1.5 rounded-full shrink-0">
        ✦ Form Peserta Didik
      </span>
    </div>

    <!-- Main Humanist Card -->
    <div class="w-full max-w-xl bg-white border border-stone-200/90 rounded-3xl sm:rounded-[2rem] p-5 sm:p-9 shadow-xl shadow-stone-900/5 relative z-10">
      <!-- Header -->
      <div class="flex items-start space-x-3.5 sm:space-x-4 pb-5 sm:pb-6 border-b border-stone-100">
        <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl bg-gradient-to-br from-teal-700 to-emerald-600 flex items-center justify-center shrink-0 shadow-md shadow-teal-900/15">
          <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <div>
          <h1 class="text-lg sm:text-2xl font-extrabold text-stone-900 tracking-tight">Halo! Yuk Daftarkan Laptopmu</h1>
          <p class="text-xs sm:text-sm text-stone-500 mt-1 leading-relaxed">
            Pilih namamu di bawah ini lalu isi merk dan tipe laptop yang kamu gunakan di sekolah.
          </p>
        </div>
      </div>

      <!-- Smooth Transition Between Form and Success View -->
      <Transition name="fade-slide" mode="out-in">
        <!-- Success State -->
        <div v-if="submittedData" key="success" class="py-5 sm:py-6 space-y-5 sm:space-y-6">
          <div class="p-5 sm:p-6 rounded-3xl bg-emerald-50/80 border border-emerald-200 text-center space-y-2.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center mx-auto shadow-sm">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-extrabold text-stone-900">Terima Kasih! Data Laptopmu Tersimpan</h3>
            <p class="text-xs text-emerald-800 font-medium">{{ successMessage }}</p>
          </div>

          <div class="bg-[#FAF8F5] border border-stone-200/80 rounded-3xl p-4 sm:p-5 space-y-3 text-sm">
            <div class="flex justify-between items-center py-1.5 border-b border-stone-200/70">
              <span class="text-stone-500 text-xs font-semibold">Nama Peserta Didik</span>
              <span class="font-bold text-stone-900 text-right">{{ submittedData.student?.name }}</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-stone-200/70">
              <span class="text-stone-500 text-xs font-semibold">NIS / Kelas</span>
              <span class="font-mono text-xs font-semibold text-stone-700">{{ submittedData.student?.nis }} • {{ submittedData.student?.class_name }}</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-stone-200/70">
              <span class="text-stone-500 text-xs font-semibold">Kode Aset Laptop</span>
              <span class="font-mono text-xs font-bold text-teal-800 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-200">{{ submittedData.asset_code }}</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-stone-200/70">
              <span class="text-stone-500 text-xs font-semibold">Merk Laptop</span>
              <span class="font-bold text-stone-900">{{ submittedData.brand }}</span>
            </div>
            <div class="flex justify-between items-center py-1.5">
              <span class="text-stone-500 text-xs font-semibold">Tipe / Seri Laptop</span>
              <span class="font-medium text-stone-700">{{ submittedData.model || '-' }}</span>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3">
            <button
              type="button"
              @click="resetForm"
              class="cursor-pointer flex-1 py-3.5 px-4 rounded-2xl bg-teal-700 hover:bg-teal-800 text-white font-bold text-sm shadow-md shadow-teal-900/15 transition-all"
            >
              Isi untuk Siswa Lainnya
            </button>
            <RouterLink
              v-if="authStore.isAuthenticated"
              to="/laptops"
              class="cursor-pointer py-3.5 px-5 rounded-2xl bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-sm text-center transition-all"
            >
              Lihat Tabel Aset
            </RouterLink>
          </div>
        </div>

        <!-- Form State -->
        <form v-else key="form" @submit.prevent="submitForm" class="mt-5 sm:mt-6 space-y-5">
          <Transition name="fade-slide">
            <div v-if="errorMessage" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start space-x-2.5">
              <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ errorMessage }}</span>
            </div>
          </Transition>

          <!-- 1. Filter Search Option: Nama Siswa -->
          <div ref="dropdownContainer" class="relative">
            <label class="block text-xs font-bold text-stone-700 mb-2">
              1. Cari & Pilih Namamu <span class="text-rose-500">*</span>
            </label>

            <div class="relative">
              <input
                v-model="studentSearch"
                type="text"
                placeholder="Ketik nama lengkap, NIS, atau kelas..."
                @focus="isDropdownOpen = true"
                @input="onSearchInput"
                class="w-full bg-[#FAF8F5] border-2 rounded-2xl pl-10 pr-10 py-3.5 text-stone-900 text-sm focus:outline-none focus:bg-white transition-all font-medium"
                :class="selectedStudent ? 'border-teal-600 bg-teal-50/20' : 'border-stone-200 focus:border-teal-700'"
              />
              <svg class="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>

              <button
                v-if="selectedStudent || studentSearch"
                type="button"
                @click="clearSelectedStudent"
                class="cursor-pointer absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 p-1"
                title="Hapus pilihan"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Animated Dropdown List -->
            <Transition name="fade-slide">
              <div
                v-if="isDropdownOpen"
                class="absolute z-30 mt-2 w-full bg-white border border-stone-200 rounded-2xl shadow-xl max-h-60 overflow-y-auto divide-y divide-stone-100"
              >
                <div v-if="loadingStudents" class="p-4 text-center text-xs text-stone-400">
                  Memuat daftar nama peserta didik...
                </div>
                <div v-else-if="filteredStudents.length === 0" class="p-4 text-center text-xs text-stone-400">
                  Nama siswa "{{ studentSearch }}" tidak ditemukan.
                </div>
                <button
                  v-for="student in filteredStudents"
                  :key="student.id"
                  type="button"
                  @click="selectStudent(student)"
                  class="cursor-pointer w-full text-left px-4 py-3 hover:bg-[#FAF8F5] transition-colors flex items-center justify-between gap-2"
                  :class="selectedStudent?.id === student.id ? 'bg-teal-50/60' : ''"
                >
                  <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-stone-100 text-stone-700 font-bold text-xs flex items-center justify-center shrink-0">
                      {{ student.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="truncate">
                      <p class="text-sm font-bold text-stone-900 truncate">{{ student.name }}</p>
                      <p class="text-xs text-stone-500 font-mono">NIS: {{ student.nis }} • {{ student.class_name }}</p>
                    </div>
                  </div>
                  <div class="shrink-0">
                    <span
                      v-if="student.laptop"
                      class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200"
                    >
                      {{ student.laptop.brand }}{{ student.laptop.model ? ' ' + student.laptop.model : '' }}
                    </span>
                    <span
                      v-else
                      class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-stone-100 text-stone-500"
                    >
                      Belum isi
                    </span>
                  </div>
                </button>
              </div>
            </Transition>

            <!-- Selected Student Card -->
            <Transition name="fade-slide">
              <div
                v-if="selectedStudent"
                class="mt-2.5 p-3.5 rounded-2xl bg-teal-50/60 border border-teal-200/80 flex items-center justify-between gap-2 text-xs"
              >
                <div class="flex items-center space-x-3 min-w-0">
                  <div class="w-9 h-9 rounded-xl bg-teal-700 text-white font-bold flex items-center justify-center shrink-0">
                    {{ selectedStudent.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="truncate">
                    <p class="font-bold text-stone-900 text-sm truncate">{{ selectedStudent.name }}</p>
                    <p class="text-stone-600 font-mono">NIS: {{ selectedStudent.nis }} • Kelas: {{ selectedStudent.class_name }}</p>
                  </div>
                </div>
                <span v-if="selectedStudent.laptop" class="text-[11px] font-bold text-amber-800 bg-amber-100/80 border border-amber-200 px-2.5 py-1 rounded-xl shrink-0">
                  Perbarui Data
                </span>
              </div>
            </Transition>
          </div>

          <!-- 2. Merk Laptop -->
          <div>
            <label class="block text-xs font-bold text-stone-700 mb-2">
              2. Merk Laptop <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.brand"
              type="text"
              required
              placeholder="Ketik atau pilih merk di bawah (misal: Lenovo, Asus, Acer)..."
              class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-4 py-3.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white focus:ring-3 focus:ring-teal-700/10 transition-all font-medium"
            />

            <div class="mt-2.5 flex flex-wrap gap-1.5">
              <button
                v-for="brand in popularBrands"
                :key="brand"
                type="button"
                @click="form.brand = brand"
                class="cursor-pointer px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all"
                :class="form.brand.toLowerCase() === brand.toLowerCase()
                  ? 'bg-teal-700 text-white border-teal-700 shadow-2xs'
                  : 'bg-[#FAF8F5] text-stone-600 border-stone-200 hover:bg-stone-100'"
              >
                {{ brand }}
              </button>
            </div>
          </div>

          <!-- 3. Tipe Laptop (Opsional) -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="block text-xs font-bold text-stone-700">
                3. Tipe / Seri Laptop
              </label>
              <span class="text-[11px] font-semibold text-stone-500 bg-stone-100 px-2.5 py-0.5 rounded-full">Opsional</span>
            </div>
            <input
              v-model="form.model"
              type="text"
              placeholder="Contoh: ThinkPad X280, Vivobook 14, Aspire 5 (Boleh dikosongkan)"
              class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-4 py-3.5 text-stone-900 text-sm focus:outline-none focus:border-teal-700 focus:bg-white focus:ring-3 focus:ring-teal-700/10 transition-all font-medium"
            />
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button
              type="submit"
              :disabled="submitting || !selectedStudent || !form.brand.trim()"
              class="cursor-pointer w-full py-3.5 px-5 rounded-2xl bg-teal-700 hover:bg-teal-800 text-white font-bold text-sm shadow-md shadow-teal-900/15 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ submitting ? 'Menyimpan Data Laptop...' : 'Simpan Data Laptop Saya' }}
            </button>
          </div>
        </form>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const students = ref([])
const loadingStudents = ref(false)
const studentSearch = ref('')
const isDropdownOpen = ref(false)
const selectedStudent = ref(null)
const dropdownContainer = ref(null)

const popularBrands = ['Lenovo', 'Asus', 'Acer', 'HP', 'Dell', 'Apple MacBook', 'MSI', 'Axioo', 'Advan', 'Infinix']

const form = ref({
  brand: '',
  model: '',
})

const submitting = ref(false)
const errorMessage = ref('')
const submittedData = ref(null)
const successMessage = ref('')

const filteredStudents = computed(() => {
  const q = studentSearch.value.trim().toLowerCase()
  if (!q) return students.value
  return students.value.filter(
    (s) =>
      s.name.toLowerCase().includes(q) ||
      s.nis.toLowerCase().includes(q) ||
      (s.class_name && s.class_name.toLowerCase().includes(q))
  )
})

const fetchPublicStudents = async () => {
  loadingStudents.value = true
  try {
    const res = await api.get('/public/students')
    students.value = res.data.data || []
  } catch (err) {
    errorMessage.value = 'Gagal memuat daftar peserta didik.'
  } finally {
    loadingStudents.value = false
  }
}

const onSearchInput = () => {
  isDropdownOpen.value = true
  if (selectedStudent.value && studentSearch.value !== `${selectedStudent.value.name} (${selectedStudent.value.nis} - ${selectedStudent.value.class_name})`) {
    selectedStudent.value = null
  }
}

const selectStudent = (student) => {
  selectedStudent.value = student
  studentSearch.value = `${student.name} (${student.nis} - ${student.class_name})`
  isDropdownOpen.value = false
  errorMessage.value = ''

  if (student.laptop) {
    form.value.brand = student.laptop.brand || ''
    form.value.model = student.laptop.model || ''
  } else {
    form.value.brand = ''
    form.value.model = ''
  }
}

const clearSelectedStudent = () => {
  selectedStudent.value = null
  studentSearch.value = ''
  form.value.brand = ''
  form.value.model = ''
  isDropdownOpen.value = true
}

const submitForm = async () => {
  if (!selectedStudent.value) {
    errorMessage.value = 'Silakan cari dan pilih nama peserta didik terlebih dahulu.'
    return
  }
  if (!form.value.brand.trim()) {
    errorMessage.value = 'Merk laptop wajib diisi.'
    return
  }

  submitting.value = true
  errorMessage.value = ''

  try {
    const res = await api.post('/public/laptop-form', {
      student_id: selectedStudent.value.id,
      brand: form.value.brand.trim(),
      model: form.value.model.trim() || null,
    })
    submittedData.value = res.data.data
    successMessage.value = res.data.message
    await fetchPublicStudents()
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal menyimpan data laptop. Silakan coba lagi.'
  } finally {
    submitting.value = false
  }
}

const resetForm = () => {
  submittedData.value = null
  successMessage.value = ''
  selectedStudent.value = null
  studentSearch.value = ''
  form.value = { brand: '', model: '' }
}

const handleClickOutside = (e) => {
  if (dropdownContainer.value && !dropdownContainer.value.contains(e.target)) {
    isDropdownOpen.value = false
  }
}

onMounted(() => {
  fetchPublicStudents()
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
