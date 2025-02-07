<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
          {{ $t('admin.bank_accounts.title') }}
        </h2>
        <Button variant="primary" @click="openCreateModal">
          <PlusIcon class="h-5 w-5 mr-2" />
          {{ $t('admin.bank_accounts.create') }}
        </Button>
      </div>
    </template>

    <!-- Rest of the template... -->
  </AdminLayout>
</template>

<script setup>
// Implementation...
</script> 