@extends('layouts.admin')

@section('title', 'Bảo mật')

@section('content')
<div class="space-y-6">
    <!-- Security Overview -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- Failed Login Attempts -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="shrink-0 bg-red-100 rounded-md p-3">
                        <x-heroicon-o-shield-exclamation class="h-6 w-6 text-red-600"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Đăng nhập thất bại
                            </dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">
                                    {{ $failedLogins }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.security.audit') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>

        <!-- Blocked IPs -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="shrink-0 bg-yellow-100 rounded-md p-3">
                        <x-heroicon-o-ban class="h-6 w-6 text-yellow-600"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                IP bị chặn
                            </dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">
                                    {{ $blockedIps }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Quản lý IP
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="shrink-0 bg-green-100 rounded-md p-3">
                        <x-heroicon-o-user-group class="h-6 w-6 text-green-600"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Phiên đang hoạt động
                            </dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">
                                    {{ $activeSessions }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Hoạt động gần đây
            </h3>
            
            <div class="mt-4">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Thời gian
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Người dùng
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Hành động
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                IP
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentActivity as $activity)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <img class="h-10 w-10 rounded-full" src="{{ $activity->user->profile_photo_url }}" alt="">
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $activity->user->name }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                {{ $activity->user->email }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $activity->is_suspicious ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ $activity->action }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $activity->ip_address }}
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
        </div>
    </div>
</div>
@endsection 