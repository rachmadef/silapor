<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-900/15">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <div>
          <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Sesi Siang • 13.00 – 15.00 WIB</span>
          <h2 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight">Scan Pengembalian Laptop</h2>
          <p class="text-xs sm:text-sm text-stone-500 mt-0.5">Pindai kembali QR Code siswa untuk memverifikasi pengembalian laptop</p>
        </div>
      </div>
    </div>

    <!-- Scan Alert Result Banner -->
    <Transition name="fade-slide" mode="out-in">
      <div
        v-if="lastResult"
        :key="lastResult.message"
        :class="[
          'p-5 rounded-3xl border transition-all duration-300 flex items-start space-x-4 shadow-xs',
          lastResult.success ? 'bg-emerald-50/90 border-emerald-200 text-emerald-900' : 'bg-rose-50/90 border-rose-200 text-rose-900'
        ]"
      >
        <div class="p-2.5 rounded-2xl shrink-0" :class="lastResult.success ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'">
          <svg v-if="lastResult.success" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="font-extrabold text-sm">{{ lastResult.success ? 'Pengembalian Berhasil Dicatat' : 'Peringatan Scan' }}</h4>
          <p class="text-xs mt-0.5 font-medium">{{ lastResult.message }}</p>
          <div v-if="lastResult.data" class="mt-3 pt-2.5 border-t border-emerald-200/80 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
            <div><span class="text-emerald-700 font-semibold">Kondisi:</span> {{ lastResult.data.condition }}</div>
            <div><span class="text-emerald-700 font-semibold">Waktu:</span> {{ formatDate(lastResult.data.returned_at) }}</div>
          </div>
        </div>
        <button @click="lastResult = null" class="p-1 text-stone-400 hover:text-stone-700 rounded-lg cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </Transition>

    <!-- Scanner Controls Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Left: USB Input Scanner -->
      <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs space-y-5">
        <div>
          <h3 class="text-base font-extrabold text-stone-900">Scanner USB / Input Token & NIS</h3>
          <p class="text-xs text-stone-500 mt-0.5">Pindai QR siswa dan catat kondisi fisik laptop saat dikembalikan</p>
        </div>

        <form @submit.prevent="submitScan" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-stone-700 mb-2">Token QR Siswa atau NIS</label>
            <input
              ref="qrInputRef"
              v-model="qrTokenInput"
              type="text"
              required
              placeholder="Arahkan scanner atau ketik NIS..."
              class="w-full bg-[#FAF8F5] border-2 border-stone-200 rounded-2xl px-4 py-3.5 text-stone-900 font-mono text-sm focus:outline-none focus:border-emerald-700 focus:bg-white transition-all"
              autofocus
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-stone-700 mb-2">Kondisi Perangkat</label>
              <select
                v-model="conditionInput"
                class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-3 text-stone-900 text-xs focus:outline-none focus:border-emerald-700 focus:bg-white transition-all font-semibold cursor-pointer"
              >
                <option value="good">Baik / Normal</option>
                <option value="damaged">Ada Kendala / Rusak</option>
                <option value="lost">Hilang</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-stone-700 mb-2">Catatan (Opsional)</label>
              <input
                v-model="notesInput"
                type="text"
                placeholder="Misal: Charger tertinggal..."
                class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-3 text-stone-900 text-xs focus:outline-none focus:border-emerald-700 focus:bg-white transition-all"
              />
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3.5 px-4 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-sm shadow-emerald-900/15 transition-all disabled:opacity-50 cursor-pointer"
          >
            {{ loading ? 'Memproses...' : 'Catat Pengembalian Laptop' }}
          </button>
        </form>
      </div>

      <!-- Right: Web Camera Scanner -->
      <CameraScanner @scan="handleCameraScan" />
    </div>

    <!-- Today's Return History Table -->
    <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-base font-extrabold text-stone-900">Riwayat Pengembalian Siang Ini</h3>
          <p class="text-xs text-stone-500">Daftar siswa yang telah mengembalikan laptop hari ini</p>
        </div>
        <span class="px-3.5 py-1.5 rounded-full bg-[#FAF8F5] border border-stone-200 text-xs text-stone-700 font-mono font-bold">
          {{ returns.length }} Siswa
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-stone-700">
          <thead class="bg-[#FAF8F5] text-[11px] font-bold text-stone-500 uppercase tracking-wider border-y border-stone-200/80">
            <tr>
              <th class="px-4 py-3.5">Peserta Didik</th>
              <th class="px-4 py-3.5">Kelas</th>
              <th class="px-4 py-3.5">Waktu Kembali</th>
              <th class="px-4 py-3.5">Kondisi</th>
              <th class="px-4 py-3.5">Catatan</th>
              <th class="px-4 py-3.5">Petugas</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100">
            <tr v-if="returns.length === 0">
              <td colspan="6" class="px-4 py-10 text-center text-stone-400 text-xs font-medium">
                Belum ada transaksi pengembalian laptop yang tercatat siang ini.
              </td>
            </tr>
            <tr v-for="ret in returns" :key="ret.id" class="hover:bg-[#FAF8F5]/80 transition-colors">
              <td class="px-4 py-3.5">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-bold text-xs flex items-center justify-center shrink-0">
                    {{ ret.loan?.student?.name?.charAt(0) || 'S' }}
                  </div>
                  <div>
                    <p class="font-bold text-stone-900">{{ ret.loan?.student?.name }}</p>
                    <p class="text-xs font-mono text-stone-500">NIS: {{ ret.loan?.student?.nis }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3.5 text-stone-600 text-xs font-semibold">{{ ret.loan?.student?.class_name }}</td>
              <td class="px-4 py-3.5 text-xs text-stone-600 font-mono">{{ formatDate(ret.returned_at) }}</td>
              <td class="px-4 py-3.5">
                <span
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold"
                  :class="ret.condition === 'good' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200'"
                >
                  {{ ret.condition === 'good' ? 'Baik' : ret.condition }}
                </span>
              </td>
              <td class="px-4 py-3.5 text-xs text-stone-500 font-medium">{{ ret.notes || '-' }}</td>
              <td class="px-4 py-3.5 text-xs text-stone-500 font-medium">{{ ret.officer?.name || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useMonitoringStore } from '@/stores/monitoring'
import CameraScanner from '@/components/scanner/CameraScanner.vue'

const monitoringStore = useMonitoringStore()

const qrInputRef = ref(null)
const qrTokenInput = ref('')
const conditionInput = ref('good')
const notesInput = ref('')
const loading = ref(false)
const lastResult = ref(null)

let lastSubmittedToken = ''
let lastSubmittedTime = 0

const returns = computed(() => monitoringStore.returns)

const submitScan = async () => {
  const token = qrTokenInput.value?.trim()
  if (!token) return
  if (loading.value) return

  const now = Date.now()
  if (token === lastSubmittedToken && now - lastSubmittedTime < 2500) {
    return
  }
  lastSubmittedToken = token
  lastSubmittedTime = now

  loading.value = true
  lastResult.value = null

  try {
    const res = await monitoringStore.processReturn(
      token,
      conditionInput.value,
      notesInput.value?.trim() || null
    )
    lastResult.value = res
    qrTokenInput.value = ''
    notesInput.value = ''
    await monitoringStore.fetchReturns()
  } catch (err) {
    lastResult.value = err
  } finally {
    loading.value = false
    if (qrInputRef.value) qrInputRef.value.focus()
  }
}

const handleCameraScan = (decodedText) => {
  if (loading.value) return
  qrTokenInput.value = decodedText
  submitScan()
}

const formatDate = (isoStr) => {
  if (!isoStr) return '-'
  return new Date(isoStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
}

onMounted(() => {
  monitoringStore.fetchReturns()
  if (qrInputRef.value) qrInputRef.value.focus()
})
</script>
