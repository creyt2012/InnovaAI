@extends('layouts.admin')

@section('title', 'Prompt Templates')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Prompt Templates</h2>
        <a href="{{ route('admin.prompts.create') }}" class="btn-primary">
            Create Template
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($templates as $template)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $template->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $template->category }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.prompts.edit', $template) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </a>
                                <form action="{{ route('admin.prompts.delete', $template) }}" method="POST" class="inline-block ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 