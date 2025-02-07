<template>
  <UserLayout>
    <div class="flex h-screen overflow-hidden">
      <!-- Chat Content -->
      <div class="flex-1 flex flex-col">
        <!-- Chat Header -->
        <div class="border-b dark:border-gray-700 p-4 flex items-center justify-between">
          <div class="flex items-center">
            <h2 class="text-xl font-semibold dark:text-white">{{ chat.title }}</h2>
            <Badge 
              v-if="chat.model" 
              :text="chat.model"
              class="ml-2"
            />
          </div>
          <div class="flex items-center space-x-2">
            <Button
              variant="secondary"
              size="sm"
              @click="showSettings = true"
            >
              <CogIcon class="h-4 w-4 mr-1" />
              {{ $t('chat.settings') }}
            </Button>
            <Button
              variant="secondary"
              size="sm"
              @click="exportChat"
            >
              <ArrowDownTrayIcon class="h-4 w-4 mr-1" />
              {{ $t('chat.export') }}
            </Button>
          </div>
        </div>

        <!-- Messages -->
        <div 
          ref="messagesContainer"
          class="flex-1 overflow-y-auto p-4 space-y-4"
        >
          <TransitionGroup
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
          >
            <Message
              v-for="message in messages"
              :key="message.id"
              :message="message"
              :is-loading="message.id === loadingMessageId"
              @retry="retryMessage(message)"
              @edit="editMessage(message)"
              @delete="deleteMessage(message)"
            />
          </TransitionGroup>

          <div v-if="isTyping" class="flex items-center space-x-2 text-gray-500">
            <div class="typing-indicator">
              <span></span>
              <span></span>
              <span></span>
            </div>
            <span class="text-sm">{{ $t('chat.bot_typing') }}</span>
          </div>
        </div>

        <!-- Input Area -->
        <div class="border-t dark:border-gray-700 p-4">
          <form @submit.prevent="sendMessage" class="flex items-start space-x-4">
            <div class="flex-1">
              <TextareaAutosize
                v-model="newMessage"
                :placeholder="$t('chat.message_placeholder')"
                :min-height="24"
                :max-height="200"
                class="w-full rounded-lg border dark:border-gray-700 dark:bg-gray-800 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @keydown.enter.exact.prevent="sendMessage"
              />
              
              <div class="mt-2 flex items-center space-x-4">
                <button
                  type="button"
                  class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                  @click="showUpload = true"
                >
                  <PaperClipIcon class="h-5 w-5" />
                </button>
                
                <button
                  type="button"
                  class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                  @click="startVoiceInput"
                >
                  <MicrophoneIcon 
                    class="h-5 w-5"
                    :class="{'text-red-500': isRecording}"
                  />
                </button>

                <div class="flex-1"></div>

                <span class="text-xs text-gray-500">
                  {{ $t('chat.enter_to_send') }}
                </span>
              </div>
            </div>

            <Button
              type="submit"
              :disabled="!newMessage.trim() || isSending"
            >
              <PaperAirplaneIcon 
                class="h-5 w-5"
                :class="{'animate-pulse': isSending}"
              />
            </Button>
          </form>
        </div>
      </div>

      <!-- Settings Modal -->
      <Modal
        v-if="showSettings"
        @close="showSettings = false"
      >
        <template #title>
          {{ $t('chat.settings') }}
        </template>
        
        <div class="space-y-4">
          <div>
            <Label>{{ $t('chat.model') }}</Label>
            <Select
              v-model="settings.model"
              :options="availableModels"
              class="mt-1"
            />
          </div>

          <div>
            <Label>{{ $t('chat.temperature') }}</Label>
            <Slider
              v-model="settings.temperature"
              :min="0"
              :max="2"
              :step="0.1"
              class="mt-1"
            />
          </div>

          <div>
            <Label>{{ $t('chat.max_tokens') }}</Label>
            <Input
              v-model.number="settings.maxTokens"
              type="number"
              :min="1"
              :max="4000"
              class="mt-1"
            />
          </div>
        </div>

        <template #footer>
          <div class="flex justify-end space-x-2">
            <Button
              variant="secondary"
              @click="showSettings = false"
            >
              {{ $t('common.cancel') }}
            </Button>
            <Button
              @click="saveSettings"
            >
              {{ $t('common.save') }}
            </Button>
          </div>
        </template>
      </Modal>

      <!-- Upload Modal -->
      <UploadModal
        v-if="showUpload"
        @close="showUpload = false"
        @upload="handleFileUpload"
      />
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import { useI18n } from 'vue-i18n'
import {
  CogIcon,
  ArrowDownTrayIcon,
  PaperClipIcon,
  MicrophoneIcon,
  PaperAirplaneIcon
} from '@heroicons/vue/24/outline'
import TextareaAutosize from '@/Components/TextareaAutosize.vue'
import Message from '@/Components/Chat/Message.vue'
import Modal from '@/Components/Modal.vue'
import UploadModal from '@/Components/Chat/UploadModal.vue'
import { useChat } from '@/Composables/useChat'
import { useVoiceInput } from '@/Composables/useVoiceInput'

const { t } = useI18n()
const props = defineProps({
  chat: {
    type: Object,
    required: true
  }
})

const {
  messages,
  newMessage,
  isSending,
  isTyping,
  settings,
  availableModels,
  sendMessage,
  retryMessage,
  editMessage,
  deleteMessage,
  saveSettings
} = useChat(props.chat)

const {
  isRecording,
  startVoiceInput,
  stopVoiceInput
} = useVoiceInput((text) => {
  newMessage.value = text
})

const messagesContainer = ref(null)
const showSettings = ref(false)
const showUpload = ref(false)

// Scroll to bottom when new messages arrive
watch(messages, async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}, { deep: true })

const handleFileUpload = async (files) => {
  // Handle file upload logic
}

const exportChat = async () => {
  // Handle chat export logic
}
</script>

<style scoped>
.typing-indicator {
  @apply flex space-x-1;
}

.typing-indicator span {
  @apply w-2 h-2 bg-gray-500 rounded-full animate-bounce;
}

.typing-indicator span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
  animation-delay: 0.4s;
}
</style> 