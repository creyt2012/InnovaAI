<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 border-r dark:border-gray-700">
      <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 border-b dark:border-gray-700">
          <Link :href="route('admin.dashboard')" class="flex items-center space-x-2">
            <img :src="settings.logo" class="h-8 w-auto" alt="Logo" />
            <span class="text-lg font-semibold text-gray-900 dark:text-white">Admin</span>
          </Link>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-2 py-4">
          <div v-for="item in navigation" :key="item.name" class="space-y-1">
            <!-- Menu Item with Children -->
            <Disclosure v-if="item.children" v-slot="{ open }" as="div" class="space-y-1">
              <DisclosureButton
                class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md"
                :class="{ 'bg-gray-50 dark:bg-gray-700': open }"
              >
                <component :is="item.icon" class="h-5 w-5 mr-3" />
                <span>{{ item.name }}</span>
                <ChevronRightIcon
                  class="ml-auto h-5 w-5 transform transition-transform duration-200"
                  :class="{ 'rotate-90': open }"
                />
              </DisclosureButton>

              <DisclosurePanel class="space-y-1 pl-11">
                <Link
                  v-for="child in item.children"
                  :key="child.name"
                  :href="route(child.route)"
                  :class="[
                    isCurrentRoute(child.route)
                      ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400'
                      : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                    'block px-3 py-2 text-sm font-medium rounded-md'
                  ]"
                >
                  {{ child.name }}
                </Link>
              </DisclosurePanel>
            </Disclosure>

            <!-- Single Menu Item -->
            <Link
              v-else
              :href="route(item.route)"
              :class="[
                isCurrentRoute(item.route)
                  ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400'
                  : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                'flex items-center px-3 py-2 text-sm font-medium rounded-md'
              ]"
            >
              <component :is="item.icon" class="h-5 w-5 mr-3" />
              {{ item.name }}
            </Link>
          </div>
        </nav>

        <!-- Quick Actions -->
        <div class="px-2 py-4 border-t dark:border-gray-700">
          <div class="space-y-2">
            <button
              class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md"
              @click="clearCache"
            >
              <ArrowPathIcon class="h-5 w-5 mr-3" />
              Clear Cache
            </button>
            
            <button
              class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md"
              @click="createBackup"
            >
              <CloudArrowUpIcon class="h-5 w-5 mr-3" />
              Create Backup
            </button>
          </div>
        </div>

        <!-- User Menu -->
        <div class="flex items-center p-4 border-t dark:border-gray-700">
          <Menu as="div" class="relative inline-block text-left w-full">
            <MenuButton class="flex items-center w-full group">
              <img
                :src="$page.props.auth.user.avatar"
                class="h-8 w-8 rounded-full"
              />
              <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $page.props.auth.user.name }}
              </span>
              <ChevronUpDownIcon class="ml-auto h-5 w-5 text-gray-400" />
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
                class="absolute bottom-full left-0 mb-2 w-full rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <div class="py-1">
                  <MenuItem v-slot="{ active }">
                    <Link
                      :href="route('profile.edit')"
                      :class="[
                        active ? 'bg-gray-100 dark:bg-gray-700' : '',
                        'block px-4 py-2 text-sm text-gray-700 dark:text-gray-300'
                      ]"
                    >
                      Profile Settings
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
                      Sign out
                    </button>
                  </MenuItem>
                </div>
              </MenuItems>
            </transition>
          </Menu>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="pl-64">
      <!-- Top Bar -->
      <header class="h-16 bg-white dark:bg-gray-800 border-b dark:border-gray-700">
        <div class="flex items-center justify-between h-full px-6">
          <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
            <slot name="header" />
          </h1>

          <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative">
              <input
                type="text"
                placeholder="Search..."
                class="w-64 pl-10 pr-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 border-transparent focus:border-gray-500 focus:bg-white dark:focus:bg-gray-800"
              />
              <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
            </div>

            <!-- Notifications -->
            <Menu as="div" class="relative">
              <MenuButton
                class="flex items-center text-gray-400 hover:text-gray-500 focus:outline-none"
              >
                <span class="sr-only">Notifications</span>
                <BellIcon class="h-6 w-6" />
                <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 flex items-center justify-center">
                  <span class="text-xs text-white">3</span>
                </span>
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
                  class="absolute right-0 mt-2 w-80 rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                  <div class="py-1">
                    <MenuItem v-slot="{ active }">
                      <a
                        href="#"
                        :class="[
                          active ? 'bg-gray-100 dark:bg-gray-700' : '',
                          'block px-4 py-2 text-sm text-gray-700 dark:text-gray-300'
                        ]"
                      >
                        <div class="flex items-center">
                          <div class="flex-shrink-0">
                            <UserIcon class="h-5 w-5 text-gray-400" />
                          </div>
                          <div class="ml-3">
                            <p class="text-sm font-medium">New user registered</p>
                            <p class="text-xs text-gray-500">2 minutes ago</p>
                          </div>
                        </div>
                      </a>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>

            <!-- Theme Toggle -->
            <button
              @click="toggleTheme"
              class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none"
            >
              <SunIcon v-if="isDark" class="h-6 w-6" />
              <MoonIcon v-else class="h-6 w-6" />
            </button>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  Cog6ToothIcon,
  ServerIcon,
  UsersIcon,
  ChartBarIcon,
  DocumentTextIcon,
  BellIcon,
  SunIcon,
  MoonIcon,
  UserIcon,
  ChevronUpDownIcon,
  MagnifyingGlassIcon,
  ArrowPathIcon,
  CloudArrowUpIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline'
import { Menu, MenuButton, MenuItem, MenuItems, Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { useSettingsStore } from '@/Stores/settingsStore'

const settingsStore = useSettingsStore()
const isDark = ref(document.documentElement.classList.contains('dark'))

const navigation = [
  { 
    name: 'Dashboard',
    route: 'admin.dashboard',
    icon: ChartBarIcon 
  },
  {
    name: 'User Management',
    icon: UsersIcon,
    children: [
      { name: 'Users', route: 'admin.users.index' },
      { name: 'Roles', route: 'admin.roles.index' },
      { name: 'Permissions', route: 'admin.permissions.index' }
    ]
  },
  {
    name: 'AI Management',
    icon: CpuChipIcon,
    children: [
      { name: 'Models', route: 'admin.models.index' },
      { name: 'Servers', route: 'admin.servers.index' },
      { name: 'APIs', route: 'admin.apis.index' }
    ]
  },
  {
    name: 'System',
    icon: WrenchScrewdriverIcon,
    children: [
      { name: 'Settings', route: 'admin.settings' },
      { name: 'Backups', route: 'admin.backups' },
      { name: 'Maintenance', route: 'admin.maintenance' }
    ]
  },
  {
    name: 'Monitoring',
    icon: ChartBarIcon,
    children: [
      { name: 'Server Status', route: 'admin.monitoring' },
      { name: 'Audit Logs', route: 'admin.audit-logs' },
      { name: 'System Logs', route: 'admin.logs' }
    ]
  }
]

const isCurrentRoute = (route) => {
  return route === usePage().component.value
}

const toggleTheme = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark')
}

const logout = () => {
  // Implement logout logic
}

const clearCache = () => {
  // Implement clear cache logic
}

const createBackup = () => {
  // Implement create backup logic
}
</script> 