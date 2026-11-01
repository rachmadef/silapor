<template>
  <span
    :class="[
      'inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wide',
      badgeClass
    ]"
  >
    <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
})

const badgeClass = computed(() => {
  switch (props.status?.toUpperCase()) {
    case 'BORROWED':
    case 'DIAMBIL':
      return 'bg-amber-50 text-amber-800 border border-amber-200/80'
    case 'RETURNED':
    case 'DIKEMBALIKAN':
    case 'AVAILABLE':
    case 'SENT':
    case 'ACTIVE':
      return 'bg-emerald-50 text-emerald-800 border border-emerald-200/80'
    case 'OVERDUE':
    case 'TERLAMBAT':
    case 'FAILED':
      return 'bg-rose-50 text-rose-800 border border-rose-200/80'
    case 'PENDING':
    case 'MAINTENANCE':
      return 'bg-stone-100 text-stone-700 border border-stone-200'
    default:
      return 'bg-stone-100 text-stone-600 border border-stone-200'
  }
})

const label = computed(() => {
  switch (props.status?.toUpperCase()) {
    case 'BORROWED': return 'Sedang Dipakai'
    case 'RETURNED': return 'Sudah Kembali'
    case 'OVERDUE': return 'Belum Kembali'
    case 'AVAILABLE': return 'Tersedia'
    case 'MAINTENANCE': return 'Perbaikan'
    case 'SENT': return 'WA Terkirim'
    case 'FAILED': return 'Gagal Kirim'
    case 'PENDING': return 'Menunggu'
    case 'ACTIVE': return 'Aktif'
    default: return props.status
  }
})
</script>
