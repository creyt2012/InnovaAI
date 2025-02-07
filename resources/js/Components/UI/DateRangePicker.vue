<template>
  <Popover class="relative">
    <PopoverButton class="w-full">
      <Input
        :modelValue="displayValue"
        readonly
        placeholder="Select date range"
        :leadingIcon="CalendarIcon"
      />
    </PopoverButton>

    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="translate-y-1 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-1 opacity-0"
    >
      <PopoverPanel class="absolute z-10 mt-1 w-screen max-w-sm">
        <div class="overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5">
          <div class="p-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label>Start Date</Label>
                <Input
                  v-model="startDate"
                  type="date"
                />
              </div>
              <div>
                <Label>End Date</Label>
                <Input
                  v-model="endDate"
                  type="date"
                />
              </div>
            </div>

            <div class="mt-4 flex justify-end space-x-2">
              <Button
                variant="ghost"
                @click="clear"
              >
                Clear
              </Button>
              <Button
                @click="apply"
              >
                Apply
              </Button>
            </div>
          </div>
        </div>
      </PopoverPanel>
    </transition>
  </Popover>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { CalendarIcon } from '@heroicons/vue/24/outline'
import { format } from 'date-fns'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({ start: null, end: null })
  }
})

const emit = defineEmits(['update:modelValue'])

const startDate = ref(props.modelValue.start)
const endDate = ref(props.modelValue.end)

const displayValue = computed(() => {
  if (!startDate.value || !endDate.value) return ''
  return `${format(new Date(startDate.value), 'MMM d, yyyy')} - ${format(new Date(endDate.value), 'MMM d, yyyy')}`
})

const apply = () => {
  emit('update:modelValue', {
    start: startDate.value,
    end: endDate.value
  })
}

const clear = () => {
  startDate.value = null
  endDate.value = null
  emit('update:modelValue', { start: null, end: null })
}
</script> 