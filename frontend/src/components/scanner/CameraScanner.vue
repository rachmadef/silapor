<template>
  <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-2">
        <div class="w-3 h-3 rounded-full" :class="isScanning ? 'bg-emerald-500 animate-ping' : 'bg-slate-300'"></div>
        <h3 class="text-sm font-bold text-slate-800">Scanner Kamera Web/HP</h3>
      </div>
      <button
        @click="toggleScanner"
        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center shadow-xs"
        :class="isScanning ? 'bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100' : 'bg-indigo-600 text-white hover:bg-indigo-700'"
      >
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path v-if="!isScanning" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ isScanning ? 'Matikan Kamera' : 'Buka Kamera Scan' }}
      </button>
    </div>

    <!-- Reader Container -->
    <div id="qr-reader-container" class="relative rounded-xl overflow-hidden bg-slate-900 min-h-[250px] border border-slate-200 flex items-center justify-center">
      <div v-if="!isScanning" class="text-center p-6 text-slate-400 space-y-2">
        <svg class="w-12 h-12 mx-auto text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
        </svg>
        <p class="text-xs text-slate-300">Klik <b class="text-indigo-400">"Buka Kamera Scan"</b> di atas untuk mengaktifkan kamera HP / Laptop</p>
      </div>

      <div id="qr-reader" class="w-full"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onUnmounted } from 'vue'
import { Html5Qrcode } from 'html5-qrcode'

const emit = defineEmits(['scan'])

const isScanning = ref(false)
let html5QrCode = null
let lastDecodedText = ''
let lastDecodedTime = 0

const startScanner = async () => {
  try {
    html5QrCode = new Html5Qrcode('qr-reader')
    await html5QrCode.start(
      { facingMode: 'environment' },
      {
        fps: 10,
        qrbox: { width: 220, height: 220 },
      },
      (decodedText) => {
        const now = Date.now()
        if (decodedText === lastDecodedText && now - lastDecodedTime < 2500) {
          return // Skip repeated duplicate scans within 2.5s
        }
        lastDecodedText = decodedText
        lastDecodedTime = now
        emit('scan', decodedText)
      },
      (errorMessage) => {
        // ignore scan error
      }
    )
    isScanning.value = true
  } catch (err) {
    console.error('Gagal membuka kamera:', err)
    alert('Gagal mengakses kamera browser. Pastikan izin kamera telah diberikan.')
    isScanning.value = false
  }
}

const stopScanner = async () => {
  if (html5QrCode && isScanning.value) {
    try {
      await html5QrCode.stop()
      html5QrCode.clear()
    } catch (err) {
      console.error('Error stopping scanner:', err)
    } finally {
      isScanning.value = false
    }
  }
}

const toggleScanner = () => {
  if (isScanning.value) {
    stopScanner()
  } else {
    startScanner()
  }
}

onUnmounted(() => {
  stopScanner()
})
</script>
