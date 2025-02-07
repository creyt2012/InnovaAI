<template>
  <div>
    <!-- Tab List -->
    <div class="relative border-b dark:border-gray-800">
      <div
        class="flex space-x-8 overflow-x-auto scrollbar-none"
        role="tablist"
        ref="tabListRef"
      >
        <button
          v-for="tab in tabs"
          :key="tab.value"
          :class="[
            'relative min-w-0 flex-shrink-0 py-4 px-1',
            'text-sm font-medium focus:outline-none',
            tab.value === modelValue
              ? 'text-primary-600 dark:text-primary-400'
              : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
          ]"
          :aria-selected="tab.value === modelValue"
          :aria-controls="`tab-panel-${tab.value}`"
          role="tab"
          @click="$emit('update:modelValue', tab.value)"
        >
          <component
            :is="tab.icon"
            v-if="tab.icon"
            class="h-5 w-5 mr-2"
          />
          {{ tab.label }}
        </button>

        <!-- Active Tab Indicator -->
        <div
          class="absolute bottom-0 left-0 h-0.5 bg-primary-600 dark:bg-primary-400
                 transition-all duration-200"
          :style="indicatorStyle"
        />
      </div>
    </div>

    <!-- Tab Panels -->
    <div class="mt-6">
      <TransitionGroup
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-1"
      >
        <div
          v-for="tab in tabs"
          :key="tab.value"
          v-show="tab.value === modelValue"
          :aria-labelledby="`tab-${tab.value}`"
          role="tabpanel"
        >
          <component :is="tab.component" v-bind="tab.props" />
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    required: true
  },
  tabs: {
    type: Array,
    required: true,
    validator: (tabs) => {
      return tabs.every(tab => 
        tab.value !== undefined && 
        tab.label !== undefined && 
        tab.component !== undefined
      )
    }
  }
})

defineEmits(['update:modelValue'])

const tabListRef = ref(null)
const activeTabElement = ref(null)

const indicatorStyle = computed(() => {
  if (!activeTabElement.value) return {}
  
  const { offsetLeft, offsetWidth } = activeTabElement.value
  return {
    transform: `translateX(${offsetLeft}px)`,
    width: `${offsetWidth}px`
  }
})

watch(() => props.modelValue, async () => {
  await nextTick()
  updateActiveTabElement()
})

onMounted(() => {
  updateActiveTabElement()
})

const updateActiveTabElement = () => {
  if (!tabListRef.value) return
  
  const activeTab = tabListRef.value.querySelector(
    `[aria-selected="true"]`
  )
  if (activeTab) {
    activeTabElement.value = activeTab
  }
}
</script> 