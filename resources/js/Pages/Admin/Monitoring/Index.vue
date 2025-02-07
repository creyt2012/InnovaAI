<template>
  <AdminLayout>
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">
          {{ $page.props.language.admin.monitoring }}
        </h1>
        <p class="mt-2 text-sm text-gray-700">
          {{ $page.props.language.admin.monitoring_description }}
        </p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <select v-model="refreshInterval" class="rounded-md border-gray-300">
          <option value="5000">5s</option>
          <option value="10000">10s</option>
          <option value="30000">30s</option>
          <option value="60000">1m</option>
        </select>
      </div>
    </div>

    <!-- Server Overview -->
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <ServerIcon class="h-6 w-6 text-gray-400" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  {{ $page.props.language.admin.monitoring_total_servers }}
                </dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">
                    {{ stats.total_servers }}
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <CheckCircleIcon class="h-6 w-6 text-green-400" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  {{ $page.props.language.admin.monitoring_active_servers }}
                </dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">
                    {{ stats.active_servers }}
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <UsersIcon class="h-6 w-6 text-blue-400" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  {{ $page.props.language.admin.monitoring_active_users }}
                </dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">
                    {{ stats.active_users }}
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <ChatBubbleLeftIcon class="h-6 w-6 text-indigo-400" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  {{ $page.props.language.admin.monitoring_total_chats }}
                </dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">
                    {{ stats.total_chats }}
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Server Metrics -->
    <div class="mt-8">
      <h2 class="text-lg font-medium text-gray-900">
        {{ $page.props.language.admin.monitoring_server_metrics }}
      </h2>
      <div class="mt-4 grid gap-6 lg:grid-cols-2">
        <div v-for="server in servers" :key="server.id"
             class="bg-white shadow rounded-lg overflow-hidden">
          <div class="p-5">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900">
                {{ server.name }}
              </h3>
              <span :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                {
                  'bg-green-100 text-green-800': server.status === 'active',
                  'bg-red-100 text-red-800': server.status === 'error',
                  'bg-gray-100 text-gray-800': server.status === 'inactive'
                }
              ]">
                {{ server.status }}
              </span>
            </div>

            <div class="mt-6 grid grid-cols-3 gap-4">
              <!-- CPU Usage -->
              <div>
                <div class="flex items-center justify-between">
                  <div class="text-sm font-medium text-gray-500">CPU</div>
                  <div class="text-sm font-medium" 
                       :class="getCpuColor(server.metrics?.cpu_usage)">
                    {{ server.metrics?.cpu_usage }}%
                  </div>
                </div>
                <div class="mt-2 relative pt-1">
                  <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                    <div :style="{ width: `${server.metrics?.cpu_usage}%` }"
                         :class="[
                           'shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center',
                           getCpuBarColor(server.metrics?.cpu_usage)
                         ]" />
                  </div>
                </div>
              </div>

              <!-- Memory Usage -->
              <div>
                <div class="flex items-center justify-between">
                  <div class="text-sm font-medium text-gray-500">Memory</div>
                  <div class="text-sm font-medium"
                       :class="getMemoryColor(server.metrics?.memory_usage)">
                    {{ server.metrics?.memory_usage }}%
                  </div>
                </div>
                <div class="mt-2 relative pt-1">
                  <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                    <div :style="{ width: `${server.metrics?.memory_usage}%` }"
                         :class="[
                           'shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center',
                           getMemoryBarColor(server.metrics?.memory_usage)
                         ]" />
                  </div>
                </div>
              </div>

              <!-- Disk Usage -->
              <div>
                <div class="flex items-center justify-between">
                  <div class="text-sm font-medium text-gray-500">Disk</div>
                  <div class="text-sm font-medium"
                       :class="getDiskColor(server.metrics?.disk_usage)">
                    {{ server.metrics?.disk_usage }}%
                  </div>
                </div>
                <div class="mt-2 relative pt-1">
                  <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                    <div :style="{ width: `${server.metrics?.disk_usage}%` }"
                         :class="[
                           'shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center',
                           getDiskBarColor(server.metrics?.disk_usage)
                         ]" />
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
              <div>
                <div class="text-sm font-medium text-gray-500">
                  {{ $page.props.language.admin.monitoring_active_connections }}
                </div>
                <div class="mt-1 text-lg font-semibold text-gray-900">
                  {{ server.metrics?.active_connections }}
                </div>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500">
                  {{ $page.props.language.admin.monitoring_response_time }}
                </div>
                <div class="mt-1 text-lg font-semibold text-gray-900">
                  {{ server.metrics?.response_time }}ms
                </div>
              </div>
            </div>

            <!-- Line Chart -->
            <div class="mt-6 h-48">
              <LineChart
                :data="server.historical_metrics"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  ServerIcon, CheckCircleIcon, UsersIcon,
  ChatBubbleLeftIcon
} from '@heroicons/vue/24/outline'
import { Line as LineChart } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps({
  servers: Array,
  stats: Object,
})

const refreshInterval = ref(10000)
let refreshTimer = null

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: {
      beginAtZero: true,
      max: 100
    }
  },
  plugins: {
    legend: {
      display: true
    }
  }
}

const getCpuColor = (usage) => {
  if (usage >= 90) return 'text-red-600'
  if (usage >= 70) return 'text-yellow-600'
  return 'text-green-600'
}

const getCpuBarColor = (usage) => {
  if (usage >= 90) return 'bg-red-500'
  if (usage >= 70) return 'bg-yellow-500'
  return 'bg-green-500'
}

const getMemoryColor = (usage) => {
  if (usage >= 90) return 'text-red-600'
  if (usage >= 70) return 'text-yellow-600'
  return 'text-blue-600'
}

const getMemoryBarColor = (usage) => {
  if (usage >= 90) return 'bg-red-500'
  if (usage >= 70) return 'bg-yellow-500'
  return 'bg-blue-500'
}

const getDiskColor = (usage) => {
  if (usage >= 90) return 'text-red-600'
  if (usage >= 70) return 'text-yellow-600'
  return 'text-purple-600'
}

const getDiskBarColor = (usage) => {
  if (usage >= 90) return 'bg-red-500'
  if (usage >= 70) return 'bg-yellow-500'
  return 'bg-purple-500'
}

const refreshData = async () => {
  try {
    const response = await axios.get(route('admin.monitoring.stats'))
    // Update stats and metrics
  } catch (error) {
    console.error('Failed to refresh monitoring data:', error)
  }
}

onMounted(() => {
  refreshTimer = setInterval(refreshData, refreshInterval.value)
})

onUnmounted(() => {
  if (refreshTimer) clearInterval(refreshTimer)
})

watch(refreshInterval, (newValue) => {
  if (refreshTimer) clearInterval(refreshTimer)
  refreshTimer = setInterval(refreshData, newValue)
})
</script> 