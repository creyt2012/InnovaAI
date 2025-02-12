import { ref, onMounted, watch } from 'vue'

const isDark = ref(false)

export function useTheme() {
  onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark')
  })

  watch(isDark, (value) => {
    document.documentElement.classList.toggle('dark', value)
    localStorage.theme = value ? 'dark' : 'light'
  })

  function toggleTheme() {
    isDark.value = !isDark.value
  }

  return {
    isDark,
    toggleTheme
  }
} 