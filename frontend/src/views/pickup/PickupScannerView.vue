<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-indigo-50 via-slate-50 to-cyan-50 border border-indigo-100 rounded-2xl p-6 shadow-sm">
      <div class="flex items-center space-x-3">
        <div class="p-3 bg-indigo-600 text-white rounded-xl shadow-md shadow-indigo-600/20">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </div>
        <div>
          <h2 class="text-xl font-bold text-slate-900">Sesi Scan Pengambilan Laptop (Pagi 07.30 - 09.00)</h2>
          <p class="text-xs text-indigo-700 font-medium">Pindai QR Code siswa untuk mencatat pengambilan laptop di pagi hari</p>
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
        <h4 class="font-bold text-base">{{ lastResult.success ? 'BERHASIL DICATAT!' : 'PERINGATAN SCAN GAGAL' }}</h4>
        <p class="text-sm mt-0.5 font-medium">{{ lastResult.message }}</p>
        <div v-if="lastResult.data" class="mt-2 pt-2 border-t border-emerald-200 grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
          <div><span class="text-emerald-700 font-semibold">NIS:</span> {{ lastResult.data.student?.nis }}</div>
          <div><span class="text-emerald-700 font-semibold">Nama:</span> {{ lastResult.data.student?.name }}</div>
          <div><span class="text-emerald-700 font-semibold">Kelas:</span> {{ lastResult.data.student?.class_name }}</div>
        </div>
      </div>
    </div>

    <!-- Scanner Input Controls Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Left: USB Barcode / Text Input Scanner -->
      <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center space-x-2">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
              class="w-full bg-slate-50 border-2 border-indigo-200 rounded-xl px-4 py-3.5 text-slate-900 font-mono text-base focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all"
              autofocus
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kode Aset Laptop (Opsional)</label>
            <input
              v-model="laptopAssetInput"
              type="text"
              placeholder="e.g. LAP-001 (Kosongkan jika otomatis)"
              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition-all disabled:opacity-50"
          >
            {{ loading ? 'Memproses Scan...' : 'Proses Scan Pengambilan' }}
          </button>
        </form>
      </div>

      <!-- Right: Web Camera Scanner -->
      <CameraScanner @scan="handleCameraScan" />
    </div>

    <!-- Today's Pickup History Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-900">Riwayat Pengambilan Hari Ini</h3>
        <span class="text-xs text-slate-500 font-mono font-medium">{{ pickups.length }} Transaksi</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700">
          <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
            <tr>
              <th class="px-4 py-3">NIS</th>
              <th class="px-4 py-3">Nama Siswa</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Kode Laptop</th>
              <th class="px-4 py-3">Waktu Ambil</th>
              <th class="px-4 py-3">Petugas</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="pickups.length === 0">
              <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs font-medium">
                Belum ada transaksi pengambilan laptop hari ini.
              </td>
            </tr>
            <tr v-for="loan in pickups" :key="loan.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 font-mono text-indigo-600 font-semibold text-xs">{{ loan.student?.nis }}</td>
              <td class="px-4 py-3 font-bold text-slate-900">{{ loan.student?.name }}</td>
              <td class="px-4 py-3 text-slate-600">{{ loan.student?.class_name }}</td>
              <td class="px-4 py-3 font-mono text-amber-700 font-semibold text-xs">{{ loan.laptop?.asset_code || '-' }}</td>
              <td class="px-4 py-3 text-xs text-slate-500 font-medium">{{ formatDate(loan.taken_at) }}</td>
              <td class="px-4 py-3 text-xs text-slate-500 font-medium">{{ loan.officer?.name || '-' }}</td>
              <td class="px-4 py-3">
                <StatusBadge :status="loan.status" />
              </td>
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
import StatusBadge from '@/components/common/StatusBadge.vue'

const monitoringStore = useMonitoringStore()

const qrInputRef = ref(null)
const qrTokenInput = ref('')
const laptopAssetInput = ref('')
const loading = ref(false)
const lastResult = ref(null)

let lastSubmittedToken = ''
let lastSubmittedTime = 0

const pickups = computed(() => monitoringStore.pickups)

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
    const res = await monitoringStore.processPickup(token, laptopAssetInput.value?.trim() || null)
    lastResult.value = res
    qrTokenInput.value = ''
    laptopAssetInput.value = ''
    await monitoringStore.fetchPickups()
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
  monitoringStore.fetchPickups()
  if (qrInputRef.value) qrInputRef.value.focus()
})
</script>
