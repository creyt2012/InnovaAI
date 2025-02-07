<template>
  <span
    :class="[
      'inline-flex items-center justify-center',
      'transition-all duration-200',
      sizeClasses[size],
      variantClasses[variant],
      rounded ? 'rounded-full' : 'rounded-md',
      className
    ]"
  >
    <component
      :is="icon"
      v-if="icon"
      class="h-3.5 w-3.5"
      :class="{ 'mr-1': $slots.default }"
    />
    <slot />
  </span>
</template>

<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => [
      'default',
      'primary',
      'success',
      'warning',
      'danger',
      'info'
    ].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  icon: {
    type: [Object, Function],
    default: null
  },
  rounded: {
    type: Boolean,
    default: false
  },
  className: {
    type: String,
    default: ''
  }
})

const sizeClasses = {
  sm: 'px-2 py-0.5 text-xs',
  md: 'px-2.5 py-0.5 text-sm',
  lg: 'px-3 py-1 text-base'
}

const variantClasses = {
  default: `
    bg-gray-100 text-gray-800
    dark:bg-gray-800 dark:text-gray-300
    border border-gray-200 dark:border-gray-700
  `,
  primary: `
    bg-primary-50 text-primary-700
    dark:bg-primary-900/20 dark:text-primary-400
    border border-primary-200 dark:border-primary-800
  `,
  success: `
    bg-green-50 text-green-700
    dark:bg-green-900/20 dark:text-green-400
    border border-green-200 dark:border-green-800
  `,
  warning: `
    bg-yellow-50 text-yellow-700
    dark:bg-yellow-900/20 dark:text-yellow-400
    border border-yellow-200 dark:border-yellow-800
  `,
  danger: `
    bg-red-50 text-red-700
    dark:bg-red-900/20 dark:text-red-400
    border border-red-200 dark:border-red-800
  `,
  info: `
    bg-blue-50 text-blue-700
    dark:bg-blue-900/20 dark:text-blue-400
    border border-blue-200 dark:border-blue-800
  `
}
</script> 