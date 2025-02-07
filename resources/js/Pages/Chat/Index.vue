<template>
  <UserLayout>
    <div class="flex h-screen bg-gray-100">
      <!-- Sidebar -->
      <div class="w-64 bg-white border-r">
        <div class="p-4">
          <Button class="w-full" @click="newChat">New Chat</Button>
        </div>
        
        <div class="overflow-y-auto h-full">
          <div v-for="chat in conversations" :key="chat.id" 
               class="p-3 hover:bg-gray-100 cursor-pointer"
               :class="{ 'bg-gray-100': currentChat?.id === chat.id }"
               @click="selectChat(chat)">
            <p class="text-sm font-medium truncate">{{ chat.title }}</p>
            <p class="text-xs text-gray-500">
              {{ formatDate(chat.created_at) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Main Chat Area -->
      <div class="flex-1 flex flex-col">
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
          <div v-if="!currentChat" class="text-center text-gray-500 mt-10">
            Select a chat or start a new one
          </div>
          
          <template v-else>
            <div v-for="message in currentChat.messages" :key="message.id"
                 :class="[
                   'p-4 rounded-lg max-w-3xl mx-auto',
                   message.role === 'user' ? 'bg-blue-50' : 'bg-white border'
                 ]">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                    {{ message.role === 'user' ? 'U' : 'A' }}
                  </div>
                </div>
                <div class="ml-3">
                  <p class="text-sm">{{ message.content }}</p>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ formatDate(message.created_at) }}
                  </p>
                </div>
              </div>
            </div>
          </template>
        </div>

        <!-- Input Area -->
        <div class="border-t p-4">
          <form @submit.prevent="sendMessage" class="flex space-x-4">
            <textarea
              v-model="newMessage"
              rows="1"
              class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
              placeholder="Type your message..."
            ></textarea>
            <Button type="submit" :disabled="!newMessage.trim()">
              Send
            </Button>
          </form>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import Button from '@/Components/Button.vue'
import { formatDate } from '@/utils/format'

const props = defineProps({
  conversations: Array,
  currentConversation: Object,
})

const currentChat = ref(props.currentConversation)
const newMessage = ref('')

const form = useForm({
  message: '',
})

const selectChat = (chat) => {
  window.location = route('chat.show', chat.id)
}

const newChat = () => {
  form.post(route('chat.store'))
}

const sendMessage = () => {
  if (!newMessage.value.trim()) return

  form.message = newMessage.value
  form.post(route('chat.message', currentChat.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      newMessage.value = ''
    },
  })
}
</script> 