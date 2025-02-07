import { defineStore } from 'pinia'
import axios from 'axios'

export const useAdminStore = defineStore('admin', {
  state: () => ({
    stats: {
      users: 0,
      chats: 0,
      servers: 0,
      models: 0
    },
    recentActivity: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchDashboardData() {
      this.loading = true
      try {
        const response = await axios.get('/api/admin/dashboard')
        this.stats = response.data.stats
        this.recentActivity = response.data.recentActivity
      } catch (error) {
        this.error = error.message
      } finally {
        this.loading = false
      }
    }
  }
}) 