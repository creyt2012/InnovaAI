@extends('layouts.admin')

@section('title', 'Create Prompt Template')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Create New Template</h2>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.prompts.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="Template Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="category" value="Category" />
                    <x-select id="category" name="category" class="mt-1 block w-full">
                        <option value="general">General</option>
                        <option value="support">Support</option>
                        <option value="marketing">Marketing</option>
                    </x-select>
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="content" value="Template Content" />
                    <x-textarea id="content" name="content" rows="6" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    <p class="mt-2 text-sm text-gray-500">
                        Use {variable} syntax for parameters. Example: Hello {name}!
                    </p>
                </div>

                <div x-data="{ parameters: [] }" class="space-y-4">
                    <x-input-label value="Parameters" />
                    
                    <template x-for="(param, index) in parameters" :key="index">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <x-text-input 
                                    x-model="param.name"
                                    :name="'parameters['+index+'][name]'"
                                    placeholder="Parameter name"
                                    class="w-full"
                                />
                            </div>
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    x-model="param.required"
                                    :name="'parameters['+index+'][required]'"
                                    class="rounded border-gray-300"
                                >
                                <span class="ml-2 text-sm text-gray-600">Required</span>
                            </div>
                            <button type="button" @click="parameters.splice(index, 1)" class="text-red-600">
                                Remove
                            </button>
                        </div>
                    </template>

                    <button 
                        type="button"
                        @click="parameters.push({name: '', required: false})"
                        class="text-indigo-600 hover:text-indigo-900"
                    >
                        + Add Parameter
                    </button>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button :href="route('admin.prompts.index')">
                    Cancel
                </x-secondary-button>
                <x-primary-button>
                    Create Template
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection 