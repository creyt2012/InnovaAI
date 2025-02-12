<template>
  <div class="relative">
    <div class="iframe-container h-[800px] border rounded-lg overflow-hidden">
      <iframe :src="pageUrl" ref="pageFrame" class="w-full h-full"></iframe>
    </div>
    <div class="heatmap-overlay absolute top-0 left-0 w-full h-full" ref="heatmapContainer"></div>
    
    <div class="mt-4 flex space-x-4">
      <Button 
        v-for="type in ['clicks', 'moves', 'scroll']" 
        :key="type"
        @click="switchHeatmapType(type)"
        :variant="currentType === type ? 'primary' : 'default'"
      >
        {{ type.charAt(0).toUpperCase() + type.slice(1) }}
      </Button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import h337 from 'heatmap.js'
import Button from '@/Components/UI/Button.vue'

const props = defineProps({
  pageUrl: {
    type: String,
    required: true
  },
  heatmapData: {
    type: Object,
    required: true
  }
})

const pageFrame = ref(null)
const heatmapContainer = ref(null)
const heatmapInstance = ref(null)
const currentType = ref('clicks')

const initHeatmap = () => {
  heatmapInstance.value = h337.create({
    container: heatmapContainer.value,
    radius: 25,
    maxOpacity: 0.6,
    minOpacity: 0.1
  })
}

const updateHeatmap = () => {
  if (!heatmapInstance.value) return

  const data = {
    clicks: props.heatmapData.clicks,
    moves: props.heatmapData.moves,
    scroll: props.heatmapData.scroll
  }[currentType.value]

  heatmapInstance.value.setData({
    max: Math.max(...data.map(point => point.value)),
    data: data.map(point => ({
      x: point.x,
      y: point.y,
      value: point.value
    }))
  })
}

const switchHeatmapType = (type) => {
  currentType.value = type
  updateHeatmap()
}

onMounted(() => {
  initHeatmap()
  updateHeatmap()
})

watch(() => props.heatmapData, updateHeatmap)
</script>

<style scoped>
.iframe-container {
  position: relative;
  z-index: 1;
}

.heatmap-overlay {
  pointer-events: none;
  z-index: 2;
}
</style> 