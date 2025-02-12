<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-6">
                        {{ isset($api) ? 'Edit API' : 'Add New API' }}
                    </h2>

                    <form action="{{ isset($api) ? route('admin.apis.update', $api) : route('admin.apis.store') }}"
                          method="POST">
                        @csrf
                        @if(isset($api))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" 
                                       name="name" 
                                       value="{{ old('name', $api->name ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Endpoint -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Endpoint URL</label>
                                <input type="url" 
                                       name="endpoint" 
                                       value="{{ old('endpoint', $api->endpoint ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- API Key -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">API Key</label>
                                <input type="password" 
                                       name="api_key" 
                                       value="{{ old('api_key', $api->api_key ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Model -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Model</label>
                                <input type="text" 
                                       name="model" 
                                       value="{{ old('model', $api->model ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <!-- Max Tokens -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Max Tokens</label>
                                    <input type="number" 
                                           name="max_tokens" 
                                           value="{{ old('max_tokens', $api->max_tokens ?? 2048) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <!-- Temperature -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Temperature</label>
                                    <input type="number" 
                                           step="0.1" 
                                           name="temperature" 
                                           value="{{ old('temperature', $api->temperature ?? 0.7) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <!-- Priority -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Priority</label>
                                    <input type="number" 
                                           name="priority" 
                                           value="{{ old('priority', $api->priority ?? 0) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <!-- Rate Limit -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Rate Limit (per minute)</label>
                                    <input type="number" 
                                           name="rate_limit" 
                                           value="{{ old('rate_limit', $api->rate_limit ?? 60) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Timeout -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Timeout (seconds)</label>
                                <input type="number" 
                                       name="timeout" 
                                       value="{{ old('timeout', $api->timeout ?? 30) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="active" {{ (old('status', $api->status ?? '') === 'active') ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive" {{ (old('status', $api->status ?? '') === 'inactive') ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    {{ isset($api) ? 'Update API' : 'Add API' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 