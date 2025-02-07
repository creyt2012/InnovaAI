<template>
  <div class="max-w-2xl mx-auto">
    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <Label for="name">Name</Label>
        <Input
          id="name"
          v-model="form.name"
          type="text"
          class="mt-1 block w-full"
          required
        />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>

      <div>
        <Label for="endpoint">API Endpoint</Label>
        <Input
          id="endpoint"
          v-model="form.endpoint"
          type="url"
          class="mt-1 block w-full"
          required
        />
        <InputError :message="form.errors.endpoint" class="mt-2" />
      </div>

      <div>
        <Label for="api_key">API Key</Label>
        <Input
          id="api_key"
          v-model="form.api_key"
          type="password"
          class="mt-1 block w-full"
          required
        />
        <InputError :message="form.errors.api_key" class="mt-2" />
      </div>

      <div>
        <Label for="configuration">Configuration (JSON)</Label>
        <textarea
          id="configuration"
          v-model="configurationText"
          rows="4"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          @input="updateConfiguration"
        ></textarea>
        <InputError :message="form.errors.configuration" class="mt-2" />
      </div>

      <div class="flex items-center">
        <Checkbox
          id="is_active"
          v-model:checked="form.is_active"
        />
        <Label for="is_active" class="ml-2">Active</Label>
      </div>

      <div class="flex justify-end space-x-3">
        <Button
          type="button"
          variant="secondary"
          :href="route('admin.apis.index')"
        >
          Cancel
        </Button>
        <Button
          type="submit"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Saving...' : 'Save' }}
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import InputError from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'

const props = defineProps({
  api: {
    type: Object,
    default: null
  }
})

const form = useForm({
  name: props.api?.name ?? '',
  endpoint: props.api?.endpoint ?? '',
  api_key: props.api?.api_key ?? '',
  configuration: props.api?.configuration ?? {},
  is_active: props.api?.is_active ?? true
})

const configurationText = ref(
  props.api?.configuration 
    ? JSON.stringify(props.api.configuration, null, 2)
    : '{}'
)

const updateConfiguration = () => {
  try {
    form.configuration = JSON.parse(configurationText.value)
  } catch (e) {
    form.errors.configuration = 'Invalid JSON format'
  }
}

const submit = () => {
  if (props.api) {
    form.put(route('admin.apis.update', props.api.id))
  } else {
    form.post(route('admin.apis.store'))
  }
}
</script> 