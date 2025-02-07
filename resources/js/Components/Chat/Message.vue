<template>
  <div 
    :class="[
      'flex items-start space-x-4 p-6 rounded-2xl transition-all duration-300',
      message.role === 'assistant' ? 
        'glass-panel hover:shadow-soft-xl' : 
        'hover:bg-gray-50 dark:hover:bg-gray-800/50'
    ]"
  >
    <!-- Avatar -->
    <div class="flex-shrink-0">
      <div 
        :class="[
          'h-10 w-10 rounded-xl overflow-hidden',
          'ring-2 ring-offset-2 dark:ring-offset-gray-900',
          message.role === 'assistant' ? 
            'ring-primary-500/30 bg-primary-500' :
            'ring-gray-200 dark:ring-gray-700'
        ]"
      >
        <img
          v-if="message.role === 'user'"
          :src="userAvatar"
          class="h-full w-full object-cover"
          alt="User Avatar"
        />
        <div 
          v-else
          class="h-full w-full flex items-center justify-center"
        >
          <SparklesIcon class="h-6 w-6 text-white" />
        </div>
      </div>
    </div>

    <!-- Message Content -->
    <div class="flex-1 space-y-3">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <span class="font-medium text-gray-900 dark:text-white">
            {{ message.role === 'user' ? userName : 'AI Assistant' }}
          </span>
          <span 
            v-if="message.role === 'assistant'"
            class="px-2 py-1 text-xs font-medium text-primary-600 dark:text-primary-400 
                   bg-primary-50 dark:bg-primary-900/20 rounded-full"
          >
            {{ message.model || 'GPT-3.5' }}
          </span>
        </div>
        <time 
          :datetime="message.created_at"
          class="text-xs text-gray-500 dark:text-gray-400"
        >
          {{ formatTime(message.created_at) }}
        </time>
      </div>

      <!-- Content -->
      <div class="prose dark:prose-invert max-w-none">
        <div v-if="isEditing" class="space-y-3">
          <textarea
            v-model="editedContent"
            class="elegant-input"
            rows="4"
          />
          <div class="flex justify-end space-x-2">
            <button
              class="glass-button text-sm"
              @click="cancelEdit"
            >
              {{ $t('common.cancel') }}
            </button>
            <button
              class="glass-button text-sm text-primary-600 dark:text-primary-400"
              @click="saveEdit"
            >
              {{ $t('common.save') }}
            </button>
          </div>
        </div>
        <div v-else>
          <div 
            v-if="isLoading"
            class="space-y-2 animate-pulse"
          >
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-full w-3/4"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-full w-1/2"></div>
          </div>
          <div 
            v-else 
            class="message-content"
            v-html="formattedContent"
          ></div>
        </div>
      </div>

      <!-- Attachments -->
      <div 
        v-if="message.attachments?.length"
        class="flex flex-wrap gap-2"
      >
        <div
          v-for="attachment in message.attachments"
          :key="attachment.id"
          class="group flex items-center space-x-2 px-3 py-2 
                 bg-gray-50 dark:bg-gray-800/50 rounded-lg
                 border border-gray-200 dark:border-gray-700
                 hover:border-primary-500 dark:hover:border-primary-400
                 transition-all duration-200"
        >
          <DocumentIcon class="h-5 w-5 text-gray-400 group-hover:text-primary-500 
                             transition-colors duration-200" />
          <span class="text-sm text-gray-600 dark:text-gray-300">
            {{ attachment.filename }}
          </span>
          <button
            @click="downloadAttachment(attachment)"
            class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700
                   text-gray-400 hover:text-primary-500
                   transition-all duration-200"
          >
            <ArrowDownTrayIcon class="h-4 w-4" />
          </button>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center space-x-3 pt-2">
        <button
          v-if="message.role === 'user'"
          class="action-button"
          @click="startEdit"
        >
          <PencilIcon class="h-4 w-4" />
          <span>{{ $t('chat.edit') }}</span>
        </button>
        <button
          v-if="message.role === 'assistant' && message.error"
          class="action-button"
          @click="$emit('retry', message)"
        >
          <ArrowPathIcon class="h-4 w-4" />
          <span>{{ $t('chat.retry') }}</span>
        </button>
        <button
          class="action-button"
          @click="copyToClipboard"
        >
          <ClipboardIcon class="h-4 w-4" />
          <span>{{ copied ? $t('chat.copied') : $t('chat.copy') }}</span>
        </button>
        <button
          class="action-button text-red-500 hover:text-red-600 
                 dark:text-red-400 dark:hover:text-red-300"
          @click="$emit('delete', message)"
        >
          <TrashIcon class="h-4 w-4" />
          <span>{{ $t('chat.delete') }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { marked } from 'marked'
import hljs from 'highlight.js'
import {
  SparklesIcon,
  DocumentIcon,
  ArrowDownTrayIcon,
  PencilIcon,
  ArrowPathIcon,
  ClipboardIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'

const { t } = useI18n()

const props = defineProps({
  message: {
    type: Object,
    required: true
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['retry', 'edit', 'delete'])

// Setup markdown renderer
marked.setOptions({
  highlight: (code, lang) => {
    if (lang && hljs.getLanguage(lang)) {
      return hljs.highlight(code, { language: lang }).value
    }
    return hljs.highlightAuto(code).value
  },
  breaks: true
})

const isEditing = ref(false)
const editedContent = ref(props.message.content)
const copied = ref(false)

const formattedContent = computed(() => {
  return marked(props.message.content)
})

const startEdit = () => {
  isEditing.value = true
  editedContent.value = props.message.content
}

const cancelEdit = () => {
  isEditing.value = false
}

const saveEdit = () => {
  emit('edit', {
    ...props.message,
    content: editedContent.value
  })
  isEditing.value = false
}

const copyToClipboard = async () => {
  await navigator.clipboard.writeText(props.message.content)
  copied.value = true
  setTimeout(() => {
    copied.value = false
  }, 2000)
}

const downloadAttachment = (attachment) => {
  window.open(attachment.url, '_blank')
}

// Mock data - replace with real data from your auth system
const userAvatar = 'https://ui-avatars.com/api/?name=User'
const userName = 'User'
</script>

<style scoped>
.message-content :deep(pre) {
  @apply bg-gray-900 dark:bg-black/50 rounded-xl p-4 
         border border-gray-800 dark:border-gray-700
         shadow-inner overflow-x-auto;
}

.message-content :deep(code) {
  @apply font-mono text-sm;
}

.message-content :deep(p) {
  @apply leading-relaxed text-gray-600 dark:text-gray-300;
}

.message-content :deep(a) {
  @apply text-primary-600 dark:text-primary-400 
         hover:text-primary-700 dark:hover:text-primary-300
         underline decoration-2 underline-offset-2
         transition-colors duration-200;
}

.action-button {
  @apply inline-flex items-center space-x-1 px-2 py-1
         text-xs font-medium text-gray-500 
         hover:text-gray-700 dark:text-gray-400 
         dark:hover:text-gray-200
         rounded-lg hover:bg-gray-100 
         dark:hover:bg-gray-800
         transition-all duration-200;
}
</style> 