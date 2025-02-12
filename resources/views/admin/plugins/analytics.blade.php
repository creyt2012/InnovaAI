<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Plugin Analytics - {{ $plugin->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Usage Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-blue-50 p-4 rounded">
                        <div class="text-2xl font-bold">{{ $usageStats['total_installations'] }}</div>
                        <div class="text-sm text-gray-600">Total Installations</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded">
                        <div class="text-2xl font-bold">{{ $usageStats['active_users'] }}</div>
                        <div class="text-sm text-gray-600">Active Users</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded">
                        <div class="text-2xl font-bold">{{ $usageStats['average_usage_time'] }}</div>
                        <div class="text-sm text-gray-600">Average Usage Time</div>
                    </div>
                </div>

                <!-- Usage Graph -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Usage Trends</h3>
                    <div class="h-64 bg-gray-50">
                        <!-- Implement chart using Chart.js or similar -->
                    </div>
                </div>

                <!-- Popular Features -->
                <div>
                    <h3 class="text-lg font-medium mb-4">Popular Features</h3>
                    <div class="space-y-4">
                        @foreach($usageStats['popular_features'] as $feature => $count)
                        <div class="flex justify-between items-center">
                            <span>{{ $feature }}</span>
                            <span class="font-medium">{{ $count }} uses</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 