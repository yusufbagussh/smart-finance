{{-- resources/views/ml/recommendations.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('ml.index') }}"
                class="mr-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-lightbulb mr-2"></i>
                {{ __('Smart Recommendations') }}
            </h2>
        </div>
    </x-slot>

    <!-- Recommendations Overview -->
    <div class="mb-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 text-white">
            <h3 class="text-2xl font-bold mb-2">
                <i class="fas fa-magic mr-2"></i>
                Personalized Financial Tips
            </h3>
            <p class="text-purple-100">
                Based on your spending patterns and financial goals, here are AI-powered recommendations to improve your
                financial health.
            </p>
        </div>
    </div>

    @if (count($recommendations) > 0)
        <!-- Priority Recommendations -->
        @php
            $highPriority = collect($recommendations)->where('priority', 'high');
            $mediumPriority = collect($recommendations)->where('priority', 'medium');
            $lowPriority = collect($recommendations)->where('priority', 'low');
        @endphp

        <!-- High Priority Recommendations -->
        @if ($highPriority->count() > 0)
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">High Priority Recommendations
                    </h3>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($highPriority as $recommendation)
                        <div
                            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                            <div class="p-6">
                                @if ($recommendation['type'] === 'reduce_spending')
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                <i class="fas fa-arrow-down text-red-500 mr-2"></i>
                                                Reduce {{ $recommendation['category'] }} Spending
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                You're spending significantly on {{ $recommendation['category'] }}.
                                                Consider reducing expenses in this category.
                                            </p>
                                        </div>
                                        <span
                                            class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full dark:bg-red-900 dark:text-red-200 ml-2">
                                            High Impact
                                        </span>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Current Monthly
                                                    Spending</p>
                                                <p class="text-lg font-bold text-red-600 dark:text-red-400">
                                                    Rp
                                                    {{ number_format($recommendation['current_amount'], 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Suggested Target</p>
                                                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                                    Rp
                                                    {{ number_format($recommendation['suggested_amount'], 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                            <p class="text-sm font-medium text-green-700 dark:text-green-300">
                                                <i class="fas fa-piggy-bank mr-1"></i>
                                                Potential Monthly Savings: Rp
                                                {{ number_format($recommendation['potential_savings'], 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @elseif($recommendation['type'] === 'emergency_fund')
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                                Build Emergency Fund
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                Having an emergency fund is crucial for financial security. Aim to save
                                                3-6 months of expenses.
                                            </p>
                                        </div>
                                        <span
                                            class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full dark:bg-green-900 dark:text-green-200 ml-2">
                                            Essential
                                        </span>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                        <div class="text-center">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Recommended Monthly
                                                Savings</p>
                                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                                Rp {{ number_format($recommendation['suggested_amount'], 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                10% of your monthly income
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                <i class="fas fa-chart-line text-blue-500 mr-2"></i>
                                                {{ ucfirst(str_replace('_', ' ', $recommendation['type'])) }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                Financial recommendation for
                                                {{ $recommendation['category'] ?? 'your finances' }}.
                                            </p>
                                        </div>
                                        <span
                                            class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full dark:bg-blue-900 dark:text-blue-200 ml-2">
                                            Important
                                        </span>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                        <div class="text-center">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Potential Impact
                                            </p>
                                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                Rp
                                                {{ number_format($recommendation['potential_savings'] ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Tips -->
                                @if (isset($recommendation['tips']) && is_array($recommendation['tips']))
                                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                        <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-3">
                                            <i class="fas fa-tasks mr-2"></i>
                                            Action Tips
                                        </h5>
                                        <ul class="space-y-2">
                                            @foreach ($recommendation['tips'] as $tip)
                                                <li class="flex items-start text-sm text-gray-600 dark:text-gray-400">
                                                    <i
                                                        class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                                                    <span>{{ $tip }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Medium Priority Recommendations -->
        @if ($mediumPriority->count() > 0)
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Medium Priority Recommendations
                    </h3>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($mediumPriority as $recommendation)
                        <div
                            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                            <i class="fas fa-chart-line text-yellow-500 mr-2"></i>
                                            Optimize {{ $recommendation['category'] ?? 'Spending' }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            @if (isset($recommendation['category']))
                                                There's room for improvement in your {{ $recommendation['category'] }}
                                                expenses.
                                            @else
                                                There are opportunities to optimize your spending habits.
                                            @endif
                                        </p>
                                    </div>
                                    <span
                                        class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full dark:bg-yellow-900 dark:text-yellow-200 ml-2">
                                        Medium Impact
                                    </span>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Potential Savings</p>
                                            <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400">
                                                Rp
                                                {{ number_format($recommendation['potential_savings'] ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">per month</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Tips -->
                                @if (isset($recommendation['tips']) && is_array($recommendation['tips']))
                                    <div class="space-y-2">
                                        @foreach (array_slice($recommendation['tips'], 0, 3) as $tip)
                                            <div class="flex items-start text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-1 flex-shrink-0"></i>
                                                <span>{{ $tip }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Low Priority Recommendations -->
        @if ($lowPriority->count() > 0)
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Low Priority Recommendations</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($lowPriority as $recommendation)
                        <div
                            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">
                                        <i class="fas fa-thumbs-up text-blue-500 mr-1"></i>
                                        {{ $recommendation['category'] ?? 'General' }} Tip
                                    </h5>
                                    <span
                                        class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full dark:bg-blue-900 dark:text-blue-200">
                                        Low
                                    </span>
                                </div>

                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                    Rp {{ number_format($recommendation['potential_savings'] ?? 0, 0, ',', '.') }}
                                    potential monthly savings
                                </p>

                                @if (isset($recommendation['tips']) && is_array($recommendation['tips']) && count($recommendation['tips']) > 0)
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $recommendation['tips'][0] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Financial Health Score -->
        <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    <i class="fas fa-heartbeat mr-2"></i>
                    Your Financial Health Score
                </h3>

                @php
                    $healthScore =
                        85 - $highPriority->count() * 15 - $mediumPriority->count() * 10 - $lowPriority->count() * 5;
                    $healthScore = max(30, min(100, $healthScore));

                    $scoreColor = 'text-red-500';
                    $scoreStatus = 'Needs Improvement';
                    $scoreMessage = 'Focus on our high-priority recommendations to improve your financial health.';

                    if ($healthScore >= 80) {
                        $scoreColor = 'text-green-500';
                        $scoreStatus = 'Excellent Financial Health';
                        $scoreMessage = 'You\'re doing great! Keep up the good financial habits.';
                    } elseif ($healthScore >= 60) {
                        $scoreColor = 'text-yellow-500';
                        $scoreStatus = 'Good Financial Health';
                        $scoreMessage = 'You\'re on the right track. Follow our recommendations to improve further.';
                    }
                @endphp

                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-32 h-32">
                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-300 dark:text-gray-600" stroke="currentColor" stroke-width="3"
                                fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="{{ $scoreColor }}" stroke="currentColor" stroke-width="3" fill="none"
                                stroke-dasharray="{{ $healthScore }}, 100"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <span
                                    class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $healthScore }}</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Score</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-6">
                    <h4 class="text-xl font-bold {{ $scoreColor }} mb-2">{{ $scoreStatus }}</h4>
                    <p class="text-gray-600 dark:text-gray-400">{{ $scoreMessage }}</p>
                </div>

                <!-- Score Breakdown -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-chart-line text-blue-500 text-2xl mb-2"></i>
                        <h5 class="font-semibold text-gray-900 dark:text-gray-100">Spending Control</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $highPriority->where('type', 'reduce_spending')->count() === 0 ? 'Good' : 'Needs Work' }}
                        </p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-piggy-bank text-green-500 text-2xl mb-2"></i>
                        <h5 class="font-semibold text-gray-900 dark:text-gray-100">Emergency Fund</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $highPriority->where('type', 'emergency_fund')->count() === 0 ? 'Good' : 'Needs Building' }}
                        </p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-balance-scale text-purple-500 text-2xl mb-2"></i>
                        <h5 class="font-semibold text-gray-900 dark:text-gray-100">Budget Balance</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $mediumPriority->count() <= 1 ? 'Good' : 'Can Improve' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- General Financial Tips -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    General Financial Tips
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-4 flex-shrink-0">
                                <i class="fas fa-calculator text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Track Every Expense
                                </h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Record all transactions, no matter
                                    how small. This helps identify spending patterns.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg mr-4 flex-shrink-0">
                                <i class="fas fa-target text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Set Clear Goals</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Define specific financial goals
                                    like emergency fund, vacation, or investment targets.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg mr-4 flex-shrink-0">
                                <i class="fas fa-sync text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Review Regularly</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Review your finances monthly to
                                    adjust budgets and track progress.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg mr-4 flex-shrink-0">
                                <i class="fas fa-shield-alt text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Automate Savings</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Set up automatic transfers to
                                    savings accounts to build wealth consistently.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg mr-4 flex-shrink-0">
                                <i class="fas fa-credit-card text-red-600 dark:text-red-400"></i>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Manage Debt</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Pay off high-interest debt first
                                    and avoid unnecessary borrowing.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg mr-4 flex-shrink-0">
                                <i class="fas fa-book text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Educate Yourself</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Read books, take courses, and stay
                                    informed about personal finance.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-12 text-center">
                <i class="fas fa-lightbulb text-gray-400 text-6xl mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">No Recommendations Yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    Add more transactions and set budgets to get personalized AI recommendations for improving your
                    financial health.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('transactions.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Transactions
                    </a>
                    <a href="{{ route('budgets.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-purple-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-chart-pie mr-2"></i>
                        Set Budget
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
