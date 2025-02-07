<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
          User Management
        </h2>
        <div class="flex items-center space-x-3">
          <Button @click="openImportModal">
            <ArrowUpTrayIcon class="h-5 w-5 mr-2" />
            Import
          </Button>
          <Button @click="exportUsers">
            <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
            Export
          </Button>
          <Button variant="primary" @click="openCreateModal">
            <PlusIcon class="h-5 w-5 mr-2" />
            Add User
          </Button>
        </div>
      </div>
    </template>

    <Card>
      <!-- Filters -->
      <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <Input
          v-model="filters.search"
          placeholder="Search users..."
          type="search"
          :leadingIcon="MagnifyingGlassIcon"
        />
        <Select
          v-model="filters.role"
          :options="roleOptions"
          placeholder="Filter by role"
        />
        <Select
          v-model="filters.status"
          :options="statusOptions"
          placeholder="Filter by status"
        />
        <DateRangePicker
          v-model="filters.dateRange"
          placeholder="Filter by date"
        />
      </div>

      <!-- Users Table -->
      <Table
        :columns="columns"
        :data="users"
        :loading="loading"
        :total="total"
        v-model:page="page"
        v-model:perPage="perPage"
        :sortable="true"
        @sort="handleSort"
      >
        <!-- Custom Column Slots -->
        <template #cell-avatar="{ row }">
          <img
            :src="row.avatar"
            :alt="row.name"
            class="h-8 w-8 rounded-full"
          />
        </template>

        <template #cell-status="{ row }">
          <Badge
            :variant="row.status === 'active' ? 'success' : 'error'"
            class="capitalize"
          >
            {{ row.status }}
          </Badge>
        </template>

        <template #cell-actions="{ row }">
          <div class="flex items-center space-x-2">
            <Button
              variant="outline"
              size="sm"
              @click="editUser(row)"
            >
              <PencilIcon class="h-4 w-4" />
            </Button>
            <Button
              variant="outline"
              size="sm"
              @click="impersonateUser(row)"
            >
              <UserIcon class="h-4 w-4" />
            </Button>
            <Button
              v-if="row.status === 'active'"
              variant="danger"
              size="sm"
              @click="banUser(row)"
            >
              <BanIcon class="h-4 w-4" />
            </Button>
            <Button
              v-else
              variant="success"
              size="sm"
              @click="unbanUser(row)"
            >
              <CheckIcon class="h-4 w-4" />
            </Button>
          </div>
        </template>
      </Table>
    </Card>

    <!-- Create/Edit Modal -->
    <Modal
      v-if="showUserModal"
      :show="true"
      @close="closeUserModal"
    >
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
          {{ editingUser ? 'Edit User' : 'Create User' }}
        </h3>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label>First Name</Label>
              <Input v-model="userForm.first_name" required />
            </div>
            <div>
              <Label>Last Name</Label>
              <Input v-model="userForm.last_name" required />
            </div>
          </div>

          <div>
            <Label>Email</Label>
            <Input
              v-model="userForm.email"
              type="email"
              required
              :disabled="!!editingUser"
            />
          </div>

          <div>
            <Label>Role</Label>
            <Select
              v-model="userForm.role"
              :options="roleOptions"
              required
            />
          </div>

          <div v-if="!editingUser">
            <Label>Password</Label>
            <Input
              v-model="userForm.password"
              type="password"
              required
            />
          </div>

          <div class="flex justify-end space-x-3">
            <Button
              variant="ghost"
              @click="closeUserModal"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :loading="saving"
            >
              {{ editingUser ? 'Save Changes' : 'Create User' }}
            </Button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Import Modal -->
    <Modal
      v-if="showImportModal"
      :show="true"
      @close="closeImportModal"
    >
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
          Import Users
        </h3>

        <div class="space-y-4">
          <div class="border-2 border-dashed rounded-xl p-6">
            <div class="text-center">
              <CloudArrowUpIcon class="mx-auto h-12 w-12 text-gray-400" />
              <div class="mt-4">
                <label class="cursor-pointer">
                  <span class="text-primary-600 hover:text-primary-500">
                    Upload a file
                  </span>
                  <input
                    type="file"
                    class="hidden"
                    accept=".csv,.xlsx"
                    @change="handleFileUpload"
                  />
                </label>
                <p class="text-sm text-gray-500">
                  or drag and drop
                </p>
              </div>
            </div>
          </div>

          <div class="flex justify-end">
            <Button
              :loading="importing"
              :disabled="!importFile"
              @click="importUsers"
            >
              Import Users
            </Button>
          </div>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useUserStore } from '@/Stores/userStore'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  PlusIcon, ArrowUpTrayIcon, ArrowDownTrayIcon,
  MagnifyingGlassIcon, PencilIcon, UserIcon,
  BanIcon, CheckIcon, CloudArrowUpIcon
} from '@heroicons/vue/24/outline'

const userStore = useUserStore()
const loading = ref(false)
const page = ref(1)
const perPage = ref(10)
const total = ref(0)
const filters = ref({
  search: '',
  role: null,
  status: null,
  dateRange: null
})

const columns = [
  { key: 'avatar', label: '', sortable: false },
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'role', label: 'Role', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'created_at', label: 'Created', sortable: true },
  { key: 'actions', label: 'Actions', sortable: false }
]

const roleOptions = [
  { value: 'admin', label: 'Admin' },
  { value: 'user', label: 'User' }
]

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'banned', label: 'Banned' }
]

// User Modal
const showUserModal = ref(false)
const editingUser = ref(null)
const userForm = ref({
  first_name: '',
  last_name: '',
  email: '',
  role: '',
  password: ''
})

// Import Modal
const showImportModal = ref(false)
const importFile = ref(null)
const importing = ref(false)

// Methods
const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await userStore.fetchUsers({
      page: page.value,
      perPage: perPage.value,
      ...filters.value
    })
    total.value = response.total
  } finally {
    loading.value = false
  }
}

const handleSort = (column) => {
  // Implement sorting logic
}

const openCreateModal = () => {
  editingUser.value = null
  userForm.value = {
    first_name: '',
    last_name: '',
    email: '',
    role: '',
    password: ''
  }
  showUserModal.value = true
}

const editUser = (user) => {
  editingUser.value = user
  userForm.value = {
    first_name: user.first_name,
    last_name: user.last_name,
    email: user.email,
    role: user.role
  }
  showUserModal.value = true
}

const saveUser = async () => {
  // Implement save logic
}

const banUser = async (user) => {
  // Implement ban logic
}

const unbanUser = async (user) => {
  // Implement unban logic
}

const impersonateUser = async (user) => {
  // Implement impersonate logic
}

const exportUsers = async () => {
  // Implement export logic
}

const handleFileUpload = (event) => {
  importFile.value = event.target.files[0]
}

const importUsers = async () => {
  // Implement import logic
}

// Watch for changes
watch([page, perPage, filters], fetchUsers, { deep: true })

// Initial fetch
onMounted(fetchUsers)
</script> 