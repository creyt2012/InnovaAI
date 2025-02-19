@extends('layouts.admin')

@section('title', 'Knowledge Base Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Knowledge Base</h2>
        <div class="flex space-x-4">
            <button 
                @click="$dispatch('open-modal', 'import-kb')"
                class="btn-secondary"
            >
                Import
            </button>
            <a href="{{ route('admin.knowledge.create') }}" class="btn-primary">
                Create New
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white shadow rounded-lg p-4">
        <div class="max-w-xl">
            <div class="relative">
                <input 
                    type="search"
                    class="form-input pl-10 w-full"
                    placeholder="Search knowledge base..."
                    wire:model.debounce.300ms="search"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-search class="h-5 w-5 text-gray-400"/>
                </div>
            </div>
        </div>
    </div>

    <!-- Knowledge Base List -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Title
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Source
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Trained
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($knowledgeBases as $kb)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $kb->title }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $kb->source_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $kb->last_trained_at?->diffForHumans() ?? 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button 
                                    @click="trainKnowledgeBase({{ $kb->id }})"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Train
                                </button>
                                <a 
                                    href="{{ route('admin.knowledge.edit', $kb) }}" 
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Edit
                                </a>
                                <button
                                    @click="deleteKnowledgeBase({{ $kb->id }})"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $knowledgeBases->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<x-modal name="import-kb">
    <form 
        action="{{ route('admin.knowledge.import') }}"
        method="POST"
        enctype="multipart/form-data"
        class="p-6"
    >
        @csrf
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            Import Knowledge Base
        </h2>

        <div class="mb-4">
            <x-input-label for="file" value="File" />
            <x-file-input
                id="file"
                name="file"
                accept=".pdf,.doc,.docx,.txt"
                class="mt-1 block w-full"
                required
            />
            <p class="mt-1 text-sm text-gray-500">
                Supported formats: PDF, DOC, DOCX, TXT (max 10MB)
            </p>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <x-secondary-button @click="$dispatch('close')">
                Cancel
            </x-secondary-button>
            <x-primary-button>
                Import
            </x-primary-button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script>
function trainKnowledgeBase(id) {
    if (!confirm('Are you sure you want to train this knowledge base?')) {
        return;
    }

    axios.post(`/admin/knowledge/${id}/train`)
        .then(response => {
            Toast.success('Training job queued successfully');
        })
        .catch(error => {
            Toast.error('Failed to queue training job');
        });
}

function deleteKnowledgeBase(id) {
    if (!confirm('Are you sure you want to delete this knowledge base?')) {
        return;
    }

    axios.delete(`/admin/knowledge/${id}`)
        .then(response => {
            window.location.reload();
        })
        .catch(error => {
            Toast.error('Failed to delete knowledge base');
        });
}
</script>
@endpush
@endsection 