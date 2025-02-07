<template>
  <AdminLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
        Site Settings
      </h2>
    </template>

    <div class="space-y-6">
      <Card>
        <form @submit.prevent="saveSettings" class="space-y-6">
          <!-- Basic Settings -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
              Basic Settings
            </h3>
            
            <div class="grid grid-cols-1 gap-6">
              <div>
                <Label>Application Name</Label>
                <Input
                  v-model="form.app_name"
                  type="text"
                  required
                />
              </div>

              <!-- Logo Upload -->
              <div>
                <Label>Logo</Label>
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0">
                    <img
                      :src="logoPreview || form.logo"
                      alt="Logo"
                      class="h-20 w-auto rounded-lg border dark:border-gray-700"
                    />
                  </div>
                  <div class="flex-1">
                    <Input
                      ref="logoInput"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleLogoUpload"
                    />
                    <Button
                      type="button"
                      variant="outline"
                      @click="logoInput?.click()"
                    >
                      Change Logo
                    </Button>
                    <p class="mt-2 text-sm text-gray-500">
                      Recommended size: 200x50px. Max file size: 2MB
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Hero Section -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
              Hero Section
            </h3>
            
            <div class="grid grid-cols-1 gap-6">
              <div>
                <Label>Hero Title</Label>
                <Input
                  v-model="form.hero_title"
                  type="text"
                />
              </div>

              <div>
                <Label>Hero Description</Label>
                <Textarea
                  v-model="form.hero_description"
                  rows="3"
                />
              </div>
            </div>
          </div>

          <!-- Features Section -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
              Features
            </h3>
            
            <div class="space-y-4">
              <div
                v-for="(feature, index) in form.features"
                :key="index"
                class="p-4 rounded-xl border dark:border-gray-800"
              >
                <div class="grid grid-cols-1 gap-4">
                  <div>
                    <Label>Title</Label>
                    <Input
                      v-model="feature.title"
                      type="text"
                    />
                  </div>

                  <div>
                    <Label>Description</Label>
                    <Input
                      v-model="feature.description"
                      type="text"
                    />
                  </div>

                  <div>
                    <Label>Icon</Label>
                    <Select
                      v-model="feature.icon"
                      :options="iconOptions"
                    />
                  </div>
                </div>

                <Button
                  v-if="form.features.length > 1"
                  type="button"
                  variant="danger"
                  size="sm"
                  class="mt-4"
                  @click="removeFeature(index)"
                >
                  Remove Feature
                </Button>
              </div>

              <Button
                type="button"
                variant="outline"
                @click="addFeature"
              >
                Add Feature
              </Button>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <Button
              type="submit"
              :loading="loading"
            >
              Save Changes
            </Button>
          </div>
        </form>
      </Card>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useSettingsStore } from '@/Stores/settingsStore'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { CpuChipIcon, ChatBubbleLeftRightIcon, SparklesIcon } from '@heroicons/vue/24/outline'

const settingsStore = useSettingsStore()
const loading = ref(false)
const logoInput = ref(null)
const logoPreview = ref(null)

const form = ref({
  app_name: '',
  logo: '',
  hero_title: '',
  hero_description: '',
  features: []
})

const iconOptions = [
  { value: 'CpuChipIcon', label: 'CPU' },
  { value: 'ChatBubbleLeftRightIcon', label: 'Chat' },
  { value: 'SparklesIcon', label: 'Sparkles' }
]

onMounted(async () => {
  await settingsStore.fetchSettings()
  form.value = { ...settingsStore.settings }
})

const handleLogoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      logoPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const addFeature = () => {
  form.value.features.push({
    title: '',
    description: '',
    icon: 'SparklesIcon'
  })
}

const removeFeature = (index) => {
  form.value.features.splice(index, 1)
}

const saveSettings = async () => {
  loading.value = true
  try {
    const formData = new FormData()
    
    // Append basic fields
    formData.append('app_name', form.value.app_name)
    formData.append('hero_title', form.value.hero_title)
    formData.append('hero_description', form.value.hero_description)
    
    // Append logo if changed
    if (logoInput.value?.files[0]) {
      formData.append('logo', logoInput.value.files[0])
    }
    
    // Append features as JSON
    formData.append('features', JSON.stringify(form.value.features))
    
    await settingsStore.updateSettings(formData)
    
    // Show success message
    toast.success('Settings updated successfully')
  } catch (error) {
    toast.error('Failed to update settings')
  } finally {
    loading.value = false
  }
}
</script> 