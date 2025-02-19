@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="shrink-0 bg-blue-100 rounded-md p-3">
                        <x-heroicon-o-users class="h-6 w-6 text-blue-600"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Tổng người dùng
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $totalUsers }}
                                </div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    <x-heroicon-s-arrow-up class="self-center flex-shrink-0 h-5 w-5"/>
                                    <span class="sr-only">Tăng</span>
                                    {{ $userGrowth }}%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Chats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="shrink-0 bg-indigo-100 rounded-md p-3">
                        <x-heroicon-o-chat-alt-2 class="h-6 w-6 text-indigo-600"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Tổng cuộc hội thoại
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $totalChats }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="shrink-0 bg-green-100 rounded-md p-3">
                        <x-heroicon-o-server class="h-6 w-6 text-green-600"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Tình trạng hệ thống
                            </dt>
                            <dd>
                                <div class="text-lg font-medium text-green-600">
                                    Hoạt động tốt
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Usage -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="shrink-0 bg-purple-100 rounded-md p-3">
                        <x-heroicon-o-chart-bar class="h-6 w-6 text-purple-600"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Lượt gọi API
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ number_format($apiCalls) }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- User Growth Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Tăng trưởng người dùng
                </h3>
                <div class="mt-4" style="height: 300px;">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chat Activity Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Hoạt động chat
                </h3>
                <div class="mt-4" style="height: 300px;">
                    <canvas id="chatActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    Hoạt động gần đây
                </h3>
                <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Xem tất cả
                </a>
            </div>

            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($recentActivities as $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full {{ $activity->type_color }} flex items-center justify-center ring-8 ring-white">
                                            <x-dynamic-component :component="$activity->icon" class="h-5 w-5 text-white"/>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                {!! $activity->description !!}
                                            </p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <time datetime="{{ $activity->created_at }}">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// User Growth Chart
const userCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userCtx, {
    type: 'line',
    data: {
        labels: @json($userGrowthData->pluck('date')),
        datasets: [{
            label: 'Người dùng mới',
            data: @json($userGrowthData->pluck('count')),
            borderColor: '#4F46E5',
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Chat Activity Chart
const chatCtx = document.getElementById('chatActivityChart').getContext('2d');
new Chart(chatCtx, {
    type: 'bar',
    data: {
        labels: @json($chatActivityData->pluck('date')),
        datasets: [{
            label: 'Số cuộc hội thoại',
            data: @json($chatActivityData->pluck('count')),
            backgroundColor: '#818CF8'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endpush
@endsection 