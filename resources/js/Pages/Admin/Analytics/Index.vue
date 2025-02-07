<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Filters -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <Label>Date Range</Label>
              <DateRangePicker
                v-model="filters.dateRange"
                class="mt-1"
              />
            </div>
            
            <div>
              <Label>User Group</Label>
              <Select
                v-model="filters.userGroup"
                :options="userGroups"
                class="mt-1"
              />
            </div>

            <div>
              <Label>Feature</Label>
              <Select
                v-model="filters.feature"
                :options="features"
                class="mt-1"
              />
            </div>

            <div class="flex items-end">
              <Button @click="applyFilters">
                Apply Filters
              </Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Usage Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <StatsCard
          title="Total Users"
          :value="stats.totalUsers"
          :change="stats.userGrowth"
          icon="UserGroupIcon"
        />
        
        <StatsCard
          title="Active Users"
          :value="stats.activeUsers"
          :change="stats.activeUserGrowth"
          icon="UserCircleIcon"
        />
        
        <StatsCard
          title="Total Queries"
          :value="stats.totalQueries"
          :change="stats.queryGrowth"
          icon="ChatBubbleLeftIcon"
        />
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium mb-4">User Growth</h3>
          <LineChart
            :data="charts.userGrowth"
            :options="chartOptions.userGrowth"
          />
        </div>

        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium mb-4">Feature Usage</h3>
          <BarChart
            :data="charts.featureUsage"
            :options="chartOptions.featureUsage"
          />
        </div>
      </div>

      <!-- User Behavior -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium mb-4">User Behavior Analysis</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div>
            <h4 class="text-sm font-medium text-gray-500">Avg. Session Duration</h4>
            <p class="text-2xl font-semibold">{{ formatDuration(behavior.avgSessionDuration) }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Bounce Rate</h4>
            <p class="text-2xl font-semibold">{{ behavior.bounceRate }}%</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Queries per Session</h4>
            <p class="text-2xl font-semibold">{{ behavior.queriesPerSession }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Return Rate</h4>
            <p class="text-2xl font-semibold">{{ behavior.returnRate }}%</p>
          </div>
        </div>

        <div class="mt-6">
          <h4 class="text-sm font-medium text-gray-500 mb-2">Popular User Paths</h4>
          <div class="space-y-2">
            <div v-for="path in behavior.popularPaths" :key="path.id"
                 class="flex items-center justify-between">
              <div class="flex-1">
                <div class="text-sm font-medium">{{ path.sequence }}</div>
                <div class="text-xs text-gray-500">{{ path.count }} users</div>
              </div>
              <div class="text-sm text-gray-500">
                {{ path.conversion_rate }}% conversion
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Retention Analysis -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium mb-4">User Retention</h3>
        
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="text-left">Cohort</th>
                <th v-for="week in 8" :key="week" class="text-center">
                  Week {{ week }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cohort in retention.cohorts" :key="cohort.date">
                <td class="font-medium">
                  {{ formatDate(cohort.date) }}
                  <span class="text-sm text-gray-500">
                    ({{ cohort.size }} users)
                  </span>
                </td>
                <td v-for="(rate, week) in cohort.retention" :key="week"
                    class="text-center"
                    :class="getRetentionColor(rate)">
                  {{ rate }}%
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Line as LineChart, Bar as BarChart } from 'vue-chartjs'
import { formatDate, formatDuration } from '@/utils/format'

const filters = ref({
  dateRange: null,
  userGroup: null,
  feature: null
})

const stats = ref({})
const charts = ref({})
const behavior = ref({})
const retention = ref({})

const chartOptions = {
  userGrowth: {
    responsive: true,
    scales: {
      y: { beginAtZero: true }
    }
  },
  featureUsage: {
    responsive: true,
    scales: {
      y: { beginAtZero: true }
    }
  }
}

onMounted(async () => {
  await loadAnalytics()
})

const loadAnalytics = async () => {
  const response = await axios.get(route('admin.analytics.data'), {
    params: filters.value
  })
  
  stats.value = response.data.stats
  charts.value = response.data.charts
  behavior.value = response.data.behavior
  retention.value = response.data.retention
}

const applyFilters = async () => {
  await loadAnalytics()
}

const getRetentionColor = (rate) => {
  if (rate >= 70) return 'bg-green-100 text-green-800'
  if (rate >= 40) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}
</script> 