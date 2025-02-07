<template>
  <div class="relative">
    <div class="flex items-center">
      <input
        type="text"
        readonly
        :value="formatDateRange"
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        @click="showPicker = true"
      />
      <CalendarIcon 
        class="absolute right-3 h-5 w-5 text-gray-400"
        @click="showPicker = true"
      />
    </div>

    <div v-if="showPicker"
         class="absolute z-10 mt-1 w-auto bg-white rounded-md shadow-lg">
      <div class="p-4">
        <div class="flex space-x-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">
              Start Date
            </label>
            <input
              type="date"
              v-model="startDate"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">
              End Date
            </label>
            <input
              type="date"
              v-model="endDate"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
          </div>
        </div>

        <div class="mt-4 flex justify-end space-x-2">
          <Button
            variant="secondary"
            size="sm"
            @click="showPicker = false"
          >
            Cancel
          </Button>
          <Button
            size="sm"
            @click="applyDateRange"
          >
            Apply
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { CalendarIcon } from '@heroicons/vue/20/solid'
import { format } from 'date-fns'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({
      start: null,
      end: null
    })
  }
})

const emit = defineEmits(['update:modelValue'])

const showPicker = ref(false)
const startDate = ref(props.modelValue.start)
const endDate = ref(props.modelValue.end)

const formatDateRange = computed(() => {
  if (!startDate.value || !endDate.value) return ''
  return `${format(new Date(startDate.value), 'MMM d, yyyy')} - ${format(new Date(endDate.value), 'MMM d, yyyy')}`
})

function applyDateRange() {
  emit('update:modelValue', {
    start: startDate.value,
    end: endDate.value
  })
  showPicker.value = false
}
</script> 