<template>
  <div class="relative">
    <label
      v-if="label"
      :for="id"
      class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="relative">
      <select
        :id="id"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg dark:bg-gray-700 dark:text-white"
      >
        <option v-if="placeholder" value="">{{ placeholder }}</option>
        <option
          v-for="option in options"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>

      <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
        <ChevronUpDownIcon
          class="h-5 w-5 text-gray-400"
          aria-hidden="true"
        />
      </div>
    </div>

    <p
      v-if="error"
      class="mt-2 text-sm text-red-600 dark:text-red-400"
    >
      {{ error }}
    </p>

    <p
      v-else-if="help"
      class="mt-2 text-sm text-gray-500 dark:text-gray-400"
    >
      {{ help }}
    </p>
  </div>
</template>

<script setup>
import { ChevronUpDownIcon } from '@heroicons/vue/24/outline'

defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  help: {
    type: String,
    default: ''
  },
  id: {
    type: String,
    default: () => `select-${Math.random().toString(36).substr(2, 9)}`
  }
})

defineEmits(['update:modelValue'])
</script> 