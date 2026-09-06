<template>
  <span
    :class="[
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider',
      badgeClass
    ]"
  >
    <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current animate-pulse"></span>
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
      return 'bg-sky-50 text-sky-700 border border-sky-200'
    case 'RETURNED':
    case 'DIKEMBALIKAN':
    case 'AVAILABLE':
    case 'SENT':
      return 'bg-emerald-50 text-emerald-700 border border-emerald-200'
    case 'OVERDUE':
    case 'TERLAMBAT':
    case 'FAILED':
      return 'bg-rose-50 text-rose-700 border border-rose-200'
    case 'PENDING':
    case 'MAINTENANCE':
      return 'bg-amber-50 text-amber-700 border border-amber-200'
    default:
      return 'bg-slate-100 text-slate-600 border border-slate-200'
  }
})

const label = computed(() => {
  switch (props.status?.toUpperCase()) {
    case 'BORROWED': return 'Sedang Dipinjam'
    case 'RETURNED': return 'Dikembalikan'
    case 'OVERDUE': return 'Terlambat'
    case 'AVAILABLE': return 'Tersedia'
    case 'MAINTENANCE': return 'Perbaikan'
    case 'SENT': return 'Terkirim'
    case 'FAILED': return 'Gagal'
    case 'PENDING': return 'Menunggu'
    default: return props.status
  }
})
</script>
