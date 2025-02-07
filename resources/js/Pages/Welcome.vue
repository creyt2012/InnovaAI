<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-900 to-indigo-900">
    <!-- Header/Navigation -->
    <nav class="relative px-6 py-6 flex justify-between items-center">
      <a href="/" class="flex items-center space-x-2">
        <img :src="settings.logo" alt="InnovaAI Logo" class="h-10 w-auto" />
        <span class="text-2xl font-bold text-white">{{ settings.app_name }}</span>
      </a>
      
      <div class="flex items-center space-x-6">
        <Link
          v-if="$page.props.auth.user"
          :href="route('dashboard')"
          class="text-white hover:text-indigo-200"
        >
          Dashboard
        </Link>
        <template v-else>
          <Link
            :href="route('login')"
            class="text-white hover:text-indigo-200"
          >
            Sign in
          </Link>
          <Link
            :href="route('register')"
            class="px-6 py-3 text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl"
          >
            Get Started
          </Link>
        </template>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-6 py-24">
      <div class="text-center">
        <h1 class="text-4xl sm:text-6xl font-bold text-white mb-8">
          {{ settings.hero_title || 'Next Generation AI Chat Platform' }}
        </h1>
        <p class="text-xl text-gray-300 mb-12 max-w-3xl mx-auto">
          {{ settings.hero_description || 'Experience the power of advanced AI models with our intuitive chat interface. Perfect for developers, researchers, and AI enthusiasts.' }}
        </p>
        <div class="flex justify-center space-x-4">
          <Link
            :href="route('register')"
            class="px-8 py-4 text-lg font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl"
          >
            Start for Free
          </Link>
          <a
            href="#features"
            class="px-8 py-4 text-lg font-medium text-white border border-white/20 hover:bg-white/10 rounded-xl"
          >
            Learn More
          </a>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-gray-900/50">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
          <div v-for="feature in settings.features" :key="feature.title" 
               class="p-6 rounded-2xl bg-gray-800/50 border border-gray-700">
            <component :is="feature.icon" class="w-12 h-12 text-indigo-400 mb-4" />
            <h3 class="text-xl font-semibold text-white mb-3">{{ feature.title }}</h3>
            <p class="text-gray-400">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useSettingsStore } from '@/Stores/settingsStore'

const settingsStore = useSettingsStore()
const settings = computed(() => settingsStore.settings)
</script> 