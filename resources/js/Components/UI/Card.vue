<template>
  <div 
    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden"
    :class="[
      hover && 'transition-shadow duration-200 hover:shadow-md',
      className
    ]"
  >
    <div 
      v-if="$slots.header"
      class="px-6 py-4 border-b dark:border-gray-700"
    >
      <slot name="header" />
    </div>

    <div class="p-6">
      <slot />
    </div>

    <div
      v-if="$slots.footer" 
      class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700"
    >
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => [
      'default',
      'glass',
      'outline',
      'flat'
    ].includes(value)
  },
  rounded: {
    type: String,
    default: 'lg',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'xl', '2xl'].includes(value)
  },
  padding: {
    type: String,
    default: 'default',
    validator: (value) => ['none', 'sm', 'default', 'lg'].includes(value)
  },
  hover: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  },
  className: {
    type: String,
    default: ''
  }
})

const variantClasses = {
  default: `
    bg-white dark:bg-gray-900
    border border-gray-200 dark:border-gray-800
    shadow-sm dark:shadow-gray-900/30
  `,
  glass: `
    backdrop-blur-lg
    bg-white/80 dark:bg-gray-900/80
    border border-gray-200/50 dark:border-gray-800/50
    shadow-xl dark:shadow-gray-900/30
  `,
  outline: `
    border border-gray-200 dark:border-gray-800
    bg-transparent
  `,
  flat: `
    bg-gray-50 dark:bg-gray-800
    border-none
  `
}

const roundedClasses = {
  none: 'rounded-none',
  sm: 'rounded-sm',
  md: 'rounded-md',
  lg: 'rounded-lg',
  xl: 'rounded-xl',
  '2xl': 'rounded-2xl'
}

const paddingClasses = {
  none: 'p-0',
  sm: 'p-3',
  default: 'p-4',
  lg: 'p-6'
}
</script> 