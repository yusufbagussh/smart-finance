{{-- resources/views/budgets/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Budget Management') }}
            </h2>
            <a href="{{ route('budgets.create') }}"
                class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Set New Budget
            </a>
        </div>
    </x-slot>

    @if ($budgets->count() > 0)
        <!-- Current Month Budget (if exists) -->
        @php
            $currentMonth = now()->format('Y-m');
            $currentBudget = $budgets->firstWhere('month', $currentMonth);
        @endphp

        @if ($currentBudget)
            <div class="mb-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg overflow-hidden">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ now()->format('F Y') }} Budget
                        </h3>
                        <div class="text-right">
                            <p class="text-lg font-semibold">
                                {{ number_format($currentBudget->progressPercentage(), 1) }}% Used
                            </p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-white bg-opacity-20 rounded-full h-4 mb-4">
                        <div class="h-4 rounded-full {{ $currentBudget->isOverBudget() ? 'bg-red-400' : 'bg-green-400' }}"
                            style="width: {{ min($currentBudget->progressPercentage(), 100) }}%"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-sm opacity-90">Budget Limit</p>
                            <p class="text-xl font-bold">Rp {{ number_format($currentBudget->limit, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm opacity-90">Amount Spent</p>
                            <p class="text-xl font-bold">Rp {{ number_format($currentBudget->spent, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm opacity-90">
                                {{ $currentBudget->isOverBudget() ? 'Over Budget' : 'Remaining' }}</p>
                            <p class="text-xl font-bold">Rp
                                {{ number_format(abs($currentBudget->remainingBudget()), 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if ($currentBudget->isOverBudget())
                        <div class="mt-4 p-3 bg-red-500 bg-opacity-20 border border-red-300 rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span class="text-sm">You have exceeded your monthly budget! Consider reviewing your
                                expenses.</span>
                        </div>
                    @elseif($currentBudget->progressPercentage() > 80)
                        <div class="mt-4 p-3 bg-yellow-500 bg-opacity-20 border border-yellow-300 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span class="text-sm">You're getting close to your budget limit. Monitor your spending
                                carefully.</span>
                        </div>
                    @endif

                    <div class="flex justify-end mt-4 space-x-2">
                        <a href="{{ route('budgets.edit', $currentBudget) }}"
                            class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Budget History -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    <i class="fas fa-history mr-2"></i>
                    Budget History
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($budgets as $budget)
                        <div
                            class="bg-gray-50 rounded-lg p-6 border {{ $budget->month === $currentMonth ? 'ring-2 ring-blue-500 border-blue-500' : 'border-gray-200' }}">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900">
                                    {{ Carbon\Carbon::createFromFormat('Y-m', $budget->month)->format('F Y') }}
                                </h4>
                                @if ($budget->month === $currentMonth)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                        Current
                                    </span>
                                @endif
                            </div>

                            <!-- Budget Stats -->
                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Budget Limit:</span>
                                    <span class="font-semibold text-gray-900">Rp
                                        {{ number_format($budget->limit, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Amount Spent:</span>
                                    <span
                                        class="font-semibold {{ $budget->isOverBudget() ? 'text-red-600' : 'text-gray-900' }}">
                                        Rp {{ number_format($budget->spent, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span
                                        class="text-sm text-gray-600">{{ $budget->isOverBudget() ? 'Over Budget:' : 'Remaining:' }}</span>
                                    <span
                                        class="font-semibold {{ $budget->isOverBudget() ? 'text-red-600' : 'text-green-600' }}">
                                        Rp {{ number_format(abs($budget->remainingBudget()), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                <div class="h-2 rounded-full {{ $budget->isOverBudget() ? 'bg-red-500' : 'bg-blue-500' }}"
                                    style="width: {{ min($budget->progressPercentage(), 100) }}%"></div>
                            </div>

                            <div class="text-center text-sm text-gray-600 mb-4">
                                {{ number_format($budget->progressPercentage(), 1) }}% of budget used
                            </div>

                            <!-- Status Badge -->
                            <div class="text-center mb-4">
                                @if ($budget->isOverBudget())
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Over Budget
                                    </span>
                                @elseif($budget->progressPercentage() > 80)
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Near Limit
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        On Track
                                    </span>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <a href="{{ route('budgets.edit', $budget) }}"
                                    class="flex-1 text-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('budgets.destroy', $budget) }}" class="flex-1"
                                    onsubmit="return confirm('Are you sure you want to delete this budget?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                        <i class="fas fa-trash mr-1"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($budgets->hasPages())
                    <div class="mt-6">
                        {{ $budgets->links() }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-12 text-center">
                <i class="fas fa-chart-pie text-gray-400 text-6xl mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">No Budgets Set</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Start managing your finances by setting monthly budgets. This helps you track your spending and
                    achieve your financial goals.
                </p>

                <div class="space-y-4">
                    <a href="{{ route('budgets.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Set Your First Budget
                    </a>

                    <div class="text-sm text-gray-500">
                        <p>💡 <strong>Tip:</strong> Start with your current month and set a realistic spending limit
                            based on your income.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
