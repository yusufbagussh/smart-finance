{{-- resources/views/admin/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-shield-alt mr-2"></i>
                {{ __('Admin Dashboard') }}
            </h2>
            <div class="mt-2 sm:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                    <i class="fas fa-crown mr-1"></i>
                    Administrator
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Users -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activeUsers }} active</p>
                </div>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="fas fa-exchange-alt text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalTransactions) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <i class="fas fa-tags text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Categories</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalCategories }}</p>
                    <a href="{{ route('admin.categories.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Manage</a>
                </div>
            </div>
        </div>

        <!-- Platform Value -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Value</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Rp {{ number_format(($totalIncome - $totalExpense) / 1000000, 1) }}M
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">All users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Platform Growth Chart -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-chart-area mr-2"></i>
                    Platform Growth (Last 6 Months)
                </h3>
                <div class="relative h-64">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Usage Chart -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Top Categories by Usage
                </h3>
                <div class="relative h-64">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Active Users -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-trophy mr-2"></i>
                        Most Active Users
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                        View All
                    </a>
                </div>

                @if($topUsers->count() > 0)
                    <div class="space-y-3">
                        @foreach($topUsers as $index => $topUser)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center min-w-0 flex-1">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="ml-3 min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $topUser->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ $topUser->email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                        {{ $topUser->transactions_count }} txns
                                    </p>
                                    <a href="{{ route('admin.users.show', $topUser) }}" class="text-xs text-gray-500 dark:text-gray-400 hover:underline">
                                        View details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-gray-400 text-4xl mb-2"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No active users yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-user-plus mr-2"></i>
                        Recent Registrations
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                        View All
                    </a>
                </div>

                @if($recentUsers->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentUsers->take(5) as $recentUser)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center min-w-0 flex-1">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($recentUser->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3 min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $recentUser->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $recentUser->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.users.show', $recentUser) }}"
                                   class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-user-plus text-gray-400 text-4xl mb-2"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No recent registrations</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-bolt mr-2"></i>
                Quick Actions
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Manage Users</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-800 transition-colors">
                    <i class="fas fa-tags text-purple-600 dark:text-purple-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-purple-700 dark:text-purple-300">Manage Categories</span>
                </a>

                <a href="{{ route('admin.categories.create') }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900 rounded-lg hover:bg-green-100 dark:hover:bg-green-800 transition-colors">
                    <i class="fas fa-plus-circle text-green-600 dark:text-green-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-green-700 dark:text-green-300">Add Category</span>
                </a>

                <a href="{{ route('dashboard') }}" class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-eye text-gray-600 dark:text-gray-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">User View</span>
                </a>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Platform Growth Chart
        const growthCtx = document.getElementById('growthChart');
        if (growthCtx) {
            new Chart(growthCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode(collect($monthlyGrowth)->pluck('month')) !!},
                    datasets: [{
                        label: 'New Users',
                        data: {!! json_encode(collect($monthlyGrowth)->pluck('users')) !!},
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Transactions',
                        data: {!! json_encode(collect($monthlyGrowth)->pluck('transactions')) !!},
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }
    
        // Category Usage Chart
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            new Chart(categoryCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryStats->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($categoryStats->pluck('transactions_count')) !!},
                        backgroundColor: {!! json_encode($categoryStats->pluck('color')) !!},
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    return label + ': ' + value + ' transactions';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
    </script>
    @endpush
</x-app-layout>

