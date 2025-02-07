<template>
  <AdminLayout>
    <div class="bg-white shadow rounded-lg">
      <div class="p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold">System Logs</h2>
          <div class="flex space-x-4">
            <Button @click="exportLogs">Export to Excel</Button>
            <Button variant="secondary" @click="exportLogs('pdf')">Export to PDF</Button>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <Input
              v-model="filters.search"
              placeholder="Search queries..."
              @input="debouncedSearch"
            />
          </div>
          <Select v-model="filters.status">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="failed">Failed</option>
          </Select>
          <DateRangePicker v-model="filters.date_range" />
        </div>

        <!-- Logs Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th>User</th>
                <th>Query</th>
                <th>Status</th>
                <th>Processing Time</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="log in logs.data" :key="log.id">
                <td>{{ log.user.name }}</td>
                <td>{{ log.query }}</td>
                <td>
                  <Badge :color="getStatusColor(log.status)">
                    {{ log.status }}
                  </Badge>
                </td>
                <td>{{ log.processing_time }}ms</td>
                <td>{{ formatDate(log.created_at) }}</td>
                <td>
                  <Button size="sm" @click="viewDetails(log)">View Details</Button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="logs.links" class="mt-6" />
      </div>
    </div>

    <!-- Log Details Modal -->
    <Modal v-model="showDetailsModal" v-if="selectedLog">
      <div class="space-y-4">
        <h3 class="text-lg font-medium">Log Details</h3>
        <div>
          <Label>Query</Label>
          <pre class="mt-1 bg-gray-50 p-3 rounded">{{ selectedLog.query }}</pre>
        </div>
        <div>
          <Label>Response</Label>
          <pre class="mt-1 bg-gray-50 p-3 rounded">{{ selectedLog.response }}</pre>
        </div>
        <div>
          <Label>Server Responses</Label>
          <div class="mt-1 space-y-2">
            <div v-for="(response, index) in selectedLog.server_responses" :key="index">
              <p class="font-medium">Server {{ index + 1 }}</p>
              <pre class="bg-gray-50 p-2 rounded">{{ response }}</pre>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { debounce } from 'lodash'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import Select from '@/Components/Select.vue'
import DateRangePicker from '@/Components/DateRangePicker.vue'
import Badge from '@/Components/Badge.vue'
import Modal from '@/Components/Modal.vue'
import { formatDate } from '@/utils/format'

const props = defineProps({
  logs: Object,
  filters: Object,
})

const showDetailsModal = ref(false)
const selectedLog = ref(null)

const debouncedSearch = debounce((value) => {
  Inertia.get(route('admin.logs'), { ...filters.value, search: value }, {
    preserveState: true,
    preserveScroll: true,
  })
}, 300)

const exportLogs = (format = 'excel') => {
  window.location.href = route('admin.logs.export', { 
    ...filters.value, 
    format 
  })
}

const viewDetails = (log) => {
  selectedLog.value = log
  showDetailsModal.value = true
}

watch(() => props.filters, (newFilters) => {
  if (newFilters.status || newFilters.date_range) {
    Inertia.get(route('admin.logs'), newFilters, {
      preserveState: true,
      preserveScroll: true,
    })
  }
}, { deep: true })
</script> 