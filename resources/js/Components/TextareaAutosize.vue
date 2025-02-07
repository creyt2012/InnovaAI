<template>
  <div class="relative">
    <div
      ref="ghost"
      class="invisible absolute top-0 left-0 right-0 overflow-hidden 
             whitespace-pre-wrap break-words"
      :style="{
        'min-height': `${minHeight}px`,
        'max-height': `${maxHeight}px`,
        'font-family': 'inherit',
        'font-size': 'inherit',
        'line-height': 'inherit',
        'padding': padding
      }"
    >{{ modelValue }}{{ suffix }}</div>
    
    <textarea
      ref="textarea"
      :value="modelValue"
      @input="onInput"
      :placeholder="placeholder"
      :style="{
        height: `${height}px`,
        'min-height': `${minHeight}px`,
        'max-height': `${maxHeight}px`,
        'resize': resize
      }"
      class="w-full rounded-xl bg-gray-50 dark:bg-gray-800 
             border border-gray-200 dark:border-gray-700
             focus:ring-2 focus:ring-primary-500/20 
             focus:border-primary-500 dark:focus:border-primary-400
             placeholder-gray-400 dark:placeholder-gray-500
             text-gray-900 dark:text-white
             shadow-sm dark:shadow-none
             transition-all duration-200"
      v-bind="$attrs"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  minHeight: {
    type: Number,
    default: 32
  },
  maxHeight: {
    type: Number,
    default: 400
  },
  placeholder: String,
  resize: {
    type: String,
    default: 'none'
  },
  padding: {
    type: String,
    default: '0.75rem'
  },
  suffix: {
    type: String,
    default: '\n'
  }
})

const emit = defineEmits(['update:modelValue'])

const ghost = ref(null)
const textarea = ref(null)
const height = ref(props.minHeight)

const updateHeight = () => {
  if (!ghost.value || !textarea.value) return

  const ghostHeight = ghost.value.scrollHeight
  height.value = Math.min(Math.max(ghostHeight, props.minHeight), props.maxHeight)
}

const onInput = (event) => {
  const value = event.target.value
  emit('update:modelValue', value)
}

watch(() => props.modelValue, updateHeight)

onMounted(() => {
  updateHeight()
})
</script>

<style scoped>
textarea {
  overflow-y: auto;
  width: 100%;
  position: relative;
  appearance: none;
  resize: none;
}
</style> 