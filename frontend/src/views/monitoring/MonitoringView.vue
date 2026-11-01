<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Pemantauan Harian</span>
        <h2 class="text-xl sm:text-2xl font-extrabold text-stone-900 tracking-tight mt-0.5">Pantauan Transaksi Real-time</h2>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">Lihat riwayat pengambilan pagi dan pengembalian siang berdasarkan tanggal</p>
      </div>

      <!-- Filters -->
      <div class="flex items-center space-x-2.5">
        <input
          v-model="selectedDate"
          type="date"
          @change="loadData"
          class="bg-[#FAF8F5] border border-stone-200 rounded-2xl px-3.5 py-2.5 text-stone-900 text-xs focus:outline-none focus:border-teal-700 focus:bg-white transition-all font-semibold cursor-pointer"
        />
        <button
          @click="loadData"
          class="px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-2xl transition-all shadow-sm shadow-teal-900/15 cursor-pointer"
        >
          Tampilkan
        </button>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-stone-200/90 rounded-3xl p-6 sm:p-7 shadow-2xs space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-stone-700">
          <thead class="bg-[#FAF8F5] text-[11px] font-bold text-stone-500 uppercase tracking-wider border-y border-stone-200/80">
            <tr>
              <th class="px-4 py-3.5">Peserta Didik</th>
              <th class="px-4 py-3.5">Kelas</th>
              <th class="px-4 py-3.5">Perangkat Laptop</th>
              <th class="px-4 py-3.5">Diambil (Pagi)</th>
              <th class="px-4 py-3.5">Dikembalikan (Siang)</th>
              <th class="px-4 py-3.5">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100">
            <tr v-if="pickups.length === 0">
              <td colspan="6" class="px-4 py-10 text-center text-stone-400 text-xs font-medium">
                Tidak ada transaksi tercatat pada tanggal yang dipilih.
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
              <td class="px-4 py-3.5 text-xs text-stone-600 font-mono">{{ loan.return_record ? formatDate(loan.return_record.returned_at) : '-' }}</td>
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
import StatusBadge from '@/components/common/StatusBadge.vue'

const monitoringStore = useMonitoringStore()

const selectedDate = ref(new Date().toISOString().split('T')[0])
const pickups = computed(() => monitoringStore.pickups)

const loadData = () => {
  monitoringStore.fetchPickups({ date: selectedDate.value })
}

const formatDate = (isoStr) => {
  if (!isoStr) return '-'
  return new Date(isoStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
}

onMounted(() => {
  loadData()
})
</script>
