<template>
  <div class="relative">
    <button
      @click="isOpen = !isOpen"
      class="flex items-center space-x-2 text-gray-700 hover:text-gray-900"
      @blur="closeMenu"
    >
      <GlobeAltIcon class="w-5 h-5" />
      <span class="text-sm">{{ currentLanguage }}</span>
      <ChevronDownIcon class="w-4 h-4" />
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-lg z-50"
    >
      <div v-if="detecting" class="px-4 py-2 text-sm text-gray-500">
        {{ $page.props.language.common.detecting }}...
      </div>
      <template v-else>
        <a
          v-for="lang in languages"
          :key="lang.code"
          :href="route('language.switch', { locale: lang.code })"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          :class="{ 'bg-gray-50': lang.code === currentLocale }"
        >
          <div class="flex items-center justify-between">
            <span>{{ lang.name }}</span>
            <CheckIcon v-if="lang.code === currentLocale" 
                      class="w-4 h-4 text-green-500" />
          </div>
        </a>
        <div class="border-t my-1"></div>
        <button
          @click="detectLanguage"
          class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
        >
          {{ $page.props.language.common.detect_language }}
        </button>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { GlobeAltIcon, ChevronDownIcon, CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  currentLocale: {
    type: String,
    required: true
  }
})

const isOpen = ref(false)
const detecting = ref(false)

const languages = [
  { code: 'en', name: 'English' },
  { code: 'vi', name: 'Tiếng Việt' }
]

const currentLanguage = computed(() => {
  return languages.find(lang => lang.code === props.currentLocale)?.name
})

const closeMenu = () => {
  // Delay để cho phép click hoạt động
  setTimeout(() => {
    isOpen.value = false
  }, 200)
}

const detectLanguage = async () => {
  detecting.value = true
  try {
    const response = await axios.get(route('language.detect'))
    const detectedLocale = response.data.locale
    
    if (detectedLocale !== props.currentLocale) {
      window.location = route('language.switch', { locale: detectedLocale })
    }
  } catch (error) {
    console.error('Language detection failed:', error)
  } finally {
    detecting.value = false
    isOpen.value = false
  }
}

// Auto detect on first visit
onMounted(() => {
  if (!document.cookie.includes('locale=')) {
    detectLanguage()
  }
})
</script> 