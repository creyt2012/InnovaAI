import { defineStore } from 'pinia'
import axios from 'axios'

export const useBankAccountStore = defineStore('bankAccount', {
  state: () => ({
    bankAccounts: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchBankAccounts() {
      this.loading = true
      try {
        const response = await axios.get('/api/admin/bank-accounts')
        this.bankAccounts = response.data
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Thêm các actions khác...
  }
}) 