<template>
  <Switch
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :class="[
      modelValue ? 'bg-indigo-600' : 'bg-gray-200',
      'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
    ]"
  >
    <span class="sr-only">Toggle</span>
    <span
      :class="[
        modelValue ? 'translate-x-5' : 'translate-x-0',
        'pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200'
      ]"
    >
      <span
        :class="[
          modelValue
            ? 'opacity-0 ease-out duration-100'
            : 'opacity-100 ease-in duration-200',
          'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity'
        ]"
        aria-hidden="true"
      >
        <XMarkIcon class="h-3 w-3 text-gray-400" />
      </span>
      <span
        :class="[
          modelValue
            ? 'opacity-100 ease-in duration-200'
            : 'opacity-0 ease-out duration-100',
          'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity'
        ]"
        aria-hidden="true"
      >
        <CheckIcon class="h-3 w-3 text-indigo-600" />
      </span>
    </span>
  </Switch>
</template>

<script setup>
import { Switch } from '@headlessui/vue'
import { XMarkIcon, CheckIcon } from '@heroicons/vue/20/solid'

defineProps({
  modelValue: Boolean
})

defineEmits(['update:modelValue'])
</script> 