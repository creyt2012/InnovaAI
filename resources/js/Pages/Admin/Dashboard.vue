<template>
  <AdminLayout>
    <template #header>Dashboard</template>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Stats Cards -->
      <div v-for="stat in stats" :key="stat.name" 
           class="bg-white dark:bg-gray-800 rounded-xl p-6">
        <div class="flex items-center">
          <component :is="stat.icon" 
                    class="h-12 w-12 text-primary-500" />
          <div class="ml-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
              {{ stat.name }}
            </h3>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
              {{ stat.value }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
      <Card>
        <template #header>
          <h3 class="text-lg font-medium">Usage Statistics</h3>
        </template>
        <LineChart :data="usageData" />
      </Card>

      <Card>
        <template #header>
          <h3 class="text-lg font-medium">Model Distribution</h3>
        </template>
        <PieChart :data="modelData" />
      </Card>
    </div>

    <!-- Recent Activity -->
    <Card class="mt-6">
      <template #header>
        <h3 class="text-lg font-medium">Recent Activity</h3>
      </template>
      
      <div class="divide-y dark:divide-gray-700">
        <div v-for="activity in recentActivity" :key="activity.id"
             class="py-4 flex items-center space-x-4">
          <component :is="activity.icon" 
                    class="h-8 w-8 text-gray-400" />
          <div>
            <p class="text-sm text-gray-900 dark:text-white">
              {{ activity.description }}
            </p>
            <p class="text-xs text-gray-500">
              {{ activity.time }}
            </p>
          </div>
        </div>
      </div>
    </Card>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Card from '@/Components/UI/Card.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import PieChart from '@/Components/Charts/PieChart.vue'
import { UsersIcon, ChatBubbleLeftIcon, ServerIcon, CpuChipIcon } from '@heroicons/vue/24/outline'

const stats = [
  { name: 'Total Users', value: '1,234', icon: UsersIcon },
  { name: 'Active Chats', value: '56', icon: ChatBubbleLeftIcon },
  { name: 'Online Servers', value: '8', icon: ServerIcon },
  { name: 'Active Models', value: '12', icon: CpuChipIcon }
]

// Add chart data and recent activity data...
</script> 