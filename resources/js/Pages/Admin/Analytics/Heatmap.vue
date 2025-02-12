<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">
          Heatmap Analysis
        </h1>
        
        <div class="mt-4 flex space-x-4">
          <div class="w-64">
            <Label>Page URL</Label>
            <Select
              v-model="selectedPage"
              :options="pages"
              class="mt-1"
            />
          </div>
          
          <div class="w-48">
            <Label>Date Range</Label>
            <Select
              v-model="dateRange"
              :options="dateRanges"
              class="mt-1"
            />
          </div>
        </div>
      </div>

      <HeatmapViewer
        :page-url="selectedPage"
        :heatmap-data="heatmapData"
      />
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import HeatmapViewer from '@/Components/Analytics/HeatmapViewer.vue'
import Label from '@/Components/UI/Label.vue'
import Select from '@/Components/UI/Select.vue'

const props = defineProps({
  heatmapData: Object
})

const selectedPage = ref('')
const dateRange = ref('today')

const pages = [
  { value: '/', label: 'Home Page' },
  { value: '/about', label: 'About Page' },
  // Add more pages
]

const dateRanges = [
  { value: 'today', label: 'Today' },
  { value: 'week', label: 'This Week' },
  { value: 'month', label: 'This Month' }
]

watch([selectedPage, dateRange], async ([page, range]) => {
  if (!page) return
  
  const response = await axios.get(route('admin.analytics.heatmap'), {
    params: {
      page_url: page,
      date_range: range
    }
  })
  
  heatmapData.value = response.data.heatmapData
})
</script> 