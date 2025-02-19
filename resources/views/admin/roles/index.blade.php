@extends('layouts.admin')

@section('title', 'Role Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Roles</h2>
        <a href="{{ route('admin.roles.create') }}" class="btn-primary">
            Create Role
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
                            Users
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            System Role
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($roles as $role)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $role->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $role->description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $role->users_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($role->is_system)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        System
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </a>
                                @unless($role->is_system)
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline-block ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 