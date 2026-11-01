<template>
  <div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 rounded-2xl bg-teal-700 text-white flex items-center justify-center shrink-0 shadow-sm shadow-teal-900/15">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </div>
        <div>
          <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Sesi Pagi • 07.30 – 09.00 WIB</span>
          <h2 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight">Scan Pengambilan Laptop</h2>
          <p class="text-xs sm:text-sm text-stone-500 mt-0.5">Pindai kartu QR siswa atau ketik NIS untuk mencatat pengambilan laptop</p>
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
          <h4 class="font-extrabold text-sm">{{ lastResult.success ? 'Pengambilan Berhasil Dicatat' : 'Peringatan Scan' }}</h4>
          <p class="text-xs mt-0.5 font-medium">{{ lastResult.message }}</p>
          <div v-if="lastResult.data" class="mt-3 pt-2.5 border-t border-emerald-200/80 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
            <div><span class="text-emerald-700 font-semibold">NIS:</span> {{ lastResult.data.student?.nis }}</div>
            <div><span class="text-emerald-700 font-semibold">Nama:</span> {{ lastResult.data.student?.name }}</div>
            <div><span class="text-emerald-700 font-semibold">Kelas:</span> {{ lastResult.data.student?.class_name }}</div>
          </div>
        </div>
        <button @click="lastResult = null" class="p-1 text-stone-400 hover:text-stone-700 rounded-lg cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </Transition>

    <!-- Scanner Input Controls Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Left: USB Barcode / Text Input Scanner -->
      <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs space-y-5">
        <div>
          <h3 class="text-base font-extrabold text-stone-900">Scanner USB / Input Token & NIS</h3>
          <p class="text-xs text-stone-500 mt-0.5">Kursor otomatis aktif pada kolom di bawah untuk alat scanner 2D</p>
        </div>

        <form @submit.prevent="submitScan" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-stone-700 mb-2">Token QR Siswa atau NIS</label>
            <input
              ref="qrInputRef"
              v-model="qrTokenInput"
              type="text"
              required
              placeholder="Arahkan scanner atau ketik NIS (misal: 2026001)..."
              class="w-full bg-[#FAF8F5] border-2 border-stone-200 rounded-2xl px-4 py-3.5 text-stone-900 font-mono text-sm focus:outline-none focus:border-teal-700 focus:bg-white transition-all"
              autofocus
            />
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="block text-xs font-bold text-stone-700">Kode Aset Laptop</label>
              <span class="text-[11px] text-stone-400">Otomatis sesuai data siswa</span>
            </div>
            <input
              v-model="laptopAssetInput"
              type="text"
              placeholder="Kosongkan agar otomatis memilih laptop milik siswa"
              class="w-full bg-[#FAF8F5] border border-stone-200 rounded-2xl px-4 py-3 text-stone-900 text-xs focus:outline-none focus:border-teal-700 focus:bg-white transition-all"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3.5 px-4 rounded-2xl bg-teal-700 hover:bg-teal-800 text-white font-bold text-sm shadow-sm shadow-teal-900/15 transition-all disabled:opacity-50 cursor-pointer"
          >
            {{ loading ? 'Memproses...' : 'Catat Pengambilan Laptop' }}
          </button>
        </form>
      </div>

      <!-- Right: Web Camera Scanner -->
      <CameraScanner @scan="handleCameraScan" />
    </div>

    <!-- Today's Pickup History Table -->
    <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-base font-extrabold text-stone-900">Riwayat Pengambilan Pagi Ini</h3>
          <p class="text-xs text-stone-500">Daftar siswa yang telah mengambil laptop hari ini</p>
        </div>
        <span class="px-3.5 py-1.5 rounded-full bg-[#FAF8F5] border border-stone-200 text-xs text-stone-700 font-mono font-bold">
          {{ pickups.length }} Siswa
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-stone-700">
          <thead class="bg-[#FAF8F5] text-[11px] font-bold text-stone-500 uppercase tracking-wider border-y border-stone-200/80">
            <tr>
              <th class="px-4 py-3.5">Peserta Didik</th>
              <th class="px-4 py-3.5">Kelas</th>
              <th class="px-4 py-3.5">Laptop Siswa</th>
              <th class="px-4 py-3.5">Waktu Ambil</th>
              <th class="px-4 py-3.5">Petugas</th>
              <th class="px-4 py-3.5">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100">
            <tr v-if="pickups.length === 0">
              <td colspan="6" class="px-4 py-10 text-center text-stone-400 text-xs font-medium">
                Belum ada siswa yang melakukan scan pengambilan laptop pagi ini.
              </td>
            </tr>
            <tr v-for="loan in pickups" :key="loan.id" class="hover:bg-[#FAF8F5]/80 transition-colors">
              <td class="px-4 py-3.5">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/60 font-bold text-xs flex items-center justify-center shrink-0">
                    {{ loan.student?.name?.charAt(0) || 'S' }}
                  </div>
                  <div>
                    <p class="font-bold text-stone-900">{{ loan.student?.name }}</p>
                    <p class="text-xs font-mono text-stone-500">NIS: {{ loan.student?.nis }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3.5 text-stone-600 text-xs font-semibold">{{ loan.student?.class_name }}</td>
              <td class="px-4 py-3.5 text-xs">
                <template v-if="loan.laptop">
                  <p class="font-bold text-stone-800">{{ loan.laptop.brand || '-' }}{{ loan.laptop.model ? ' ' + loan.laptop.model : '' }}</p>
                  <p class="font-mono text-teal-700 font-semibold text-[11px]">{{ loan.laptop.asset_code }}</p>
                </template>
                <span v-else class="text-stone-400 font-mono">-</span>
              </td>
              <td class="px-4 py-3.5 text-xs text-stone-600 font-mono">{{ formatDate(loan.taken_at) }}</td>
              <td class="px-4 py-3.5 text-xs text-stone-500 font-medium">{{ loan.officer?.name || '-' }}</td>
              <td class="px-4 py-3.5">
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
    return
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
