<template>
  <div>
    <label
      v-if="label"
      :for="id"
      class="block text-sm font-medium text-gray-700 dark:text-gray-300"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="mt-1 relative">
      <div
        v-if="leadingIcon"
        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
      >
        <component
          :is="leadingIcon"
          class="h-5 w-5 text-gray-400"
          aria-hidden="true"
        />
      </div>

      <input
        :id="id"
        :value="modelValue"
        :type="type"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :class="[
          'block w-full rounded-lg',
          'border-gray-300 dark:border-gray-600',
          'focus:ring-primary-500 focus:border-primary-500',
          'dark:bg-gray-700 dark:text-white',
          { 'pl-10': leadingIcon },
          { 'pr-10': trailingIcon }
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
      />

      <div
        v-if="trailingIcon"
        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
      >
        <component
          :is="trailingIcon"
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
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  leadingIcon: {
    type: [Object, Function],
    default: null
  },
  trailingIcon: {
    type: [Object, Function],
    default: null
  },
  id: {
    type: String,
    default: () => `input-${Math.random().toString(36).substr(2, 9)}`
  }
})

defineEmits(['update:modelValue'])
</script> 