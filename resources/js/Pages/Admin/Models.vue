<template>
  <AdminLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
        Quản lý Models AI
      </h2>
    </template>

    <div class="space-y-6">
      <!-- Models List -->
      <Card v-for="(models, server) in modelsByServer" :key="server">
        <template #header>
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
              {{ server }}
            </h3>
            <Badge
              :variant="getServerStatus(server)"
              class="capitalize"
            >
              {{ getServerStatus(server) }}
            </Badge>
          </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="model in models"
            :key="model.id"
            class="relative p-4 rounded-xl border dark:border-gray-800"
          >
            <div class="flex items-start justify-between">
              <div>
                <h4 class="font-medium text-gray-900 dark:text-white">
                  {{ model.name }}
                </h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  {{ model.path }}
                </p>
              </div>
              <Toggle
                v-model="model.is_active"
                @update:model-value="toggleModelStatus(model)"
              />
            </div>

            <div class="mt-4 flex items-center space-x-4 text-sm">
              <Badge :variant="model.category.toLowerCase()">
                {{ model.category }}
              </Badge>
              <span class="text-gray-500 dark:text-gray-400">
                {{ model.context_length.toLocaleString() }} tokens
              </span>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2">
              <Button
                variant="outline"
                size="sm"
                @click="editModel(model)"
              >
                <PencilIcon class="h-4 w-4 mr-1" />
                Edit
              </Button>
              <Button
                variant="danger"
                size="sm"
                @click="deleteModel(model)"
              >
                <TrashIcon class="h-4 w-4 mr-1" />
                Delete
              </Button>
            </div>
          </div>
        </div>
      </Card>
    </div>

    <!-- Edit Model Modal -->
    <Modal
      v-if="editingModel"
      :show="!!editingModel"
      @close="editingModel = null"
    >
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
          Edit Model
        </h3>
        
        <form @submit.prevent="saveModel" class="mt-6 space-y-4">
          <div>
            <Label>Name</Label>
            <Input
              v-model="editingModel.name"
              type="text"
              required
            />
          </div>

          <div>
            <Label>Description</Label>
            <Textarea
              v-model="editingModel.description"
              rows="3"
            />
          </div>

          <div>
            <Label>Category</Label>
            <Select
              v-model="editingModel.category"
              :options="categoryOptions"
              required
            />
          </div>

          <div>
            <Label>Context Length</Label>
            <Input
              v-model.number="editingModel.context_length"
              type="number"
              min="1"
              required
            />
          </div>

          <div class="flex justify-end space-x-3">
            <Button
              variant="ghost"
              @click="editingModel = null"
            >
              Cancel
            </Button>
            <Button type="submit">
              Save Changes
            </Button>
          </div>
        </form>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { useModelStore } from '@/Stores/modelStore'
import axios from 'axios'

const modelStore = useModelStore()
const editingModel = ref(null)

// Group models by server
const modelsByServer = computed(() => {
  return modelStore.models.reduce((groups, model) => {
    const server = model.server
    const group = groups[server] || []
    group.push(model)
    groups[server] = group
    return groups
  }, {})
})

const categoryOptions = [
  { value: 'Base', label: 'Base' },
  { value: 'Chat', label: 'Chat' },
  { value: 'Code', label: 'Code' },
  { value: 'Instruct', label: 'Instruct' }
]

const getServerStatus = (server) => {
  const models = modelsByServer.value[server]
  const onlineModels = models.filter(m => m.status === 'online')
  
  if (onlineModels.length === 0) return 'error'
  if (onlineModels.length === models.length) return 'success'
  return 'warning'
}

const toggleModelStatus = async (model) => {
  try {
    await axios.patch(`/api/models/${model.id}/toggle`)
    await modelStore.fetchModels()
  } catch (error) {
    console.error('Failed to toggle model status:', error)
  }
}

const editModel = (model) => {
  editingModel.value = { ...model }
}

const saveModel = async () => {
  try {
    await axios.put(`/api/models/${editingModel.value.id}`, editingModel.value)
    await modelStore.fetchModels()
    editingModel.value = null
  } catch (error) {
    console.error('Failed to save model:', error)
  }
}

const deleteModel = async (model) => {
  if (!confirm('Are you sure you want to delete this model?')) return
  
  try {
    await axios.delete(`/api/models/${model.id}`)
    await modelStore.fetchModels()
  } catch (error) {
    console.error('Failed to delete model:', error)
  }
}

// Fetch models on mount
onMounted(() => {
  modelStore.fetchModels()
})
</script> 