import { defineStore } from 'pinia'
import axios from 'axios'

export const useModelStore = defineStore('model', {
  state: () => ({
    models: [],
    selectedModel: null,
    loading: false,
    error: null
  }),

  getters: {
    availableModels: (state) => state.models.filter(m => m.status === 'online'),
    modelsByCategory: (state) => {
      return state.models.reduce((groups, model) => {
        const group = groups[model.category] || []
        group.push(model)
        groups[model.category] = group
        return groups
      }, {})
    }
  },

  actions: {
    async fetchModels() {
      this.loading = true
      try {
        const response = await axios.get('/api/models')
        this.models = response.data.models
        
        // Auto-select first available model if none selected
        if (!this.selectedModel && this.availableModels.length) {
          this.selectedModel = this.availableModels[0]
        }
      } catch (error) {
        this.error = error.message
      } finally {
        this.loading = false
      }
    },

    async selectModel(model) {
      if (model.status === 'offline') return

      try {
        // Save preference to backend
        await axios.post('/api/user/preferences/model', {
          model_id: model.id
        })
        
        this.selectedModel = model
      } catch (error) {
        this.error = error.message
      }
    },

    async checkModelStatus(modelId) {
      try {
        const response = await axios.get(`/api/models/${modelId}/status`)
        const index = this.models.findIndex(m => m.id === modelId)
        if (index !== -1) {
          this.models[index] = {
            ...this.models[index],
            status: response.data.status,
            latency: response.data.latency
          }
        }
      } catch (error) {
        console.error(`Failed to check model status: ${error}`)
      }
    }
  }
}) 