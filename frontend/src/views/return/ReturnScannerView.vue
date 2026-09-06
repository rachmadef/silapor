<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-50 via-slate-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 shadow-sm">
      <div class="flex items-center space-x-3">
        <div class="p-3 bg-emerald-600 text-white rounded-xl shadow-md shadow-emerald-600/20">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <div>
          <h2 class="text-xl font-bold text-slate-900">Sesi Scan Pengembalian Laptop (Siang 13.00 - 15.00)</h2>
          <p class="text-xs text-emerald-700 font-medium">Pindai QR Code siswa untuk mencatat pengembalian laptop di siang hari</p>
        </div>
      </div>
    </div>

    <!-- Scan Alert Result Banner -->
    <div v-if="lastResult" :class="[
      'p-4 rounded-2xl border transition-all duration-300 flex items-start space-x-4 shadow-sm',
      lastResult.success ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'
    ]">
      <div class="p-2 rounded-xl" :class="lastResult.success ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
        <svg v-if="lastResult.success" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </div>
      <div class="flex-1">
        <h4 class="font-bold text-base">{{ lastResult.success ? 'PENGEMBALIAN BERHASIL DICATAT!' : 'PERINGATAN SCAN GAGAL' }}</h4>
        <p class="text-sm mt-0.5 font-medium">{{ lastResult.message }}</p>
        <div v-if="lastResult.data" class="mt-2 pt-2 border-t border-emerald-200 grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
          <div><span class="text-emerald-700 font-semibold">Kondisi:</span> {{ lastResult.data.condition }}</div>
          <div><span class="text-emerald-700 font-semibold">Waktu:</span> {{ formatDate(lastResult.data.returned_at) }}</div>
        </div>
      </div>
    </div>

    <!-- Scanner Controls Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Left: USB Input Scanner -->
      <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center space-x-2">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <h3 class="text-base font-bold text-slate-900">Scanner USB / Input Manual Token QR</h3>
        </div>

        <form @submit.prevent="submitScan" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Token QR Siswa / NIS</label>
            <input
              ref="qrInputRef"
              v-model="qrTokenInput"
              type="text"
              required
              placeholder="Arahkan scanner USB ke sini (e.g. STU-XXXXXXXX)"
              class="w-full bg-slate-50 border-2 border-emerald-200 rounded-xl px-4 py-3.5 text-slate-900 font-mono text-base focus:outline-none focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100 transition-all"
              autofocus
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kondisi Laptop</label>
              <select
                v-model="conditionInput"
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100 transition-all font-medium"
              >
                <option value="good">Baik (Normal)</option>
                <option value="damaged">Rusak / Bermasalah</option>
                <option value="lost">Hilang</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Catatan (Opsional)</label>
              <input
                v-model="notesInput"
                type="text"
                placeholder="e.g. Charger tertinggal"
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 text-sm focus:outline-none focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100 transition-all"
              />
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition-all disabled:opacity-50"
          >
            {{ loading ? 'Memproses Pengembalian...' : 'Proses Scan Pengembalian' }}
          </button>
        </form>
      </div>

      <!-- Right: Web Camera Scanner -->
      <CameraScanner @scan="handleCameraScan" />
    </div>

    <!-- Today's Return History Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-900">Riwayat Pengembalian Hari Ini</h3>
        <span class="text-xs text-slate-500 font-mono font-medium">{{ returns.length }} Transaksi</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700">
          <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
            <tr>
              <th class="px-4 py-3">NIS</th>
              <th class="px-4 py-3">Nama Siswa</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Waktu Kembalikan</th>
              <th class="px-4 py-3">Kondisi</th>
              <th class="px-4 py-3">Catatan</th>
              <th class="px-4 py-3">Petugas</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="returns.length === 0">
              <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs font-medium">
                Belum ada transaksi pengembalian laptop hari ini.
              </td>
            </tr>
            <tr v-for="ret in returns" :key="ret.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 font-mono text-indigo-600 font-semibold text-xs">{{ ret.loan?.student?.nis }}</td>
              <td class="px-4 py-3 font-bold text-slate-900">{{ ret.loan?.student?.name }}</td>
              <td class="px-4 py-3 text-slate-600">{{ ret.loan?.student?.class_name }}</td>
              <td class="px-4 py-3 text-xs text-slate-500 font-medium">{{ formatDate(ret.returned_at) }}</td>
              <td class="px-4 py-3">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase" :class="ret.condition === 'good' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                  {{ ret.condition }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-slate-500 font-medium">{{ ret.notes || '-' }}</td>
              <td class="px-4 py-3 text-xs text-slate-500 font-medium">{{ ret.officer?.name || '-' }}</td>
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
    return // Avoid duplicate submit within 2.5 seconds
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
