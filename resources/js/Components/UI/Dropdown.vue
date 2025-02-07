<template>
  <Menu as="div" class="relative inline-block text-left">
    <MenuButton
      :class="[
        'inline-flex items-center justify-center transition-colors duration-200',
        'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500',
        'dark:focus:ring-offset-gray-900',
        buttonClass || defaultButtonClass
      ]"
    >
      <slot name="trigger" />
    </MenuButton>

    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <MenuItems
        :class="[
          'absolute z-50 mt-2 w-56 origin-top-right',
          'bg-white dark:bg-gray-900',
          'border border-gray-200 dark:border-gray-800',
          'rounded-xl shadow-lg',
          'focus:outline-none',
          'divide-y divide-gray-100 dark:divide-gray-800',
          positionClass
        ]"
      >
        <div v-for="(group, index) in items" :key="index" class="py-1">
          <MenuItem v-for="item in group" :key="item.label" v-slot="{ active }">
            <button
              v-if="!item.href"
              :class="[
                'group flex w-full items-center px-4 py-2 text-sm',
                active
                  ? 'bg-gray-50 text-gray-900 dark:bg-gray-800/50 dark:text-white'
                  : 'text-gray-700 dark:text-gray-300',
                item.disabled && 'opacity-50 cursor-not-allowed'
              ]"
              :disabled="item.disabled"
              @click="item.onClick"
            >
              <component
                :is="item.icon"
                v-if="item.icon"
                :class="[
                  'mr-3 h-5 w-5',
                  active ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400 dark:text-gray-500',
                  item.iconClass
                ]"
                aria-hidden="true"
              />
              {{ item.label }}
            </button>

            <a
              v-else
              :href="item.href"
              :class="[
                'group flex w-full items-center px-4 py-2 text-sm',
                active
                  ? 'bg-gray-50 text-gray-900 dark:bg-gray-800/50 dark:text-white'
                  : 'text-gray-700 dark:text-gray-300'
              ]"
            >
              <component
                :is="item.icon"
                v-if="item.icon"
                :class="[
                  'mr-3 h-5 w-5',
                  active ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400 dark:text-gray-500',
                  item.iconClass
                ]"
                aria-hidden="true"
              />
              {{ item.label }}
            </a>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'

const props = defineProps({
  items: {
    type: Array,
    required: true
  },
  position: {
    type: String,
    default: 'right',
    validator: (value) => ['left', 'right'].includes(value)
  },
  buttonClass: {
    type: String,
    default: ''
  }
})

const defaultButtonClass = `
  inline-flex items-center px-4 py-2 text-sm font-medium
  text-gray-700 dark:text-gray-300
  bg-white dark:bg-gray-900
  border border-gray-300 dark:border-gray-700
  hover:bg-gray-50 dark:hover:bg-gray-800
  rounded-lg shadow-sm
`

const positionClass = computed(() => ({
  'right-0': props.position === 'right',
  'left-0': props.position === 'left'
}))
</script> 