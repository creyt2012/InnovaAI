<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
          {{ $t('admin.packages.title') }}
        </h2>
        <Button variant="primary" @click="openCreateModal">
          <PlusIcon class="h-5 w-5 mr-2" />
          {{ $t('admin.packages.create') }}
        </Button>
      </div>
    </template>

    <Card>
      <Table
        :columns="columns"
        :data="packages"
        :loading="loading"
        :total="total"
        v-model:page="page"
        v-model:perPage="perPage"
      >
        <template #cell-price="{ row }">
          {{ formatPrice(row.price) }}
        </template>

        <template #cell-is_active="{ row }">
          <Badge
            :variant="row.is_active ? 'success' : 'error'"
          >
            {{ row.is_active ? 'Active' : 'Inactive' }}
          </Badge>
        </template>

        <template #cell-actions="{ row }">
          <div class="flex items-center space-x-2">
            <Button
              variant="outline"
              size="sm"
              @click="editPackage(row)"
            >
              <PencilIcon class="h-4 w-4" />
            </Button>
            <Button
              variant="outline"
              size="sm"
              @click="togglePackageStatus(row)"
            >
              <component 
                :is="row.is_active ? BanIcon : CheckIcon"
                class="h-4 w-4"
              />
            </Button>
          </div>
        </template>
      </Table>
    </Card>

    <!-- Create/Edit Modal -->
    <Modal
      v-if="showPackageModal"
      :show="true"
      @close="closePackageModal"
    >
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
          {{ editingPackage ? 'Edit Package' : 'Create Package' }}
        </h3>

        <form @submit.prevent="savePackage" class="space-y-4">
          <div>
            <Label>Name</Label>
            <Input v-model="packageForm.name" required />
          </div>

          <div>
            <Label>Description</Label>
            <Textarea v-model="packageForm.description" rows="3" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label>Price</Label>
              <Input v-model="packageForm.price" type="number" step="0.01" required />
            </div>
            <div>
              <Label>Credits</Label>
              <Input v-model="packageForm.credits" type="number" required />
            </div>
          </div>

          <div>
            <Label>Duration (Days)</Label>
            <Input v-model="packageForm.duration_days" type="number" required />
          </div>

          <div>
            <Label>Features</Label>
            <div class="space-y-2">
              <div v-for="(feature, index) in packageForm.features" :key="index" class="flex items-center space-x-2">
                <Input v-model="packageForm.features[index]" />
                <Button type="button" variant="ghost" @click="removeFeature(index)">
                  <XMarkIcon class="h-5 w-5" />
                </Button>
              </div>
              <Button type="button" variant="outline" @click="addFeature">
                Add Feature
              </Button>
            </div>
          </div>

          <div class="flex justify-end space-x-3">
            <Button variant="ghost" @click="closePackageModal">
              Cancel
            </Button>
            <Button type="submit" :loading="saving">
              {{ editingPackage ? 'Save Changes' : 'Create Package' }}
            </Button>
          </div>
        </form>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePackageStore } from '@/Stores/packageStore'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  PlusIcon, PencilIcon, BanIcon, CheckIcon, XMarkIcon
} from '@heroicons/vue/24/outline'

const packageStore = usePackageStore()
const loading = ref(false)
const saving = ref(false)
const page = ref(1)
const perPage = ref(10)
const total = ref(0)

const columns = [
  { key: 'name', label: 'Name' },
  { key: 'price', label: 'Price' },
  { key: 'credits', label: 'Credits' },
  { key: 'duration_days', label: 'Duration' },
  { key: 'is_active', label: 'Status' },
  { key: 'actions', label: 'Actions' }
]

const showPackageModal = ref(false)
const editingPackage = ref(null)
const packageForm = ref({
  name: '',
  description: '',
  price: 0,
  credits: 0,
  duration_days: 30,
  features: [],
  is_active: true
})

// Methods
const fetchPackages = async () => {
  loading.value = true
  try {
    const response = await packageStore.fetchPackages({
      page: page.value,
      perPage: perPage.value
    })
    total.value = response.total
  } finally {
    loading.value = false
  }
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

const openCreateModal = () => {
  editingPackage.value = null
  packageForm.value = {
    name: '',
    description: '',
    price: 0,
    credits: 0,
    duration_days: 30,
    features: [],
    is_active: true
  }
  showPackageModal.value = true
}

const editPackage = (pkg) => {
  editingPackage.value = pkg
  packageForm.value = { ...pkg }
  showPackageModal.value = true
}

const closePackageModal = () => {
  showPackageModal.value = false
  editingPackage.value = null
}

const addFeature = () => {
  packageForm.value.features.push('')
}

const removeFeature = (index) => {
  packageForm.value.features.splice(index, 1)
}

const savePackage = async () => {
  saving.value = true
  try {
    if (editingPackage.value) {
      await packageStore.updatePackage(editingPackage.value.id, packageForm.value)
    } else {
      await packageStore.createPackage(packageForm.value)
    }
    closePackageModal()
    fetchPackages()
  } finally {
    saving.value = false
  }
}

const togglePackageStatus = async (pkg) => {
  await packageStore.updatePackage(pkg.id, {
    ...pkg,
    is_active: !pkg.is_active
  })
  fetchPackages()
}

// Initial fetch
onMounted(fetchPackages)
</script> 