<template>
  <div class="h-screen flex dark:bg-gray-900">
    <!-- Sidebar -->
    <div 
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r dark:border-gray-700 transform transition-transform duration-300 ease-in-out',
        {'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}
      ]"
    >
      <div class="h-full flex flex-col">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 border-b dark:border-gray-700">
          <Link href="/" class="flex items-center">
            <img src="/logo.svg" alt="Logo" class="h-8 w-8" />
            <span class="ml-2 text-xl font-semibold dark:text-white">LM Studio</span>
          </Link>
          <button 
            @click="toggleSidebar"
            class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
          >
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
          <!-- New Chat Button -->
          <button
            @click="startNewChat"
            class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            {{ $t('chat.new_chat') }}
          </button>

          <!-- Recent Chats -->
          <div class="mt-6">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('chat.recent_chats') }}
            </h3>
            <div class="mt-2 space-y-1">
              <Link
                v-for="chat in recentChats"
                :key="chat.id"
                :href="route('chat.show', chat.id)"
                class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                :class="{'bg-gray-100 dark:bg-gray-700': route().current('chat.show', chat.id)}"
              >
                <ChatBubbleLeftIcon class="h-5 w-5 mr-2 text-gray-400" />
                <span class="truncate dark:text-gray-300">{{ chat.title }}</span>
              </Link>
            </div>
          </div>

          <!-- Folders -->
          <div class="mt-6">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('chat.folders') }}
            </h3>
            <div class="mt-2 space-y-1">
              <Link
                v-for="folder in folders"
                :key="folder.id"
                :href="route('folders.show', folder.id)"
                class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
              >
                <FolderIcon class="h-5 w-5 mr-2 text-gray-400" />
                <span class="truncate dark:text-gray-300">{{ folder.name }}</span>
              </Link>
            </div>
          </div>
        </nav>

        <!-- User Menu -->
        <div class="border-t dark:border-gray-700 p-4">
          <Menu as="div" class="relative">
            <MenuButton class="flex items-center w-full text-sm">
              <img
                :src="user.avatar"
                alt=""
                class="h-8 w-8 rounded-full"
              />
              <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ user.name }}
              </span>
              <ChevronUpIcon
                class="ml-auto h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
            </MenuButton>

            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems
                class="absolute bottom-full left-0 w-full mb-2 origin-bottom-right bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <div class="py-1">
                  <MenuItem v-slot="{ active }">
                    <Link
                      :href="route('profile.show')"
                      :class="[
                        active ? 'bg-gray-100 dark:bg-gray-700' : '',
                        'block px-4 py-2 text-sm text-gray-700 dark:text-gray-300'
                      ]"
                    >
                      {{ $t('user.profile') }}
                    </Link>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <Link
                      :href="route('settings.show')"
                      :class="[
                        active ? 'bg-gray-100 dark:bg-gray-700' : '',
                        'block px-4 py-2 text-sm text-gray-700 dark:text-gray-300'
                      ]"
                    >
                      {{ $t('user.settings') }}
                    </Link>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <button
                      @click="logout"
                      :class="[
                        active ? 'bg-gray-100 dark:bg-gray-700' : '',
                        'block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300'
                      ]"
                    >
                      {{ $t('user.logout') }}
                    </button>
                  </MenuItem>
                </div>
              </MenuItems>
            </transition>
          </Menu>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col lg:pl-64">
      <!-- Mobile Header -->
      <div class="lg:hidden">
        <div class="flex items-center justify-between h-16 px-4 border-b dark:border-gray-700">
          <button
            @click="toggleSidebar"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
          >
            <Bars3Icon class="h-6 w-6" />
          </button>
          <Link href="/" class="flex items-center">
            <img src="/logo.svg" alt="Logo" class="h-8 w-8" />
            <span class="ml-2 text-xl font-semibold dark:text-white">LM Studio</span>
          </Link>
          <div class="w-6"></div>
        </div>
      </div>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  Bars3Icon,
  XMarkIcon,
  PlusIcon,
  ChatBubbleLeftIcon,
  FolderIcon,
  ChevronUpIcon
} from '@heroicons/vue/24/outline'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const sidebarOpen = ref(true)
const recentChats = ref([])
const folders = ref([])
const user = ref({
  name: 'John Doe',
  avatar: '/avatar.jpg'
})

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const startNewChat = () => {
  // Implement new chat logic
}

const logout = () => {
  // Implement logout logic
}

onMounted(async () => {
  // Load recent chats and folders
})
</script> 