<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
          <th
            v-for="column in columns"
            :key="column.key"
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
          >
            <div class="flex items-center space-x-1">
              <span>{{ column.label }}</span>
              <button
                v-if="column.sortable"
                @click="$emit('sort', column)"
                class="text-gray-400 hover:text-gray-500"
              >
                <ChevronUpDownIcon class="h-4 w-4" />
              </button>
            </div>
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
        <tr v-for="(row, index) in data" :key="index">
          <td
            v-for="column in columns"
            :key="column.key"
            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300"
          >
            <slot :name="`cell-${column.key}`" :row="row">
              {{ row[column.key] }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="px-6 py-4 flex items-center justify-between border-t dark:border-gray-700">
      <div class="flex-1 flex justify-between sm:hidden">
        <Button
          :disabled="page === 1"
          @click="$emit('update:page', page - 1)"
        >
          Previous
        </Button>
        <Button
          :disabled="page >= totalPages"
          @click="$emit('update:page', page + 1)"
        >
          Next
        </Button>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700 dark:text-gray-400">
            Showing
            <span class="font-medium">{{ startIndex }}</span>
            to
            <span class="font-medium">{{ endIndex }}</span>
            of
            <span class="font-medium">{{ total }}</span>
            results
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            <!-- Pagination buttons -->
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ChevronUpDownIcon } from '@heroicons/vue/24/outline'
import Button from './Button.vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true
  },
  data: {
    type: Array,
    required: true
  },
  total: {
    type: Number,
    required: true
  },
  page: {
    type: Number,
    required: true
  },
  perPage: {
    type: Number,
    required: true
  }
})

const startIndex = computed(() => (props.page - 1) * props.perPage + 1)
const endIndex = computed(() => Math.min(props.page * props.perPage, props.total))
const totalPages = computed(() => Math.ceil(props.total / props.perPage))
</script> 