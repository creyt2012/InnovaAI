<template>
  <AdminLayout>
    <div class="container mx-auto py-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">LM Studio APIs</h1>
        <Link :href="route('admin.apis.create')" 
              class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
          Add New API
        </Link>
      </div>

      <!-- API List -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Endpoint
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Last Tested
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="api in apis.data" :key="api.id">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ api.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ api.endpoint }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                  api.is_active 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800'
                ]">
                  {{ api.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ api.last_tested_at ? formatDate(api.last_tested_at) : 'Never' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button @click="testConnection(api)" 
                        class="text-indigo-600 hover:text-indigo-900 mr-3">
                  Test
                </button>
                <Link :href="route('admin.apis.edit', api.id)" 
                      class="text-blue-600 hover:text-blue-900 mr-3">
                  Edit
                </Link>
                <button @click="deleteApi(api)" 
                        class="text-red-600 hover:text-red-900">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination :links="apis.links" class="mt-6" />
    </div>

    <!-- Test Connection Modal -->
    <Modal v-if="showTestModal" @close="showTestModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          Connection Test Results
        </h3>
        <div v-if="testLoading" class="flex justify-center">
          <Spinner />
        </div>
        <div v-else>
          <div :class="[
            'p-4 rounded-lg mb-4',
            testSuccess ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
          ]">
            {{ testMessage }}
          </div>
          <div v-if="testSuccess && availableModels.length" class="mt-4">
            <h4 class="font-medium mb-2">Available Models:</h4>
            <ul class="list-disc list-inside">
              <li v-for="model in availableModels" :key="model.id">
                {{ model.id }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import Spinner from '@/Components/Spinner.vue'
import { formatDate } from '@/utils/format'

const props = defineProps({
  apis: Object,
})

const showTestModal = ref(false)
const testLoading = ref(false)
const testSuccess = ref(false)
const testMessage = ref('')
const availableModels = ref([])

const testConnection = async (api) => {
  showTestModal.value = true
  testLoading.value = true
  testSuccess.value = false
  testMessage.value = ''
  availableModels.value = []

  try {
    const response = await axios.post(route('admin.apis.test', api.id))
    testSuccess.value = true
    testMessage.value = response.data.message
    if (response.data.models) {
      availableModels.value = response.data.models
    }
  } catch (error) {
    testSuccess.value = false
    testMessage.value = error.response?.data?.message || 'Connection test failed'
  } finally {
    testLoading.value = false
  }
}

const deleteApi = (api) => {
  if (confirm(`Are you sure you want to delete ${api.name}?`)) {
    router.delete(route('admin.apis.destroy', api.id))
  }
}
</script> 