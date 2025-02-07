import { ref } from 'vue'

export function useVoiceInput(onResult) {
  const isRecording = ref(false)
  let recognition = null

  const initRecognition = () => {
    if (!('webkitSpeechRecognition' in window)) {
      alert('Speech recognition is not supported in this browser.')
      return false
    }

    recognition = new webkitSpeechRecognition()
    recognition.continuous = true
    recognition.interimResults = true
    recognition.lang = 'vi-VN' // Có thể thay đổi theo ngôn ngữ hiện tại

    recognition.onresult = (event) => {
      const result = Array.from(event.results)
        .map(result => result[0])
        .map(result => result.transcript)
        .join('')

      onResult(result)
    }

    recognition.onerror = (event) => {
      console.error('Speech recognition error:', event.error)
      stopVoiceInput()
    }

    recognition.onend = () => {
      stopVoiceInput()
    }

    return true
  }

  const startVoiceInput = () => {
    if (!recognition && !initRecognition()) return

    try {
      recognition.start()
      isRecording.value = true
    } catch (error) {
      console.error('Failed to start voice input:', error)
    }
  }

  const stopVoiceInput = () => {
    if (recognition) {
      recognition.stop()
      isRecording.value = false
    }
  }

  return {
    isRecording,
    startVoiceInput,
    stopVoiceInput
  }
} 