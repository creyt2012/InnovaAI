<template>
  <AdminLayout>
    <!-- Security Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <DashboardCard title="Active Threats">
        <template #icon>
          <ShieldExclamationIcon 
            class="w-6 h-6"
            :class="{
              'text-red-500': stats.active_threats > 0,
              'text-green-500': stats.active_threats === 0
            }"
          />
        </template>
        <div class="flex flex-col">
          <span class="text-2xl font-bold">{{ stats.active_threats }}</span>
          <span class="text-sm text-gray-500">
            Last 24 hours
          </span>
        </div>
      </DashboardCard>

      <DashboardCard title="Failed Logins">
        <template #icon>
          <KeyIcon class="w-6 h-6 text-yellow-500" />
        </template>
        <div class="flex flex-col">
          <span class="text-2xl font-bold">{{ stats.failed_logins }}</span>
          <span class="text-sm text-gray-500">
            Today
          </span>
        </div>
      </DashboardCard>

      <DashboardCard title="Blocked IPs">
        <template #icon>
          <NoSymbolIcon class="w-6 h-6 text-red-500" />
        </template>
        <div class="flex flex-col">
          <span class="text-2xl font-bold">{{ stats.blocked_ips }}</span>
          <span class="text-sm text-gray-500">
            Active blocks
          </span>
        </div>
      </DashboardCard>

      <DashboardCard title="API Security">
        <template #icon>
          <LockClosedIcon 
            class="w-6 h-6"
            :class="{
              'text-green-500': stats.api_security_score >= 80,
              'text-yellow-500': stats.api_security_score >= 60,
              'text-red-500': stats.api_security_score < 60
            }"
          />
        </template>
        <div class="flex flex-col">
          <span class="text-2xl font-bold">{{ stats.api_security_score }}%</span>
          <span class="text-sm text-gray-500">
            Security Score
          </span>
        </div>
      </DashboardCard>
    </div>

    <!-- Security Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Login Attempts</h3>
        <LineChart
          :data="chartData.loginAttempts"
          :options="chartOptions.loginAttempts"
        />
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Threat Distribution</h3>
        <PieChart
          :data="chartData.threatDistribution"
          :options="chartOptions.threatDistribution"
        />
      </div>
    </div>

    <!-- Security Logs -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Security Events</h3>
          <div class="space-x-2">
            <Button @click="exportLogs">Export Logs</Button>
            <Button @click="showFilters = !showFilters">
              <FilterIcon class="w-5 h-5" />
            </Button>
          </div>
        </div>

        <div v-if="showFilters" class="mb-4">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Event Type
              </label>
              <Select
                v-model="filters.eventType"
                :options="eventTypes"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">
                Severity
              </label>
              <Select
                v-model="filters.severity"
                :options="severityLevels"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">
                Date Range
              </label>
              <DateRangePicker v-model="filters.dateRange" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">
                IP Address
              </label>
              <Input v-model="filters.ipAddress" placeholder="Search IP" />
            </div>
          </div>
        </div>

        <DataTable
          :data="securityLogs"
          :columns="columns"
          :sortable="true"
          :searchable="true"
          :paginate="true"
        >
          <template #severity="{ row }">
            <Badge :color="getSeverityColor(row.severity)">
              {{ row.severity }}
            </Badge>
          </template>

          <template #actions="{ row }">
            <div class="flex space-x-2">
              <Button
                size="sm"
                @click="showEventDetails(row)"
              >
                Details
              </Button>
              <Button
                size="sm"
                variant="danger"
                v-if="row.ip"
                @click="blockIP(row.ip)"
              >
                Block IP
              </Button>
            </div>
          </template>
        </DataTable>
      </div>
    </div>

    <!-- Event Details Modal -->
    <Modal v-model="showEventModal">
      <template #title>Security Event Details</template>
      <template #content>
        <div class="space-y-4">
          <div v-for="(value, key) in selectedEvent" :key="key">
            <label class="block text-sm font-medium text-gray-700">
              {{ formatLabel(key) }}
            </label>
            <div class="mt-1">
              <pre class="text-sm text-gray-900">{{ formatValue(value) }}</pre>
            </div>
          </div>
        </div>
      </template>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  ShieldExclamationIcon, KeyIcon, NoSymbolIcon,
  LockClosedIcon, FilterIcon
} from '@heroicons/vue/24/outline'
import { formatDate, formatValue, formatLabel } from '@/utils/format'

const props = defineProps({
  stats: Object,
  logs: Array
})

const showFilters = ref(false)
const showEventModal = ref(false)
const selectedEvent = ref(null)
const filters = ref({
  eventType: null,
  severity: null,
  dateRange: null,
  ipAddress: ''
})

const chartData = ref({
  loginAttempts: null,
  threatDistribution: null
})

const chartOptions = {
  loginAttempts: {
    responsive: true,
    scales: {
      y: { beginAtZero: true }
    }
  },
  threatDistribution: {
    responsive: true,
    plugins: {
      legend: {
        position: 'right'
      }
    }
  }
}

const columns = [
  {
    key: 'timestamp',
    label: 'Time',
    formatter: formatDate
  },
  {
    key: 'event_type',
    label: 'Event Type'
  },
  {
    key: 'severity',
    label: 'Severity'
  },
  {
    key: 'ip',
    label: 'IP Address'
  },
  {
    key: 'user',
    label: 'User'
  },
  {
    key: 'description',
    label: 'Description'
  }
]

const getSeverityColor = (severity) => {
  const colors = {
    critical: 'red',
    high: 'orange',
    medium: 'yellow',
    low: 'blue'
  }
  return colors[severity] || 'gray'
}

const showEventDetails = (event) => {
  selectedEvent.value = event
  showEventModal.value = true
}

const blockIP = async (ip) => {
  try {
    await axios.post(route('admin.security.block-ip'), { ip })
    // Update UI
  } catch (error) {
    console.error('Failed to block IP:', error)
  }
}

const exportLogs = async () => {
  try {
    const response = await axios.get(route('admin.security.export-logs'), {
      params: filters.value,
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'security-logs.xlsx')
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Failed to export logs:', error)
  }
}

onMounted(async () => {
  const response = await axios.get(route('admin.security.charts'))
  chartData.value = response.data
})
</script> 