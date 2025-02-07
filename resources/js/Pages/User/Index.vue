<template>
  <UserLayout>
    <div class="min-h-screen bg-gray-100">
      <!-- Hero Section -->
      <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
              {{ $t('home.welcome') }}
            </h1>
            <p class="mt-5 max-w-xl mx-auto text-xl text-gray-500">
              {{ $t('home.description') }}
            </p>
            <div class="mt-8 flex justify-center">
              <Button @click="startNewChat">
                {{ $t('home.start_chat') }}
              </Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Chats -->
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="chat in recentChats" :key="chat.id"
               class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <ChatBubbleLeftIcon class="h-6 w-6 text-gray-400" />
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-gray-900">
                    {{ truncate(chat.title, 30) }}
                  </h3>
                  <p class="text-sm text-gray-500">
                    {{ formatDate(chat.created_at) }}
                  </p>
                </div>
              </div>
              <div class="mt-4">
                <p class="text-gray-600">
                  {{ truncate(chat.last_message?.content, 100) }}
                </p>
              </div>
              <div class="mt-6 flex justify-between items-center">
                <Button size="sm" @click="continueChat(chat)">
                  {{ $t('home.continue_chat') }}
                </Button>
                <button class="text-gray-400 hover:text-gray-500"
                        @click="deleteChat(chat)">
                  <TrashIcon class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Features -->
      <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">
              {{ $t('home.features.title') }}
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
              {{ $t('home.features.description') }}
            </p>
          </div>

          <div class="mt-20">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
              <div v-for="feature in features" :key="feature.name" 
                   class="pt-6">
                <div class="flow-root rounded-lg px-6 pb-8">
                  <div class="-mt-6">
                    <div>
                      <span class="inline-flex items-center justify-center p-3 bg-indigo-500 rounded-md shadow-lg">
                        <component :is="feature.icon" 
                                 class="h-6 w-6 text-white" />
                      </span>
                    </div>
                    <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">
                      {{ feature.name }}
                    </h3>
                    <p class="mt-5 text-base text-gray-500">
                      {{ feature.description }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
          <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="action in quickActions" :key="action.name"
                 class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <component :is="action.icon" 
                             class="h-6 w-6 text-indigo-600" />
                  </div>
                  <div class="ml-4">
                    <h4 class="text-lg font-medium text-gray-900">
                      {{ action.name }}
                    </h4>
                  </div>
                </div>
                <div class="mt-4">
                  <Button size="sm" block @click="action.handler">
                    {{ action.buttonText }}
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          {{ $t('home.recent_activity') }}
        </h2>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
          <ul class="divide-y divide-gray-200">
            <li v-for="activity in recentActivity" :key="activity.id">
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <component :is="activity.icon" 
                             class="h-5 w-5 text-gray-400" />
                    <p class="ml-2 text-sm font-medium text-gray-900">
                      {{ activity.description }}
                    </p>
                  </div>
                  <div class="ml-2 flex-shrink-0 flex">
                    <p class="text-sm text-gray-500">
                      {{ formatDate(activity.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal v-model="showDeleteModal">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ $t('home.delete_chat_confirm') }}
        </h3>
        <p class="text-sm text-gray-500">
          {{ $t('home.delete_chat_warning') }}
        </p>
        <div class="mt-6 flex justify-end space-x-3">
          <Button
            variant="secondary"
            @click="showDeleteModal = false"
          >
            {{ $t('common.cancel') }}
          </Button>
          <Button
            variant="danger"
            @click="confirmDelete"
          >
            {{ $t('common.delete') }}
          </Button>
        </div>
      </div>
    </Modal>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import UserLayout from '@/Layouts/UserLayout.vue'
import {
  ChatBubbleLeftIcon,
  TrashIcon,
  DocumentTextIcon,
  CogIcon,
  UserGroupIcon,
  ChartBarIcon
} from '@heroicons/vue/24/outline'
import { formatDate, truncate } from '@/utils/format'

const router = useRouter()
const recentChats = ref([])
const recentActivity = ref([])
const showDeleteModal = ref(false)
const chatToDelete = ref(null)

const features = [
  {
    name: 'AI Chat Assistant',
    description: 'Powerful AI chat assistant powered by LM Studio',
    icon: ChatBubbleLeftIcon
  },
  {
    name: 'File Analysis',
    description: 'Upload and analyze documents with AI',
    icon: DocumentTextIcon
  },
  {
    name: 'Team Collaboration',
    description: 'Share and collaborate with team members',
    icon: UserGroupIcon
  },
  {
    name: 'Analytics & Insights',
    description: 'Get insights into your chat history',
    icon: ChartBarIcon
  }
]

const quickActions = [
  {
    name: 'New Chat',
    icon: ChatBubbleLeftIcon,
    buttonText: 'Start Chat',
    handler: startNewChat
  },
  {
    name: 'Upload Document',
    icon: DocumentTextIcon,
    buttonText: 'Upload',
    handler: () => router.push('/documents/upload')
  },
  {
    name: 'Settings',
    icon: CogIcon,
    buttonText: 'Configure',
    handler: () => router.push('/settings')
  },
  {
    name: 'Analytics',
    icon: ChartBarIcon,
    buttonText: 'View Stats',
    handler: () => router.push('/analytics')
  }
]

onMounted(async () => {
  await loadRecentChats()
  await loadRecentActivity()
})

async function loadRecentChats() {
  const response = await axios.get(route('chats.recent'))
  recentChats.value = response.data.chats
}

async function loadRecentActivity() {
  const response = await axios.get(route('activity.recent'))
  recentActivity.value = response.data.activities
}

function startNewChat() {
  router.push(route('chat.new'))
}

function continueChat(chat) {
  router.push(route('chat.show', chat.id))
}

function deleteChat(chat) {
  chatToDelete.value = chat
  showDeleteModal.value = true
}

async function confirmDelete() {
  if (!chatToDelete.value) return
  
  await axios.delete(route('chat.destroy', chatToDelete.value.id))
  await loadRecentChats()
  
  showDeleteModal.value = false
  chatToDelete.value = null
}
</script> 