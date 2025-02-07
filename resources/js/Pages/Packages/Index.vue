<template>
  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ $t('packages.title') }}
          </h2>
          <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">
            {{ $t('packages.subtitle') }}
          </p>
        </div>

        <div class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="pkg in packages"
            :key="pkg.id"
            class="relative rounded-2xl border border-gray-200 dark:border-gray-700 p-8 shadow-sm flex flex-col"
            :class="{
              'border-primary-500 dark:border-primary-400 shadow-primary-100 dark:shadow-primary-900/30': pkg.popular
            }"
          >
            <!-- Popular Badge -->
            <span
              v-if="pkg.popular"
              class="absolute top-0 -translate-y-1/2 bg-primary-500 text-white px-4 py-1 text-sm font-semibold rounded-full"
            >
              {{ $t('packages.popular_badge') }}
            </span>

            <!-- Package Header -->
            <div class="mb-6">
              <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ pkg.name }}
              </h3>
              <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ pkg.description }}
              </p>
            </div>

            <!-- Price -->
            <div class="mb-6">
              <p class="text-4xl font-bold text-gray-900 dark:text-white">
                {{ formatPrice(pkg.price) }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('packages.duration_days', { duration: pkg.duration_days }) }}
              </p>
            </div>

            <!-- Features -->
            <ul class="mb-6 space-y-4 flex-1">
              <li
                v-for="feature in pkg.features"
                :key="feature"
                class="flex items-start"
              >
                <CheckCircleIcon class="h-6 w-6 text-primary-500 flex-shrink-0" />
                <span class="ml-3 text-gray-600 dark:text-gray-400">
                  {{ feature }}
                </span>
              </li>
            </ul>

            <!-- Action Button -->
            <Button
              variant="primary"
              class="w-full"
              :class="{ 'bg-primary-600': pkg.popular }"
              @click="selectPackage(pkg)"
            >
              {{ $t('packages.get_started') }}
            </Button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <Modal
      v-if="showPaymentModal"
      :show="true"
      @close="closePaymentModal"
    >
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
          {{ $t('payment.title') }}
        </h3>

        <div class="space-y-4">
          <!-- Package Summary -->
          <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">{{ $t('payment.package_name') }}:</span>
              <span class="font-medium">{{ selectedPackage?.name }}</span>
            </div>
            <div class="flex justify-between mt-2">
              <span class="text-gray-600 dark:text-gray-400">{{ $t('payment.amount') }}:</span>
              <span class="font-medium">{{ formatPrice(selectedPackage?.price) }}</span>
            </div>
          </div>

          <!-- Bank Transfer Information -->
          <div v-for="bank in bankAccounts" :key="bank.id" class="border dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <img :src="bank.logo" :alt="bank.name" class="h-8 w-8">
                <div>
                  <p class="font-medium">{{ bank.name }}</p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ bank.account_number }}
                  </p>
                </div>
              </div>
              <Button
                variant="outline"
                size="sm"
                @click="copyAccountNumber(bank.account_number)"
              >
                {{ $t('payment.copy_success') }}
              </Button>
            </div>
          </div>

          <div class="text-sm text-gray-600 dark:text-gray-400">
            <p>{{ $t('payment.transfer_note') }}</p>
            <code class="block mt-2 p-2 bg-gray-100 dark:bg-gray-800 rounded font-mono">
              {{ paymentCode }}
            </code>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <Button @click="closePaymentModal">
            {{ $t('payment.close') }}
          </Button>
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePackageStore } from '@/Stores/packageStore'
import AppLayout from '@/Layouts/AppLayout.vue'
import { CheckCircleIcon } from '@heroicons/vue/24/solid'

const packageStore = usePackageStore()
const packages = ref([])
const loading = ref(false)
const showPaymentModal = ref(false)
const selectedPackage = ref(null)
const paymentCode = ref('')

const bankAccounts = ref([
  {
    id: 1,
    name: 'VietcomBank',
    account_number: '1234567890',
    logo: '/images/banks/vcb.png'
  },
  // Add more banks...
])

// Methods
const fetchPackages = async () => {
  loading.value = true
  try {
    packages.value = await packageStore.fetchActivePackages()
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

const selectPackage = (pkg) => {
  selectedPackage.value = pkg
  paymentCode.value = `PKG${pkg.id}_${Date.now()}`
  showPaymentModal.value = true
}

const closePaymentModal = () => {
  showPaymentModal.value = false
  selectedPackage.value = null
}

const copyAccountNumber = (accountNumber) => {
  navigator.clipboard.writeText(accountNumber)
  // Show success toast
}

// Initial fetch
onMounted(fetchPackages)
</script> 