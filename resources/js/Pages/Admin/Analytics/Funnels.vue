<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 px-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Conversion Funnels</h1>
        <Button @click="showCreateModal = true">Create Funnel</Button>
      </div>

      <!-- Funnels List -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Funnel cards -->
      </div>

      <!-- Create Modal -->
      <Modal v-if="showCreateModal" @close="showCreateModal = false">
        <!-- Form content -->
      </Modal>
    </div>
  </AdminLayout>
</template> 