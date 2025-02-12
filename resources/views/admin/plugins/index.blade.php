<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Plugin Management</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Plugin Statistics -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Plugin Overview</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded">
                            <div class="text-2xl font-bold">{{ $plugins->count() }}</div>
                            <div class="text-sm text-gray-600">Total Plugins</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded">
                            <div class="text-2xl font-bold">
                                {{ $plugins->where('status', 'active')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Active Plugins</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded">
                            <div class="text-2xl font-bold">
                                {{ $plugins->sum(function($plugin) { return $plugin->users->count(); }) }}
                            </div>
                            <div class="text-sm text-gray-600">Total Installations</div>
                        </div>
                    </div>
                </div>

                <!-- Plugin List -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left">Plugin</th>
                                <th class="px-6 py-3 bg-gray-50 text-left">Status</th>
                                <th class="px-6 py-3 bg-gray-50 text-left">Users</th>
                                <th class="px-6 py-3 bg-gray-50 text-left">Version</th>
                                <th class="px-6 py-3 bg-gray-50 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($plugins as $plugin)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i class="{{ $plugin->icon ?? 'fas fa-puzzle-piece' }} mr-3"></i>
                                        <div>
                                            <div class="font-medium">{{ $plugin->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $plugin->description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.plugins.status', $plugin) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" 
                                                onchange="this.form.submit()"
                                                class="rounded border-gray-300">
                                            @foreach(['active', 'inactive', 'pending'] as $status)
                                                <option value="{{ $status }}" 
                                                        {{ $plugin->status === $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $plugin->users->count() }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $plugin->version }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('admin.plugins.analytics', $plugin) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            Analytics
                                        </a>
                                        <button onclick="openSettingsModal('{{ $plugin->id }}')"
                                                class="text-green-600 hover:text-green-900">
                                            Settings
                                        </button>
                                        <button onclick="openPermissionsModal('{{ $plugin->id }}')"
                                                class="text-purple-600 hover:text-purple-900">
                                            Permissions
                                        </button>
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

    <!-- Settings Modal -->
    <div id="settingsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="modal-content">
            <!-- Settings form -->
        </div>
    </div>

    <!-- Permissions Modal -->
    <div id="permissionsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="modal-content">
            <!-- Permissions form -->
        </div>
    </div>

    <script>
        function openSettingsModal(pluginId) {
            // Implementation
        }

        function openPermissionsModal(pluginId) {
            // Implementation
        }
    </script>
</x-app-layout> 