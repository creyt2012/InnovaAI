@extends('layouts.admin')

@section('title', 'Create Knowledge Base')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Add Knowledge Base</h2>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.knowledge.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <x-input-label for="title" value="Title" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="source_type" value="Source Type" />
                    <x-select id="source_type" name="source_type" class="mt-1 block w-full">
                        <option value="manual">Manual Entry</option>
                        <option value="file">File Upload</option>
                        <option value="url">URL</option>
                    </x-select>
                    <x-input-error :messages="$errors->get('source_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="content" value="Content" />
                    <x-textarea id="content" name="content" rows="10" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>

                <div x-show="sourceType === 'url'">
                    <x-input-label for="source_url" value="Source URL" />
                    <x-text-input id="source_url" name="source_url" type="url" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('source_url')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button :href="route('admin.knowledge.index')">
                    Cancel
                </x-secondary-button>
                <x-primary-button>
                    Create Knowledge Base
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection 