import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'

export function useChat(initialChat) {
  const messages = ref(initialChat.messages || [])
  const newMessage = ref('')
  const isSending = ref(false)
  const isTyping = ref(false)
  const loadingMessageId = ref(null)

  const settings = ref({
    model: initialChat.model || 'gpt-3.5-turbo',
    temperature: initialChat.temperature || 0.7,
    maxTokens: initialChat.max_tokens || 2000
  })

  const availableModels = [
    { value: 'gpt-3.5-turbo', label: 'GPT-3.5 Turbo' },
    { value: 'gpt-4', label: 'GPT-4' },
    { value: 'claude-2', label: 'Claude 2' },
    { value: 'llama-2', label: 'Llama 2' }
  ]

  const sendMessage = async () => {
    if (!newMessage.value.trim() || isSending.value) return

    const messageContent = newMessage.value
    newMessage.value = ''
    isSending.value = true

    // Add user message immediately
    const userMessage = {
      id: Date.now(),
      role: 'user',
      content: messageContent,
      created_at: new Date().toISOString()
    }
    messages.value.push(userMessage)

    // Add temporary assistant message
    const assistantMessage = {
      id: Date.now() + 1,
      role: 'assistant',
      content: '',
      created_at: new Date().toISOString()
    }
    messages.value.push(assistantMessage)
    loadingMessageId.value = assistantMessage.id

    try {
      const response = await axios.post(route('chat.message', initialChat.id), {
        message: messageContent,
        settings: settings.value
      })

      // Update assistant message with response
      const index = messages.value.findIndex(m => m.id === assistantMessage.id)
      if (index !== -1) {
        messages.value[index] = {
          ...assistantMessage,
          ...response.data.message
        }
      }
    } catch (error) {
      // Handle error
      const index = messages.value.findIndex(m => m.id === assistantMessage.id)
      if (index !== -1) {
        messages.value[index] = {
          ...assistantMessage,
          content: 'Sorry, there was an error processing your request.',
          error: true
        }
      }
    } finally {
      isSending.value = false
      loadingMessageId.value = null
    }
  }

  const retryMessage = async (message) => {
    const index = messages.value.findIndex(m => m.id === message.id)
    if (index === -1) return

    loadingMessageId.value = message.id
    messages.value[index] = { ...message, error: false }

    try {
      const response = await axios.post(route('chat.retry', {
        chat: initialChat.id,
        message: message.id
      }))

      messages.value[index] = response.data.message
    } catch (error) {
      messages.value[index] = { ...message, error: true }
    } finally {
      loadingMessageId.value = null
    }
  }

  const editMessage = async (message) => {
    const index = messages.value.findIndex(m => m.id === message.id)
    if (index === -1) return

    try {
      const response = await axios.put(route('chat.message.update', {
        chat: initialChat.id,
        message: message.id
      }), {
        content: message.content
      })

      messages.value[index] = response.data.message
    } catch (error) {
      // Handle error
    }
  }

  const deleteMessage = async (message) => {
    const index = messages.value.findIndex(m => m.id === message.id)
    if (index === -1) return

    try {
      await axios.delete(route('chat.message.destroy', {
        chat: initialChat.id,
        message: message.id
      }))

      messages.value.splice(index, 1)
    } catch (error) {
      // Handle error
    }
  }

  const saveSettings = async () => {
    try {
      await axios.put(route('chat.settings', initialChat.id), settings.value)
      return true
    } catch (error) {
      return false
    }
  }

  return {
    messages,
    newMessage,
    isSending,
    isTyping,
    loadingMessageId,
    settings,
    availableModels,
    sendMessage,
    retryMessage,
    editMessage,
    deleteMessage,
    saveSettings
  }
} 