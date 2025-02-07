import { defineStore } from 'pinia'
import axios from 'axios'

export const usePackageStore = defineStore('package', {
  state: () => ({
    packages: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchPackages(params) {
      this.loading = true
      try {
        const response = await axios.get('/api/admin/packages', { params })
        this.packages = response.data.data
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchActivePackages() {
      try {
        const response = await axios.get('/api/packages')
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    async createPackage(packageData) {
      try {
        const response = await axios.post('/api/admin/packages', packageData)
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    async updatePackage(id, packageData) {
      try {
        const response = await axios.put(`/api/admin/packages/${id}`, packageData)
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    async deletePackage(id) {
      try {
        await axios.delete(`/api/admin/packages/${id}`)
      } catch (error) {
        this.error = error.message
        throw error
      }
    }
  }
}) 