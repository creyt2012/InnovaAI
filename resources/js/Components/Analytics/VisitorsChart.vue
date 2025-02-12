<template>
  <div class="relative h-[300px]">
    <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-gray-800/50">
      <Spinner />
    </div>

    <Line
      v-if="chartData"
      :data="chartData"
      :options="chartOptions"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  loading: Boolean
})

const { isDark } = useTheme()

const chartData = computed(() => ({
  labels: props.data.labels,
  datasets: [
    {
      label: 'Visitors',
      data: props.data.visitors,
      borderColor: '#6366F1',
      backgroundColor: '#6366F1',
      tension: 0.4
    }
  ]
}))

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: 'index'
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        color: isDark.value ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
      },
      ticks: {
        color: isDark.value ? '#9CA3AF' : '#6B7280'
      }
    },
    x: {
      grid: {
        display: false
      },
      ticks: {
        color: isDark.value ? '#9CA3AF' : '#6B7280'
      }
    }
  },
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: isDark.value ? '#374151' : '#FFFFFF',
      titleColor: isDark.value ? '#F3F4F6' : '#111827',
      bodyColor: isDark.value ? '#D1D5DB' : '#4B5563',
      borderColor: isDark.value ? '#4B5563' : '#E5E7EB',
      borderWidth: 1,
      padding: 12,
      boxPadding: 6
    }
  }
}))
</script> 