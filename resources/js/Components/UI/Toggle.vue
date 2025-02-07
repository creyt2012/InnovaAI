<template>
  <Switch
    :model-value="modelValue"
    :class="[
      'relative inline-flex shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out',
      'focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2',
      'disabled:cursor-not-allowed disabled:opacity-50',
      sizeClasses[size],
      modelValue ? enabledClasses[variant] : disabledClasses
    ]"
    :disabled="disabled"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <span class="sr-only">
      <slot>Toggle</slot>
    </span>
    <span
      aria-hidden="true"
      :class="[
        'pointer-events-none inline-block transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
        modelValue ? translateClasses[size].on : translateClasses[size].off,
        sizeClasses[size].handle
      ]"
    />
  </Switch>
</template>

<script setup>
import { Switch } from '@headlessui/vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'success', 'danger'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])

const sizeClasses = {
  sm: {
    switch: 'h-5 w-9',
    handle: 'h-3.5 w-3.5'
  },
  md: {
    switch: 'h-6 w-11',
    handle: 'h-4.5 w-4.5'
  },
  lg: {
    switch: 'h-7 w-14',
    handle: 'h-5.5 w-5.5'
  }
}

const translateClasses = {
  sm: {
    off: 'translate-x-0.5',
    on: 'translate-x-[18px]'
  },
  md: {
    off: 'translate-x-0.5',
    on: 'translate-x-[22px]'
  },
  lg: {
    off: 'translate-x-1',
    on: 'translate-x-[30px]'
  }
}

const enabledClasses = {
  primary: 'bg-primary-600 dark:bg-primary-500',
  success: 'bg-green-600 dark:bg-green-500',
  danger: 'bg-red-600 dark:bg-red-500'
}

const disabledClasses = 'bg-gray-200 dark:bg-gray-700'
</script> 