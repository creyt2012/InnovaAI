<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <component 
            :is="icon"
            class="h-6 w-6 text-gray-400"
          />
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 truncate">
              {{ title }}
            </dt>
            <dd class="flex items-baseline">
              <div class="text-2xl font-semibold text-gray-900">
                {{ value }}
              </div>
              <div v-if="change !== undefined"
                   :class="[
                     change >= 0 ? 'text-green-600' : 'text-red-600',
                     'ml-2 flex items-baseline text-sm font-semibold'
                   ]">
                <ArrowUpIcon v-if="change >= 0"
                  class="self-center flex-shrink-0 h-5 w-5"
                />
                <ArrowDownIcon v-else
                  class="self-center flex-shrink-0 h-5 w-5"
                />
                <span class="sr-only">
                  {{ change >= 0 ? 'Increased' : 'Decreased' }} by
                </span>
                {{ Math.abs(change) }}%
              </div>
            </dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  ArrowUpIcon,
  ArrowDownIcon,
  UserGroupIcon,
  UserCircleIcon,
  ChatBubbleLeftIcon
} from '@heroicons/vue/24/outline'

defineProps({
  title: String,
  value: [Number, String],
  change: Number,
  icon: {
    type: String,
    default: 'UserGroupIcon'
  }
})
</script> 