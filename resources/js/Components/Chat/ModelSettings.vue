<template>
  <div class="space-y-6">
    <!-- Model Selection -->
    <div>
      <h3 class="text-lg font-medium text-gray-900 dark:text-white">
        Chọn Model AI
      </h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Chọn model AI phù hợp với nhu cầu của bạn
      </p>

      <div class="mt-4">
        <div class="space-y-4">
          <div v-for="(models, category) in modelsByCategory" :key="category">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              {{ category }}
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <button
                v-for="model in models"
                :key="model.id"
                :class="[
                  'relative flex flex-col p-4 w-full text-left',
                  'rounded-xl border transition-all duration-200',
                  'hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500',
                  selectedModel?.id === model.id 
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
                    {{ model.context_length.toLocaleString() }} tokens
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
                  v-if="selectedModel?.id === model.id"
                  class="absolute -top-px -right-px p-1 bg-primary-500 rounded-bl-xl rounded-tr-xl"
                >
                  <CheckIcon class="h-4 w-4 text-white" />
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Model Parameters -->
    <div v-if="selectedModel">
      <h4 class="font-medium text-gray-900 dark:text-white">
        Tham số Model
      </h4>
      
      <div class="mt-4 space-y-4">
        <!-- Temperature -->
        <div>
          <div class="flex items-center justify-between">
            <Label>Temperature</Label>
            <span class="text-sm text-gray-500">{{ temperature }}</span>
          </div>
          <Slider
            v-model="temperature"
            :min="0"
            :max="2"
            :step="0.1"
            class="mt-2"
          />
          <p class="mt-1 text-sm text-gray-500">
            Điều chỉnh độ sáng tạo của câu trả lời. Giá trị cao hơn sẽ cho kết quả đa dạng hơn.
          </p>
        </div>

        <!-- Max Length -->
        <div>
          <div class="flex items-center justify-between">
            <Label>Độ dài tối đa</Label>
            <span class="text-sm text-gray-500">{{ maxLength }}</span>
          </div>
          <Slider
            v-model="maxLength"
            :min="100"
            :max="4000"
            :step="100"
            class="mt-2"
          />
          <p class="mt-1 text-sm text-gray-500">
            Số tokens tối đa cho mỗi câu trả lời.
          </p>
        </div>

        <!-- Top P -->
        <div>
          <div class="flex items-center justify-between">
            <Label>Top P</Label>
            <span class="text-sm text-gray-500">{{ topP }}</span>
          </div>
          <Slider
            v-model="topP"
            :min="0"
            :max="1"
            :step="0.05"
            class="mt-2"
          />
          <p class="mt-1 text-sm text-gray-500">
            Điều chỉnh độ tập trung của câu trả lời. Giá trị thấp hơn sẽ cho kết quả tập trung hơn.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useModelStore } from '@/Stores/modelStore'
import ModelSelector from './ModelSelector.vue'
import Label from '@/Components/UI/Label.vue'
import Slider from '@/Components/UI/Slider.vue'
import Badge from '@/Components/UI/Badge.vue'
import { CpuChipIcon, ServerIcon, ClockIcon, CheckIcon } from '@heroicons/vue/24/outline'

const modelStore = useModelStore()

const selectedModel = computed({
  get: () => modelStore.selectedModel,
  set: (value) => modelStore.selectModel(value)
})

const models = computed(() => modelStore.models)

// Model parameters with persistence
const temperature = ref(0.7)
const maxLength = ref(2000)
const topP = ref(0.95)

// Watch for changes and save to backend
watch([temperature, maxLength, topP], async ([temp, length, p]) => {
  try {
    await axios.post('/api/user/preferences/parameters', {
      temperature: temp,
      max_length: length,
      top_p: p
    })
  } catch (error) {
    console.error('Failed to save parameters:', error)
  }
}, { deep: true })

const modelsByCategory = computed(() => {
  return models.value.reduce((groups, model) => {
    const category = model.category
    const group = groups[category] || []
    group.push(model)
    groups[category] = group
    return groups
  }, {})
})

const selectModel = (model) => {
  selectedModel.value = model
}
</script> 