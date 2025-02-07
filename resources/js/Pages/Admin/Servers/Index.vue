<template>
  <AdminLayout>
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">
          {{ $page.props.language.admin.servers }}
        </h1>
        <p class="mt-2 text-sm text-gray-700">
          {{ $page.props.language.admin.servers_description }}
        </p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <Button @click="showCreateModal = true">
          {{ $page.props.language.admin.servers_add }}
        </Button>
      </div>
    </div>

    <!-- Server Grid -->
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="server in servers.data" :key="server.id"
           class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div :class="[
                'flex-shrink-0 rounded-full h-3 w-3',
                {
                  'bg-green-400': server.status === 'active',
                  'bg-red-400': server.status === 'error',
                  'bg-gray-400': server.status === 'inactive'
                }
              ]" />
              <h3 class="ml-2 text-lg font-medium text-gray-900">
                {{ server.name }}
              </h3>
            </div>
            <Menu as="div" class="relative">
              <MenuButton class="flex items-center text-gray-400 hover:text-gray-600">
                <EllipsisVerticalIcon class="h-5 w-5" />
              </MenuButton>
              <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <MenuItems class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                  <MenuItem v-slot="{ active }">
                    <button
                      @click="editServer(server)"
                      :class="[
                        active ? 'bg-gray-100' : '',
                        'block w-full px-4 py-2 text-sm text-gray-700 text-left'
                      ]"
                    >
                      {{ $page.props.language.common.edit }}
                    </button>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <button
                      @click="testConnection(server)"
                      :class="[
                        active ? 'bg-gray-100' : '',
                        'block w-full px-4 py-2 text-sm text-gray-700 text-left'
                      ]"
                    >
                      {{ $page.props.language.admin.servers_test }}
                    </button>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <button
                      @click="deleteServer(server)"
                      :class="[
                        active ? 'bg-gray-100' : '',
                        'block w-full px-4 py-2 text-sm text-red-700 text-left'
                      ]"
                    >
                      {{ $page.props.language.common.delete }}
                    </button>
                  </MenuItem>
                </MenuItems>
              </transition>
            </Menu>
          </div>

          <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
            <div>
              <p class="text-gray-500">{{ $page.props.language.admin.servers_host }}</p>
              <p class="font-medium">{{ server.host }}</p>
            </div>
            <div>
              <p class="text-gray-500">{{ $page.props.language.admin.servers_port }}</p>
              <p class="font-medium">{{ server.port }}</p>
            </div>
            <div>
              <p class="text-gray-500">{{ $page.props.language.admin.servers_status }}</p>
              <p :class="[
                'font-medium',
                {
                  'text-green-600': server.status === 'active',
                  'text-red-600': server.status === 'error',
                  'text-gray-600': server.status === 'inactive'
                }
              ]">
                {{ server.status }}
              </p>
            </div>
            <div>
              <p class="text-gray-500">{{ $page.props.language.admin.servers_last_ping }}</p>
              <p class="font-medium">
                {{ server.last_ping_at ? formatDate(server.last_ping_at) : '-' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination :links="servers.links" class="mt-6" />

    <!-- Create/Edit Modal -->
    <Modal :show="showCreateModal || editingServer" @close="closeModal">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingServer 
            ? $page.props.language.admin.servers_edit 
            : $page.props.language.admin.servers_add }}
        </h3>

        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <Label for="name">{{ $page.props.language.admin.servers_name }}</Label>
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
            <Label for="host">{{ $page.props.language.admin.servers_host }}</Label>
            <Input
              id="host"
              v-model="form.host"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="form.errors.host" class="mt-2" />
          </div>

          <div>
            <Label for="port">{{ $page.props.language.admin.servers_port }}</Label>
            <Input
              id="port"
              v-model="form.port"
              type="number"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="form.errors.port" class="mt-2" />
          </div>

          <div>
            <Label for="configuration">{{ $page.props.language.admin.servers_config }}</Label>
            <textarea
              id="configuration"
              v-model="configurationText"
              rows="4"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              @input="updateConfiguration"
            ></textarea>
            <InputError :message="form.errors.configuration" class="mt-2" />
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <Button
              type="button"
              variant="secondary"
              @click="closeModal"
            >
              {{ $page.props.language.common.cancel }}
            </Button>
            <Button
              type="submit"
              :disabled="form.processing"
            >
              {{ form.processing 
                ? $page.props.language.common.saving 
                : $page.props.language.common.save }}
            </Button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Test Connection Modal -->
    <Modal :show="showTestModal" @close="closeTestModal">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ $page.props.language.admin.servers_test_results }}
        </h3>
        <div v-if="testing" class="flex justify-center">
          <Spinner />
        </div>
        <div v-else>
          <div :class="[
            'p-4 rounded-lg',
            testSuccess ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
          ]">
            {{ testMessage }}
          </div>
        </div>
        <div class="mt-6 flex justify-end">
          <Button @click="closeTestModal">
            {{ $page.props.language.common.close }}
          </Button>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { EllipsisVerticalIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import InputError from '@/Components/InputError.vue'
import Pagination from '@/Components/Pagination.vue'
import Spinner from '@/Components/Spinner.vue'
import { formatDate } from '@/utils/format'

const props = defineProps({
  servers: Object,
})

const showCreateModal = ref(false)
const showTestModal = ref(false)
const editingServer = ref(null)
const testing = ref(false)
const testSuccess = ref(false)
const testMessage = ref('')

const form = useForm({
  name: '',
  host: '',
  port: '',
  configuration: {},
})

const configurationText = ref('{}')

const updateConfiguration = () => {
  try {
    form.configuration = JSON.parse(configurationText.value)
  } catch (e) {
    form.errors.configuration = 'Invalid JSON format'
  }
}

const editServer = (server) => {
  editingServer.value = server
  form.name = server.name
  form.host = server.host
  form.port = server.port
  form.configuration = server.configuration
  configurationText.value = JSON.stringify(server.configuration, null, 2)
}

const closeModal = () => {
  showCreateModal.value = false
  editingServer.value = null
  form.reset()
  form.clearErrors()
  configurationText.value = '{}'
}

const submitForm = () => {
  if (editingServer.value) {
    form.put(route('admin.servers.update', editingServer.value.id), {
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('admin.servers.store'), {
      onSuccess: () => closeModal(),
    })
  }
}

const testConnection = async (server) => {
  showTestModal.value = true
  testing.value = true
  testSuccess.value = false
  testMessage.value = ''

  try {
    const response = await axios.post(route('admin.servers.test', server.id))
    testSuccess.value = true
    testMessage.value = response.data.message
  } catch (error) {
    testSuccess.value = false
    testMessage.value = error.response?.data?.message || 'Connection test failed'
  } finally {
    testing.value = false
  }
}

const closeTestModal = () => {
  showTestModal.value = false
  testing.value = false
  testMessage.value = ''
}

const deleteServer = (server) => {
  if (confirm($page.props.language.admin.servers_delete_confirm)) {
    form.delete(route('admin.servers.destroy', server.id))
  }
}
</script> 