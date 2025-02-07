<template>
  <UserLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">
              Two Factor Authentication
            </h2>

            <div class="mt-6" v-if="!enabled">
              <div v-if="qrCodeUrl" class="mt-4">
                <p class="text-sm text-gray-600 mb-4">
                  Scan QR code below with your authenticator app:
                </p>
                <img :src="qrCodeUrl" alt="QR Code" class="mb-4">
                <p class="text-sm text-gray-600 mb-4">
                  Or enter this code manually: {{ secret }}
                </p>
              </div>

              <form @submit.prevent="enable">
                <div>
                  <Label for="code">Verification Code</Label>
                  <Input
                    id="code"
                    type="text"
                    v-model="form.code"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="form.errors.code" class="mt-2" />
                </div>

                <div class="mt-4">
                  <Button :disabled="form.processing">
                    Enable 2FA
                  </Button>
                </div>
              </form>
            </div>

            <div v-else class="mt-6">
              <p class="text-sm text-gray-600">
                2FA is currently enabled.
              </p>

              <div v-if="recoveryCodes.length" class="mt-4">
                <h3 class="text-lg font-medium text-gray-900">
                  Recovery Codes
                </h3>
                <div class="grid grid-cols-2 gap-4 mt-4">
                  <div
                    v-for="code in recoveryCodes"
                    :key="code"
                    class="text-sm font-mono bg-gray-100 p-2 rounded"
                  >
                    {{ code }}
                  </div>
                </div>
              </div>

              <form @submit.prevent="disable" class="mt-6">
                <div>
                  <Label for="disable-code">Verification Code</Label>
                  <Input
                    id="disable-code"
                    type="text"
                    v-model="disableForm.code"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="disableForm.errors.code" class="mt-2" />
                </div>

                <div class="mt-4">
                  <Button variant="danger" :disabled="disableForm.processing">
                    Disable 2FA
                  </Button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  enabled: Boolean,
  qrCodeUrl: String,
  secret: String,
  recoveryCodes: Array,
})

const form = useForm({
  code: '',
})

const disableForm = useForm({
  code: '',
})

const enable = () => {
  form.post(route('two-factor.enable'), {
    preserveScroll: true,
  })
}

const disable = () => {
  disableForm.delete(route('two-factor.disable'), {
    preserveScroll: true,
  })
}
</script> 