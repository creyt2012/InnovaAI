<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between h-16 px-4 border-b dark:border-gray-700">
        <Logo class="h-8 w-auto" />
        <ThemeToggle />
      </div>

      <nav class="p-4 space-y-1">
        <SidebarLink 
          v-for="item in navigation" 
          :key="item.name"
          :item="item"
        />
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="pl-64">
      <!-- Top Navigation -->
      <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between h-full px-6">
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ title }}
          </h1>

          <div class="flex items-center space-x-4">
            <NotificationsDropdown />
            <UserDropdown />
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6">
        <div class="max-w-7xl mx-auto">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Logo from '@/Components/Logo.vue'
import ThemeToggle from '@/Components/ThemeToggle.vue'
import SidebarLink from '@/Components/SidebarLink.vue'
import NotificationsDropdown from '@/Components/NotificationsDropdown.vue'
import UserDropdown from '@/Components/UserDropdown.vue'

const page = usePage()
const title = computed(() => page.props.title)

const navigation = [
  {
    name: 'Dashboard',
    icon: 'HomeIcon',
    route: 'admin.dashboard'
  },
  {
    name: 'Analytics',
    icon: 'ChartBarIcon',
    children: [
      { name: 'Overview', route: 'admin.analytics.index' },
      { name: 'Real-time', route: 'admin.analytics.realtime' },
      { name: 'Heatmaps', route: 'admin.analytics.heatmap' },
      { name: 'Conversions', route: 'admin.analytics.conversions' },
      { name: 'Funnels', route: 'admin.analytics.funnels' },
      { name: 'A/B Tests', route: 'admin.analytics.ab-tests' }
    ]
  },
  // ... other navigation items
]
</script> 