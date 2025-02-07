<template>
  <div class="relative inline-block">
    <!-- Trigger -->
    <div
      ref="triggerRef"
      @mouseenter="show"
      @mouseleave="hide"
      @focus="show"
      @blur="hide"
    >
      <slot />
    </div>

    <!-- Tooltip -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="isVisible"
        ref="tooltipRef"
        role="tooltip"
        :class="[
          'absolute z-50 px-2 py-1 text-sm',
          'bg-gray-900 dark:bg-gray-800 text-white',
          'rounded-lg shadow-lg',
          'whitespace-nowrap',
          arrowClass
        ]"
        :style="positionStyle"
      >
        {{ content }}
        <!-- Arrow -->
        <div
          :class="[
            'absolute w-2 h-2 bg-gray-900 dark:bg-gray-800',
            'transform rotate-45',
            arrowPositionClass
          ]"
        />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useFloating } from '@floating-ui/vue'
import { offset, flip, shift } from '@floating-ui/core'

const props = defineProps({
  content: {
    type: String,
    required: true
  },
  position: {
    type: String,
    default: 'top',
    validator: (value) => ['top', 'right', 'bottom', 'left'].includes(value)
  },
  offset: {
    type: Number,
    default: 8
  }
})

const isVisible = ref(false)
const triggerRef = ref(null)
const tooltipRef = ref(null)

const {
  x,
  y,
  strategy,
  placement,
  middlewareData: { offset: offsetData }
} = useFloating(triggerRef, tooltipRef, {
  placement: props.position,
  middleware: [
    offset(props.offset),
    flip(),
    shift({ padding: 8 })
  ]
})

const positionStyle = computed(() => ({
  position: strategy.value,
  top: y.value ? `${y.value}px` : '',
  left: x.value ? `${x.value}px` : ''
}))

const arrowClass = computed(() => {
  const basePosition = placement.value.split('-')[0]
  return {
    'top': 'mb-2',
    'right': 'ml-2',
    'bottom': 'mt-2',
    'left': 'mr-2'
  }[basePosition]
})

const arrowPositionClass = computed(() => {
  const basePosition = placement.value.split('-')[0]
  return {
    'top': '-bottom-1',
    'right': '-left-1',
    'bottom': '-top-1',
    'left': '-right-1'
  }[basePosition]
})

const show = () => {
  isVisible.value = true
}

const hide = () => {
  isVisible.value = false
}

// Clean up
onBeforeUnmount(() => {
  isVisible.value = false
})
</script> 