<template>
  <div class="space-y-4">
    <!-- Model Groups -->
    <div v-for="(group, groupName) in modelGroups" :key="groupName" class="space-y-3">
      <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
        {{ groupName }}
      </h3>
      
      <!-- Model Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <button
          v-for="model in group"
          :key="model.id"
          :class="[
            'relative flex flex-col p-4 w-full text-left',
            'rounded-xl border transition-all duration-200',
            'hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500',
            modelValue?.id === model.id 
              ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10'
              : 'border-gray-200 dark:border-gray-700 hover:border-primary-500/50',
            model.status === 'offline' && 'opacity-50 cursor-not-allowed'
          ]"
          :disabled="model.status === 'offline'"
          @click="selectModel(model)"
        >
          <!-- Model Info -->
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h4 class="font-medium text-gray-900 dark:text-white">
                {{ model.name }}
              </h4>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ model.description }}
              </p>
            </div>
            
            <!-- Status Badge -->
            <Badge
              :variant="model.status === 'online' ? 'success' : 'error'"
              size="sm"
              class="ml-2"
            >
              {{ model.status }}
            </Badge>
          </div>

          <!-- Model Specs -->
          <div class="mt-4 flex items-center space-x-4 text-sm">
            <div class="flex items-center text-gray-500 dark:text-gray-400">
              <CpuChipIcon class="h-4 w-4 mr-1" />
              {{ model.parameters }}
            </div>
            <div class="flex items-center text-gray-500 dark:text-gray-400">
              <ServerIcon class="h-4 w-4 mr-1" />
              {{ model.server }}
            </div>
            <div class="flex items-center text-gray-500 dark:text-gray-400">
              <ClockIcon class="h-4 w-4 mr-1" />
              {{ model.latency }}ms
            </div>
          </div>

          <!-- Selected Indicator -->
          <div
            v-if="modelValue?.id === model.id"
            class="absolute -top-px -right-px p-1 bg-primary-500 rounded-bl-xl rounded-tr-xl"
          >
            <CheckIcon class="h-4 w-4 text-white" />
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { CpuChipIcon, ServerIcon, ClockIcon, CheckIcon } from '@heroicons/vue/24/outline'
import Badge from '@/Components/UI/Badge.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    default: null
  },
  models: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['update:modelValue'])

// Group models by category
const modelGroups = computed(() => {
  return props.models.reduce((groups, model) => {
    const group = groups[model.category] || []
    group.push(model)
    groups[model.category] = group
    return groups
  }, {})
})

const selectModel = (model) => {
  if (model.status === 'offline') return
  emit('update:modelValue', model)
}
</script> 