<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Developer Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-2xl font-bold">{{ $stats['total_plugins'] }}</div>
                    <div class="text-gray-600">Total Plugins</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-2xl font-bold">{{ $stats['total_users'] }}</div>
                    <div class="text-gray-600">Total Users</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-2xl font-bold">{{ $stats['total_downloads'] }}</div>
                    <div class="text-gray-600">Total Downloads</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-2xl font-bold">${{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="text-gray-600">Total Revenue</div>
                </div>
            </div>

            <!-- Plugins List -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Your Plugins</h3>
                        <a href="{{ route('developer.plugins.create') }}"
                           class="bg-blue-500 text-white px-4 py-2 rounded">
                            Upload New Plugin
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Plugin</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Users</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Downloads</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Revenue</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($plugins as $plugin)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="font-medium">{{ $plugin->name }}</div>
                                                <div class="text-sm text-gray-500">v{{ $plugin->version }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $plugin->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($plugin->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $plugin->users_count }}</td>
                                    <td class="px-6 py-4">{{ $plugin->downloads_count }}</td>
                                    <td class="px-6 py-4">${{ number_format($plugin->revenue, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('developer.plugins.edit', $plugin) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                Edit
                                            </a>
                                            <a href="{{ route('developer.plugins.analytics', $plugin) }}"
                                               class="text-green-600 hover:text-green-900">
                                                Analytics
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 