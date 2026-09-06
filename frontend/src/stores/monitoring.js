import { defineStore } from 'pinia'
import api from '@/services/api'

export const useMonitoringStore = defineStore('monitoring', {
  state: () => ({
    stats: {
      total_students: 0,
      total_taken: 0,
      total_returned: 0,
      total_borrowed: 0,
      total_overdue: 0,
      percentage_returned: 0,
      total_notifications_sent: 0,
      overdue_list: [],
    },
    pickups: [],
    returns: [],
    loading: false,
    lastScanResult: null,
  }),

  actions: {
    async fetchStats(date = null) {
      this.loading = true
      try {
        const response = await api.get('/dashboard/stats', {
          params: date ? { date } : {},
        })
        this.stats = response.data.data
        return this.stats
      } catch (err) {
        console.error('Error fetching stats:', err)
      } finally {
        this.loading = false
      }
    },

    async fetchPickups(params = {}) {
      try {
        const response = await api.get('/pickups/history', { params })
        this.pickups = response.data.data
        return this.pickups
      } catch (err) {
        console.error('Error fetching pickups:', err)
      }
    },

    async fetchReturns(params = {}) {
      try {
        const response = await api.get('/returns/history', { params })
        this.returns = response.data.data
        return this.returns
      } catch (err) {
        console.error('Error fetching returns:', err)
      }
    },

    async processPickup(qrToken, laptopAssetCode = null) {
      this.loading = true
      this.lastScanResult = null
      try {
        const response = await api.post('/pickups/scan', {
          qr_token: qrToken,
          laptop_asset_code: laptopAssetCode,
        })
        this.lastScanResult = {
          success: true,
          message: response.data.message,
          data: response.data.data,
        }
        await this.fetchStats()
        return this.lastScanResult
      } catch (err) {
        this.lastScanResult = {
          success: false,
          message: err.response?.data?.message || 'Gagal memproses scan pengambilan.',
        }
        throw this.lastScanResult
      } finally {
        this.loading = false
      }
    },

    async processReturn(qrToken, condition = 'good', notes = null) {
      this.loading = true
      this.lastScanResult = null
      try {
        const response = await api.post('/returns/scan', {
          qr_token: qrToken,
          condition,
          notes,
        })
        this.lastScanResult = {
          success: true,
          message: response.data.message,
          data: response.data.data,
        }
        await this.fetchStats()
        return this.lastScanResult
      } catch (err) {
        this.lastScanResult = {
          success: false,
          message: err.response?.data?.message || 'Gagal memproses scan pengembalian.',
        }
        throw this.lastScanResult
      } finally {
        this.loading = false
      }
    },

    async triggerCheckOverdue() {
      try {
        const response = await api.post('/notifications/check-overdue')
        await this.fetchStats()
        return response.data
      } catch (err) {
        throw err
      }
    },
  },
})
