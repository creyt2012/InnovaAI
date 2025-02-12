<template>
  <DashboardLayout>
    <div class="space-y-6">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <StatsCard
          v-for="stat in stats"
          :key="stat.name"
          v-bind="stat"
        />
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <template #header>
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium">Visitors Over Time</h3>
              <DateRangePicker v-model="dateRange" />
            </div>
          </template>

          <VisitorsChart 
            :data="chartData.visitors"
            :loading="loading"
          />
        </Card>

        <Card>
          <template #header>
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium">Traffic Sources</h3>
              <SegmentedControl v-model="period" :options="periodOptions" />
            </div>
          </template>

          <TrafficSourcesChart 
            :data="chartData.sources"
            :loading="loading"
          />
        </Card>
      </div>

      <!-- Detailed Stats -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <Card>
          <template #header>
            <h3 class="text-lg font-medium">Top Pages</h3>
          </template>

          <TopPagesTable 
            :pages="topPages"
            :loading="loading"
          />
        </Card>

        <Card>
          <template #header>
            <h3 class="text-lg font-medium">Visitor Demographics</h3>
          </template>

          <DemographicsChart 
            :data="demographics"
            :loading="loading"
          />
        </Card>

        <Card>
          <template #header>
            <h3 class="text-lg font-medium">Device Distribution</h3>
          </template>

          <DevicesChart 
            :data="devices"
            :loading="loading"
          />
        </Card>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import StatsCard from '@/Components/StatsCard.vue'
import Card from '@/Components/UI/Card.vue'
import DateRangePicker from '@/Components/DateRangePicker.vue'
import SegmentedControl from '@/Components/SegmentedControl.vue'
import VisitorsChart from '@/Components/Analytics/VisitorsChart.vue'
import TrafficSourcesChart from '@/Components/Analytics/TrafficSourcesChart.vue'
import TopPagesTable from '@/Components/Analytics/TopPagesTable.vue'
import DemographicsChart from '@/Components/Analytics/DemographicsChart.vue'
import DevicesChart from '@/Components/Analytics/DevicesChart.vue'

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