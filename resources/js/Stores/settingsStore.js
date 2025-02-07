import { defineStore } from 'pinia'
import axios from 'axios'

export const useSettingsStore = defineStore('settings', {
  state: () => ({
    settings: {
      app_name: 'InnovaAI',
      logo: '/images/logo.svg',
      hero_title: 'Next Generation AI Chat Platform',
      hero_description: 'Experience the power of advanced AI models with our intuitive chat interface.',
      features: [
        {
          title: 'Multiple AI Models',
          description: 'Access various AI models from one platform',
          icon: 'CpuChipIcon'
        },
        {
          title: 'Real-time Chat',
          description: 'Instant responses with WebSocket support',
          icon: 'ChatBubbleLeftRightIcon'
        },
        {
          title: 'Advanced Features',
          description: 'Code highlighting, file sharing, and more',
          icon: 'SparklesIcon'
        }
      ]
    },
    loading: false,
    error: null
  }),

  actions: {
    async fetchSettings() {
      this.loading = true
      try {
        const response = await axios.get('/api/settings')
        this.settings = response.data.settings
      } catch (error) {
        this.error = error.message
      } finally {
        this.loading = false
      }
    },

    async updateSettings(settings) {
      try {
        const response = await axios.post('/api/settings', settings)
        this.settings = response.data.settings
        return true
      } catch (error) {
        this.error = error.message
        return false
      }
    }
  }
}) 