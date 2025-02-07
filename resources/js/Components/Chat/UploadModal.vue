<template>
  <Modal :show="true" @close="$emit('close')">
    <div class="p-6">
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          {{ $t('upload.title') }}
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          {{ $t('upload.description') }}
        </p>
      </div>

      <!-- Upload Zone -->
      <div
        class="relative"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
      >
        <!-- Upload Area -->
        <div
          :class="[
            'border-2 border-dashed rounded-2xl p-8',
            'transition-all duration-200',
            isDragging ? 
              'border-primary-500 bg-primary-50 dark:bg-primary-900/10' : 
              'border-gray-300 dark:border-gray-700'
          ]"
        >
          <div class="text-center">
            <CloudArrowUpIcon 
              class="mx-auto h-12 w-12 text-gray-400"
              :class="{ 'animate-bounce': isDragging }"
            />
            
            <div class="mt-4 flex flex-col items-center text-sm">
              <label
                class="relative cursor-pointer rounded-md font-medium text-primary-600 
                       hover:text-primary-500 focus-within:outline-none"
              >
                <span>{{ $t('upload.select_files') }}</span>
                <input
                  ref="fileInput"
                  type="file"
                  class="sr-only"
                  multiple
                  @change="handleFileSelect"
                />
              </label>
              <p class="pl-1 text-gray-500 dark:text-gray-400">
                {{ $t('upload.or_drag_drop') }}
              </p>
            </div>
            
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              {{ $t('upload.file_types') }}
            </p>
          </div>
        </div>

        <!-- Selected Files -->
        <div v-if="selectedFiles.length" class="mt-4 space-y-2">
          <div
            v-for="(file, index) in selectedFiles"
            :key="index"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 
                   rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="flex items-center space-x-3">
              <DocumentIcon class="h-5 w-5 text-gray-400" />
              <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                  {{ file.name }}
                </p>
                <p class="text-xs text-gray-500">
                  {{ formatFileSize(file.size) }}
                </p>
              </div>
            </div>
            
            <button
              class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full
                     text-gray-400 hover:text-gray-500 dark:hover:text-gray-300
                     transition-colors duration-200"
              @click="removeFile(index)"
            >
              <XMarkIcon class="h-5 w-5" />
            </button>
          </div>
        </div>

        <!-- Upload Progress -->
        <div v-if="isUploading" class="mt-4">
          <div class="flex items-center justify-between mb-1">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
              {{ $t('upload.uploading') }}
            </span>
            <span class="text-sm text-gray-500">
              {{ Math.round(uploadProgress) }}%
            </span>
          </div>
          <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div
              class="bg-primary-600 h-2 rounded-full transition-all duration-200"
              :style="{ width: `${uploadProgress}%` }"
            />
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="mt-6 flex justify-end space-x-3">
        <Button
          variant="secondary"
          @click="$emit('close')"
        >
          {{ $t('common.cancel') }}
        </Button>
        <Button
          :disabled="!selectedFiles.length || isUploading"
          :loading="isUploading"
          @click="uploadFiles"
        >
          {{ $t('upload.upload') }}
        </Button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref } from 'vue'
import { CloudArrowUpIcon, DocumentIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import Modal from '@/Components/UI/Modal.vue'
import Button from '@/Components/UI/Button.vue'

const emit = defineEmits(['close', 'upload'])

const isDragging = ref(false)
const selectedFiles = ref([])
const isUploading = ref(false)
const uploadProgress = ref(0)
const fileInput = ref(null)

const handleDrop = (e) => {
  isDragging.value = false
  const files = Array.from(e.dataTransfer.files)
  addFiles(files)
}

const handleFileSelect = (e) => {
  const files = Array.from(e.target.files)
  addFiles(files)
  fileInput.value.value = '' // Reset input
}

const addFiles = (files) => {
  // Filter and validate files here
  selectedFiles.value.push(...files)
}

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const uploadFiles = async () => {
  isUploading.value = true
  uploadProgress.value = 0

  try {
    const formData = new FormData()
    selectedFiles.value.forEach(file => {
      formData.append('files[]', file)
    })

    // Simulate upload progress
    const interval = setInterval(() => {
      if (uploadProgress.value < 90) {
        uploadProgress.value += 10
      }
    }, 500)

    // Replace with your actual upload API call
    // const response = await axios.post('/api/upload', formData, {
    //   onUploadProgress: (e) => {
    //     uploadProgress.value = Math.round((e.loaded * 100) / e.total)
    //   }
    // })

    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 2000))
    uploadProgress.value = 100
    clearInterval(interval)

    emit('upload', selectedFiles.value)
    emit('close')
  } catch (error) {
    console.error('Upload failed:', error)
  } finally {
    isUploading.value = false
  }
}
</script> 