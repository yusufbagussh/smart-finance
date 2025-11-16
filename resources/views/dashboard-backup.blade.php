{{-- resources/views/dashboard.blade.php - VERSION LENGKAP YANG SUDAH DIPERBAIKI --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="mt-2 sm:mt-0 text-sm text-gray-600 dark:text-gray-400">
                Welcome back, {{ Auth::user()->name }}!
            </div>
        </div>
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Balance -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="fas fa-wallet text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Balance</p>
                    <p
                        class="text-2xl font-bold {{ $currentBalance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        Rp {{ number_format($currentBalance, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Monthly Income -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="fas fa-arrow-up text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month Income</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Monthly Expense -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                    <i class="fas fa-arrow-down text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month Expense</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Budget Status -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div
                    class="p-3 rounded-full {{ $currentBudget && $currentBudget->isOverBudget() ? 'bg-red-100 dark:bg-red-900' : 'bg-yellow-100 dark:bg-yellow-900' }}">
                    <i
                        class="fas fa-chart-pie {{ $currentBudget && $currentBudget->isOverBudget() ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400' }} text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Budget Status</p>
                    @if ($currentBudget)
                        <p
                            class="text-xl font-bold {{ $currentBudget->isOverBudget() ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                            {{ number_format($currentBudget->progressPercentage(), 1) }}%
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Rp {{ number_format($currentBudget->remainingBudget(), 0, ',', '.') }}
                            {{ $currentBudget->isOverBudget() ? 'over budget' : 'remaining' }}
                        </p>
                    @else
                        <p class="text-xl font-bold text-gray-600 dark:text-gray-400">No Budget</p>
                        <a href="{{ route('budgets.create') }}"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Set Budget</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Monthly Trend Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-chart-line mr-2"></i>
                    Monthly Trend (Last 6 Months)
                </h3>
                <div class="relative h-64">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-clock mr-2"></i>
                        Recent Transactions
                    </h3>
                    <a href="{{ route('transactions.index') }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                        View All
                    </a>
                </div>

                @if ($recentTransactions->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentTransactions as $transaction)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm"
                                        style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}">
                                        {{ $transaction->category->icon }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ Str::limit($transaction->description, 20) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $transaction->category->name }} •
                                            {{ $transaction->date->format('M d') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p
                                        class="text-sm font-bold {{ $transaction->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }}Rp
                                        {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No transactions yet</p>
                        <a href="{{ route('transactions.create') }}"
                            class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            Add First Transaction
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Category Breakdown and Budget Progress -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Category Breakdown -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Expense Categories (This Month)
                </h3>

                @if ($categoryBreakdown->count() > 0)
                    <div class="relative h-64 mb-4">
                        <canvas id="categoryChart"></canvas>
                    </div>

                    <div class="space-y-2">
                        @foreach ($categoryBreakdown->take(5) as $category)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2"
                                        style="background-color: {{ $category->category->color }}"></div>
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300">{{ $category->category->name }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($category->total, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No expense data for this month</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Budget Progress - content sama seperti sebelumnya -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-bullseye mr-2"></i>
                        Budget Progress
                    </h3>
                    @if (!$currentBudget)
                        <a href="{{ route('budgets.create') }}"
                            class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                            Set Budget
                        </a>
                    @endif
                </div>

                @if ($currentBudget)
                    <!-- Budget content same as before -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Monthly Budget</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($currentBudget->limit, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Spent</span>
                                <span
                                    class="text-lg font-bold {{ $currentBudget->isOverBudget() ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }}">
                                    Rp {{ number_format($currentBudget->spent, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 mb-2">
                                <div class="h-3 rounded-full {{ $currentBudget->isOverBudget() ? 'bg-red-500' : 'bg-blue-500' }}"
                                    style="width: {{ min($currentBudget->progressPercentage(), 100) }}%"></div>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ number_format($currentBudget->progressPercentage(), 1) }}% used
                                </span>
                                <span
                                    class="{{ $currentBudget->isOverBudget() ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ $currentBudget->isOverBudget() ? 'Over by' : 'Remaining' }}:
                                    Rp {{ number_format(abs($currentBudget->remainingBudget()), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-bullseye text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No budget set for this month</p>
                        <a href="{{ route('budgets.create') }}"
                            class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Set Monthly Budget
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-rocket mr-2"></i>
                Quick Actions
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <a href="{{ route('transactions.create') }}"
                    class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900 rounded-lg hover:bg-green-100 dark:hover:bg-green-800 transition-colors">
                    <i class="fas fa-plus-circle text-green-600 dark:text-green-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-green-700 dark:text-green-300">Add Transaction</span>
                </a>

                <a href="{{ route('budgets.create') }}"
                    class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors">
                    <i class="fas fa-chart-pie text-blue-600 dark:text-blue-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Set Budget</span>
                </a>

                <a href="{{ route('transactions.index') }}?type=expense"
                    class="flex flex-col items-center p-4 bg-red-50 dark:bg-red-900 rounded-lg hover:bg-red-100 dark:hover:bg-red-800 transition-colors">
                    <i class="fas fa-search text-red-600 dark:text-red-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-red-700 dark:text-red-300">View Expenses</span>
                </a>

                <a href="{{ route('ml.index') }}"
                    class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-800 transition-colors">
                    <i class="fas fa-brain text-purple-600 dark:text-purple-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-purple-700 dark:text-purple-300">AI Insights</span>
                </a>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Monthly Data:');
                console.log('Labels:', {!! json_encode(collect($monthlyData)->pluck('month')) !!});
                console.log('Income:', {!! json_encode(collect($monthlyData)->pluck('income')) !!});
                console.log('Expense:', {!! json_encode(collect($monthlyData)->pluck('expense')) !!});
                // Monthly Trend Chart
                const monthlyCtx = document.getElementById('monthlyChart');
                if (monthlyCtx) {
                    const monthlyChart = new Chart(monthlyCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: {!! json_encode(collect($monthlyData)->pluck('month')) !!},
                            datasets: [{
                                label: 'Income',
                                data: {!! json_encode(collect($monthlyData)->pluck('income')) !!},
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'Expense',
                                data: {!! json_encode(collect($monthlyData)->pluck('expense')) !!},
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
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
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                }

                // Category Pie Chart
                @if ($categoryBreakdown->count() > 0)
                    const categoryCtx = document.getElementById('categoryChart');
                    if (categoryCtx) {
                        const categoryChart = new Chart(categoryCtx.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: {!! json_encode($categoryBreakdown->pluck('category.name')) !!},
                                datasets: [{
                                    data: {!! json_encode($categoryBreakdown->pluck('total')) !!},
                                    backgroundColor: {!! json_encode($categoryBreakdown->pluck('category.color')) !!},
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return context.label + ': Rp ' + context.parsed
                                                    .toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                @endif
            });
        </script>
    @endpush
</x-app-layout>
