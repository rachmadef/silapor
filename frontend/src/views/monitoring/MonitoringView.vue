<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
      <div>
        <h2 class="text-xl font-bold text-slate-900">Monitoring Transaksi Real-time</h2>
        <p class="text-xs text-slate-500 mt-1">Pantau seluruh status pengambilan dan pengembalian laptop siswa per tanggal & kelas</p>
      </div>

      <!-- Filters -->
      <div class="flex items-center space-x-3">
        <input
          v-model="selectedDate"
          type="date"
          @change="loadData"
          class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 text-xs focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all font-medium"
        />
        <button
          @click="loadData"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-600/20"
        >
          Filter Data
        </button>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700">
          <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
            <tr>
              <th class="px-4 py-3">NIS</th>
              <th class="px-4 py-3">Nama Siswa</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Laptop</th>
              <th class="px-4 py-3">Diambil (Pagi)</th>
              <th class="px-4 py-3">Dikembalikan (Siang)</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="pickups.length === 0">
              <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs font-medium">
                Tidak ada transaksi tercatat pada tanggal terpilih.
              </td>
            </tr>
            <tr v-for="loan in pickups" :key="loan.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 font-mono text-indigo-600 font-semibold text-xs">{{ loan.student?.nis }}</td>
              <td class="px-4 py-3 font-bold text-slate-900">{{ loan.student?.name }}</td>
              <td class="px-4 py-3 text-slate-600">{{ loan.student?.class_name }}</td>
              <td class="px-4 py-3 font-mono text-amber-700 font-semibold text-xs">{{ loan.laptop?.asset_code || '-' }}</td>
              <td class="px-4 py-3 text-xs text-slate-500 font-medium">{{ formatDate(loan.taken_at) }}</td>
              <td class="px-4 py-3 text-xs text-slate-500 font-medium">{{ loan.return_record ? formatDate(loan.return_record.returned_at) : '-' }}</td>
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
