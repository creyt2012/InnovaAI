<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 px-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Conversion Goals</h1>
        <Button @click="showCreateModal = true">Add Goal</Button>
      </div>

      <!-- Goals List -->
      <div class="bg-white shadow overflow-hidden rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <!-- Table header and content -->
        </table>
      </div>

      <!-- Create Modal -->
      <Modal v-if="showCreateModal" @close="showCreateModal = false">
        <!-- Form content -->
      </Modal>
    </div>
  </AdminLayout>
</template> 