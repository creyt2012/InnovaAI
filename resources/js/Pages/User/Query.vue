<template>
  <UserLayout>
    <div class="bg-white shadow sm:rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Submit Query
        </h3>
        
        <div class="mt-5">
          <form @submit.prevent="submitQuery">
            <div>
              <Label for="query">Your Query</Label>
              <textarea
                id="query"
                v-model="form.query"
                rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Enter your query here..."
                required
              ></textarea>
              <InputError :message="form.errors.query" />
            </div>

            <div class="mt-5">
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Processing...' : 'Submit Query' }}
              </Button>
            </div>
          </form>
        </div>

        <!-- Results Section -->
        <div v-if="result" class="mt-8">
          <h4 class="text-lg font-medium text-gray-900 mb-4">Results</h4>
          <div class="bg-gray-50 rounded-lg p-4">
            <pre class="whitespace-pre-wrap">{{ result }}</pre>
          </div>
        </div>

        <!-- Recent Queries -->
        <div class="mt-8">
          <h4 class="text-lg font-medium text-gray-900 mb-4">Recent Queries</h4>
          <div class="space-y-4">
            <div v-for="log in recentQueries" :key="log.id" 
                 class="bg-gray-50 rounded-lg p-4">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-medium">{{ log.query }}</p>
                  <p class="text-sm text-gray-500">
                    {{ new Date(log.created_at).toLocaleString() }}
                  </p>
                </div>
                <Badge :color="getStatusColor(log.status)">
                  {{ log.status }}
                </Badge>
              </div>
              <div v-if="log.response" class="mt-2">
                <p class="text-sm text-gray-700">{{ log.response }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import Button from '@/Components/Button.vue'
import Label from '@/Components/Label.vue'
import InputError from '@/Components/InputError.vue'
import Badge from '@/Components/Badge.vue'

const props = defineProps({
  recentQueries: Array,
})

const result = ref('')
const form = useForm({
  query: '',
})

const submitQuery = () => {
  form.post(route('query.submit'), {
    onSuccess: (response) => {
      result.value = response.data.result
      form.reset()
    },
  })
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'yellow',
    processing: 'blue',
    completed: 'green',
    failed: 'red',
  }
  return colors[status] || 'gray'
}
</script> 