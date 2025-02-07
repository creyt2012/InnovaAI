<template>
  <TransitionRoot
    appear
    :show="modelValue"
    as="template"
    enter="transform ease-out duration-300 transition"
    enter-from="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to="translate-y-0 opacity-100 sm:translate-x-0"
    leave="transition ease-in duration-100"
    leave-from="opacity-100"
    leave-to="opacity-0"
  >
    <div
      :class="[
        'pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg shadow-lg ring-1',
        'transition-all duration-200',
        variantClasses[variant]
      ]"
    >
      <div class="p-4">
        <div class="flex items-start">
          <!-- Icon -->
          <div class="flex-shrink-0">
            <component
              :is="iconComponent"
              class="h-6 w-6"
              :class="iconColorClass"
              aria-hidden="true"
            />
          </div>

          <!-- Content -->
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p
              v-if="title"
              class="text-sm font-medium text-gray-900 dark:text-white"
            >
              {{ title }}
            </p>
            <p
              :class="[
                'text-sm',
                title ? 'mt-1' : '',
                'text-gray-500 dark:text-gray-400'
              ]"
            >
              {{ message }}
            </p>
          </div>

          <!-- Close Button -->
          <div class="ml-4 flex flex-shrink-0">
            <button
              type="button"
              class="inline-flex rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
              @click="close"
            >
              <span class="sr-only">Close</span>
              <XMarkIcon class="h-5 w-5" aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>

      <!-- Progress Bar (if auto-dismiss) -->
      <div
        v-if="duration"
        class="h-1 bg-gray-100 dark:bg-gray-800"
      >
        <div
          :class="[
            'h-full transition-all duration-200',
            progressColorClass
          ]"
          :style="{ width: `${progress}%` }"
        />
      </div>
    </div>
  </TransitionRoot>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { TransitionRoot } from '@headlessui/vue'
import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    required: true
  },
  variant: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'info', 'warning', 'error'].includes(value)
  },
  duration: {
    type: Number,
    default: 0 // 0 means no auto-dismiss
  }
})

const emit = defineEmits(['update:modelValue'])

const progress = ref(100)
let progressInterval = null
let dismissTimeout = null

const variantClasses = {
  success: 'bg-green-50 dark:bg-green-900/20 ring-green-500/10 dark:ring-green-500/20',
  info: 'bg-blue-50 dark:bg-blue-900/20 ring-blue-500/10 dark:ring-blue-500/20',
  warning: 'bg-yellow-50 dark:bg-yellow-900/20 ring-yellow-500/10 dark:ring-yellow-500/20',
  error: 'bg-red-50 dark:bg-red-900/20 ring-red-500/10 dark:ring-red-500/20'
}

const iconComponent = computed(() => ({
  success: CheckCircleIcon,
  info: InformationCircleIcon,
  warning: ExclamationTriangleIcon,
  error: XCircleIcon
}[props.variant]))

const iconColorClass = computed(() => ({
  success: 'text-green-400 dark:text-green-500',
  info: 'text-blue-400 dark:text-blue-500',
  warning: 'text-yellow-400 dark:text-yellow-500',
  error: 'text-red-400 dark:text-red-500'
}[props.variant]))

const progressColorClass = computed(() => ({
  success: 'bg-green-500/20',
  info: 'bg-blue-500/20',
  warning: 'bg-yellow-500/20',
  error: 'bg-red-500/20'
}[props.variant]))

const close = () => {
  emit('update:modelValue', false)
}

const startProgress = () => {
  if (!props.duration) return

  const step = 100 / (props.duration / 100) // Update every 100ms
  progressInterval = setInterval(() => {
    progress.value = Math.max(0, progress.value - step)
  }, 100)

  dismissTimeout = setTimeout(() => {
    close()
  }, props.duration)
}

onMounted(() => {
  if (props.modelValue) {
    startProgress()
  }
})

onBeforeUnmount(() => {
  if (progressInterval) clearInterval(progressInterval)
  if (dismissTimeout) clearTimeout(dismissTimeout)
})

watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    progress.value = 100
    startProgress()
  } else {
    if (progressInterval) clearInterval(progressInterval)
    if (dismissTimeout) clearTimeout(dismissTimeout)
  }
})
</script> 