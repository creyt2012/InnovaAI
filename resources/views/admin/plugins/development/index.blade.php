<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Plugin Development</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Create New Plugin -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Create New Plugin</h3>
                    <form action="{{ route('admin.plugins.development.create') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Plugin Name</label>
                                <input type="text" 
                                       name="name" 
                                       class="mt-1 block w-full rounded-md border-gray-300"
                                       placeholder="my-awesome-plugin">
                                <p class="mt-1 text-sm text-gray-500">
                                    Only lowercase letters, numbers, and hyphens
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" 
                                          rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Author</label>
                                <input type="text" 
                                       name="author" 
                                       class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <button type="submit" 
                                        class="bg-blue-500 text-white px-4 py-2 rounded">
                                    Create Plugin Structure
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Upload Plugin -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Upload Plugin</h3>
                    <form action="{{ route('admin.plugins.development.upload') }}" 
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Plugin ZIP File</label>
                                <input type="file" 
                                       name="plugin" 
                                       accept=".zip"
                                       class="mt-1 block w-full">
                            </div>
                            <div>
                                <button type="submit" 
                                        class="bg-green-500 text-white px-4 py-2 rounded">
                                    Upload Plugin
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Documentation Link -->
                <div>
                    <h3 class="text-lg font-medium mb-4">Documentation</h3>
                    <p class="text-gray-600 mb-4">
                        Learn how to develop plugins for LM Studio by reading our comprehensive documentation.
                    </p>
                    <a href="{{ route('admin.plugins.development.documentation') }}"
                       class="text-blue-600 hover:text-blue-900">
                        Read Documentation →
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 