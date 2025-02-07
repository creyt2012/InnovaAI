<template>
  <UserLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Cài đặt chung -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="p-6">
          <h2 class="text-lg font-medium mb-4">{{ $t('settings.general') }}</h2>
          
          <div class="space-y-4">
            <!-- Dark Mode -->
            <div class="flex items-center justify-between">
              <div>
                <label class="font-medium">{{ $t('settings.dark_mode') }}</label>
                <p class="text-sm text-gray-500">{{ $t('settings.dark_mode_desc') }}</p>
              </div>
              <Toggle v-model="settings.darkMode" @change="updateSettings" />
            </div>

            <!-- Ngôn ngữ -->
            <div>
              <label class="font-medium">{{ $t('settings.language') }}</label>
              <Select
                v-model="settings.language"
                :options="languageOptions"
                class="mt-1"
                @change="updateSettings"
              />
            </div>

            <!-- Thông báo -->
            <div>
              <label class="font-medium">{{ $t('settings.notifications') }}</label>
              <div class="mt-2 space-y-2">
                <Checkbox
                  v-model="settings.emailNotifications"
                  label="Email notifications"
                />
                <Checkbox
                  v-model="settings.pushNotifications"
                  label="Push notifications"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cài đặt bảo mật -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="p-6">
          <h2 class="text-lg font-medium mb-4">{{ $t('settings.security') }}</h2>
          
          <div class="space-y-4">
            <!-- 2FA -->
            <div>
              <div class="flex items-center justify-between">
                <div>
                  <label class="font-medium">{{ $t('settings.two_factor') }}</label>
                  <p class="text-sm text-gray-500">{{ $t('settings.two_factor_desc') }}</p>
                </div>
                <Button
                  v-if="!twoFactorEnabled"
                  @click="enableTwoFactor"
                >
                  {{ $t('settings.enable') }}
                </Button>
                <Button
                  v-else
                  variant="danger"
                  @click="disableTwoFactor"
                >
                  {{ $t('settings.disable') }}
                </Button>
              </div>

              <!-- QR Code setup -->
              <div v-if="showTwoFactorSetup" class="mt-4">
                <img :src="twoFactorQR" alt="2FA QR Code" class="mb-4" />
                <Input
                  v-model="twoFactorCode"
                  type="text"
                  placeholder="Enter verification code"
                />
                <div class="mt-2 flex space-x-2">
                  <Button @click="confirmTwoFactor">Confirm</Button>
                  <Button
                    variant="secondary"
                    @click="showTwoFactorSetup = false"
                  >
                    Cancel
                  </Button>
                </div>
              </div>
            </div>

            <!-- API Keys -->
            <div>
              <label class="font-medium">{{ $t('settings.api_keys') }}</label>
              <div class="mt-2">
                <Button @click="showApiKeyModal = true">
                  {{ $t('settings.generate_key') }}
                </Button>
              </div>
              
              <div v-if="apiKeys.length" class="mt-4">
                <table class="min-w-full">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Created</th>
                      <th>Last Used</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="key in apiKeys" :key="key.id">
                      <td>{{ key.name }}</td>
                      <td>{{ formatDate(key.created_at) }}</td>
                      <td>{{ formatDate(key.last_used_at) }}</td>
                      <td>
                        <Button
                          variant="danger"
                          size="sm"
                          @click="revokeApiKey(key)"
                        >
                          {{ $t('settings.revoke') }}
                        </Button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sessions -->
      <div class="bg-white shadow rounded-lg">
        <div class="p-6">
          <h2 class="text-lg font-medium mb-4">{{ $t('settings.sessions') }}</h2>
          
          <div class="space-y-4">
            <div v-for="session in activeSessions" :key="session.id"
                 class="flex items-center justify-between">
              <div>
                <div class="font-medium">{{ session.device }}</div>
                <div class="text-sm text-gray-500">
                  {{ session.ip }} · {{ formatDate(session.last_active) }}
                </div>
              </div>
              <Button
                variant="danger"
                size="sm"
                @click="terminateSession(session)"
                :disabled="session.is_current"
              >
                {{ $t('settings.terminate') }}
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- API Key Modal -->
    <Modal v-model="showApiKeyModal">
      <div class="p-6">
        <h3 class="text-lg font-medium mb-4">
          {{ $t('settings.new_api_key') }}
        </h3>
        
        <form @submit.prevent="generateApiKey">
          <div class="space-y-4">
            <div>
              <Label>{{ $t('settings.key_name') }}</Label>
              <Input
                v-model="newApiKey.name"
                required
                class="mt-1"
              />
            </div>

            <div>
              <Label>{{ $t('settings.expiration') }}</Label>
              <Select
                v-model="newApiKey.expiration"
                :options="expirationOptions"
                class="mt-1"
              />
            </div>

            <div class="flex justify-end space-x-2">
              <Button
                type="button"
                variant="secondary"
                @click="showApiKeyModal = false"
              >
                {{ $t('common.cancel') }}
              </Button>
              <Button type="submit">
                {{ $t('settings.generate') }}
              </Button>
            </div>
          </div>
        </form>
      </div>
    </Modal>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import { formatDate } from '@/utils/format'

const settings = ref({
  darkMode: false,
  language: 'en',
  emailNotifications: true,
  pushNotifications: false
})

const twoFactorEnabled = ref(false)
const showTwoFactorSetup = ref(false)
const twoFactorQR = ref('')
const twoFactorCode = ref('')
const apiKeys = ref([])
const activeSessions = ref([])
const showApiKeyModal = ref(false)

const newApiKey = ref({
  name: '',
  expiration: '30d'
})

const languageOptions = [
  { value: 'en', label: 'English' },
  { value: 'vi', label: 'Tiếng Việt' }
]

const expirationOptions = [
  { value: '7d', label: '7 days' },
  { value: '30d', label: '30 days' },
  { value: '90d', label: '90 days' },
  { value: '365d', label: '1 year' },
  { value: 'never', label: 'Never' }
]

onMounted(async () => {
  // Load user settings
  const response = await axios.get(route('settings.index'))
  settings.value = response.data.settings
  twoFactorEnabled.value = response.data.two_factor_enabled
  apiKeys.value = response.data.api_keys
  activeSessions.value = response.data.active_sessions
})

const updateSettings = async () => {
  await axios.post(route('settings.update'), settings.value)
}

const enableTwoFactor = async () => {
  const response = await axios.post(route('two-factor.enable'))
  twoFactorQR.value = response.data.qr_code
  showTwoFactorSetup.value = true
}

const confirmTwoFactor = async () => {
  try {
    await axios.post(route('two-factor.confirm'), {
      code: twoFactorCode.value
    })
    twoFactorEnabled.value = true
    showTwoFactorSetup.value = false
  } catch (error) {
    // Handle error
  }
}

const generateApiKey = async () => {
  try {
    const response = await axios.post(route('api-keys.generate'), newApiKey.value)
    apiKeys.value.push(response.data.key)
    showApiKeyModal.value = false
  } catch (error) {
    // Handle error
  }
}

const revokeApiKey = async (key) => {
  if (confirm('Are you sure you want to revoke this API key?')) {
    await axios.delete(route('api-keys.revoke', key.id))
    apiKeys.value = apiKeys.value.filter(k => k.id !== key.id)
  }
}

const terminateSession = async (session) => {
  if (confirm('Are you sure you want to terminate this session?')) {
    await axios.delete(route('sessions.terminate', session.id))
    activeSessions.value = activeSessions.value.filter(s => s.id !== session.id)
  }
}
</script> 