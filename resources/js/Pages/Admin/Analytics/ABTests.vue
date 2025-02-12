<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 px-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">A/B Tests</h1>
        <Button @click="showCreateModal = true">Create Test</Button>
      </div>

      <!-- Tests List -->
      <div class="space-y-6">
        <!-- Test cards with results -->
      </div>

      <!-- Create Modal -->
      <Modal v-if="showCreateModal" @close="showCreateModal = false">
        <!-- Form content -->
      </Modal>
    </div>
  </AdminLayout>
</template> 