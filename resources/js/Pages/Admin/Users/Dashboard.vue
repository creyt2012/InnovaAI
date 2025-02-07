<template>
  <AdminLayout>
    <!-- User Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <DashboardCard title="Total Users">
        <template #icon>
          <UsersIcon class="w-6 h-6 text-blue-500" />
        </template>
        <span class="text-2xl font-bold">{{ stats.total_users }}</span>
      </DashboardCard>

      <DashboardCard title="Active Today">
        <template #icon>
          <UserCircleIcon class="w-6 h-6 text-green-500" />
        </template>
        <span class="text-2xl font-bold">{{ stats.active_today }}</span>
      </DashboardCard>

      <DashboardCard title="New This Week">
        <template #icon>
          <UserPlusIcon class="w-6 h-6 text-purple-500" />
        </template>
        <span class="text-2xl font-bold">{{ stats.new_this_week }}</span>
      </DashboardCard>
    </div>

    <!-- User Management Tools -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Bulk Actions</h3>
          <div class="space-x-2">
            <Button @click="showImportModal = true">
              Import Users
            </Button>
            <Button @click="exportUsers">
              Export Users
            </Button>
          </div>
        </div>

        <div class="space-y-4">
          <div class="flex items-center space-x-2">
            <Checkbox v-model="selectedUsers" />
            <span>Select All Users</span>
          </div>

          <div class="flex space-x-2">
            <Button
              variant="danger"
              :disabled="!selectedUsers.length"
              @click="confirmBulkDelete"
            >
              Delete Selected
            </Button>
            <Button
              :disabled="!selectedUsers.length"
              @click="showBulkEditModal = true"
            >
              Edit Selected
            </Button>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">User Roles</h3>
        <PieChart :data="roleDistribution" />
      </div>
    </div>

    <!-- Advanced Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <h3 class="text-lg font-semibold mb-4">Advanced Filters</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">
            Registration Date
          </label>
          <DateRangePicker v-model="filters.dateRange" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">
            Activity Status
          </label>
          <Select
            v-model="filters.status"
            :options="[
              { value: 'all', label: 'All Users' },
              { value: 'active', label: 'Active Users' },
              { value: 'inactive', label: 'Inactive Users' }
            ]"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">
            Role
          </label>
          <Select
            v-model="filters.role"
            :options="roles"
          />
        </div>
      </div>
    </div>

    <!-- User Table -->
    <div class="bg-white rounded-lg shadow">
      <DataTable
        :data="users"
        :columns="columns"
        :sortable="true"
        :searchable="true"
        :paginate="true"
      >
        <template #actions="{ row }">
          <div class="flex space-x-2">
            <Button
              size="sm"
              @click="editUser(row)"
            >
              Edit
            </Button>
            <Button
              size="sm"
              variant="danger"
              @click="confirmDelete(row)"
            >
              Delete
            </Button>
            <Button
              size="sm"
              @click="impersonateUser(row)"
            >
              Impersonate
            </Button>
          </div>
        </template>
      </DataTable>
    </div>

    <!-- Modals -->
    <Modal v-model="showImportModal">
      <template #title>Import Users</template>
      <template #content>
        <FileUpload
          accept=".csv,.xlsx"
          @change="handleImport"
        />
      </template>
    </Modal>

    <Modal v-model="showBulkEditModal">
      <template #title>Edit Selected Users</template>
      <template #content>
        <form @submit.prevent="saveBulkEdit">
          <!-- Bulk edit form fields -->
        </form>
      </template>
    </Modal>
  </AdminLayout>
</template>

<script setup>
// ... Component logic
</script> 