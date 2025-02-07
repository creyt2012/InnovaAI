import { defineStore } from 'pinia'
import axios from 'axios'

export const useUserStore = defineStore('user', {
  state: () => ({
    users: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchUsers(params) {
      this.loading = true
      try {
        const response = await axios.get('/api/admin/users', { params })
        this.users = response.data.data
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async createUser(userData) {
      try {
        const response = await axios.post('/api/admin/users', userData)
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    async updateUser(id, userData) {
      try {
        const response = await axios.put(`/api/admin/users/${id}`, userData)
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    async deleteUser(id) {
      try {
        await axios.delete(`/api/admin/users/${id}`)
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    async banUser(id) {
      try {
        const response = await axios.post(`/api/admin/users/${id}/ban`)
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    async unbanUser(id) {
      try {
        const response = await axios.post(`/api/admin/users/${id}/unban`)
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      }
    }
  }
}) 