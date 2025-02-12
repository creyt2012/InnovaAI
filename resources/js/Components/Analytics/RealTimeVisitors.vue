<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-medium">Real-time Visitors</h3>
      <span class="text-sm text-gray-500">Updated live</span>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
      <div>
        <div class="text-2xl font-semibold">{{ activeVisitors }}</div>
        <div class="text-sm text-gray-500">Active now</div>
      </div>
      <div>
        <div class="text-2xl font-semibold">{{ pageViews }}/min</div>
        <div class="text-sm text-gray-500">Page views</div>
      </div>
      <div>
        <div class="text-2xl font-semibold">{{ avgDuration }}</div>
        <div class="text-sm text-gray-500">Avg. duration</div>
      </div>
    </div>

    <LiveMap :visitors="visitors" />
    <ActivityFeed :activities="activities" />
  </div>
</template> 