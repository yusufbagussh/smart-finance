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

    <!-- !! BAGIAN INI DIUBAH TOTAL !! -->
    <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                <i class="fas fa-star text-yellow-400 mr-2"></i> AI Summary & Top Tips
            </h3>
            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 text-base">
                {{-- Tampilkan teks dari Gemini --}}
                {!! nl2br(e($geminiRecommendationText)) !!}
            </div>
        </div>
    </div>
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
            <i class="fas fa-search-dollar text-gray-500 mr-2"></i> Detailed Insights
        </h3>
        <div class="space-y-6">
            {{-- Loop melalui hasil Python ($pythonInsights) --}}
            @forelse ($pythonInsights as $rec)
                @php
                    // Logika warna dan ikon SAMA seperti sebelumnya
                    $icon = 'fas fa-info-circle';
                    $theme = 'blue';
                    switch ($rec['type']) {
                        case 'anomaly':
                            $icon = 'fas fa-exclamation-triangle';
                            $theme = 'yellow';
                            break;
                        case 'budget_warning':
                            $icon = 'fas fa-fire';
                            $theme = 'red';
                            break;
                        case 'category_insight':
                            $icon = 'fas fa-lightbulb';
                            $theme = 'blue';
                            break;
                        case 'error':
                            $icon = 'fas fa-times-circle';
                            $theme = 'gray';
                            break;
                        case 'info':
                        default:
                            $icon = 'fas fa-check-circle';
                            $theme = 'green';
                            break;
                    }
                @endphp

                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-{{ $theme }}-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="{{ $icon }} text-{{ $theme }}-500 text-3xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <p class="text-base text-gray-800 dark:text-gray-200">
                                    {{ $rec['message'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0"><i class="fas fa-check-circle text-green-500 text-3xl"></i></div>
                            <div class="ml-5 w-0 flex-1">
                                <p class="text-base text-gray-800 dark:text-gray-200">
                                    Tidak ada temuan spesifik yang perlu perhatian khusus bulan ini. Kerja bagus!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>


    <!-- BAGIAN FINANCIAL HEALTH SCORE DIHAPUS KARENA LOGIKA PRIORITAS SUDAH TIDAK ADA -->
    {{-- <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"> ... </div> --}}


    <!-- General Financial Tips (Ini tetap ada, tidak masalah) -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                <i class="fas fa-graduation-cap mr-2"></i>
                General Financial Tips
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <!-- Tip 1: Track -->
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
                    <!-- Tip 2: Goals -->
                    <div class="flex items-start">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg mr-4 flex-shrink-0">
                            <i class="fas fa-bullseye text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Set Clear Goals</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Define specific
                                financial goals
                                like emergency fund, vacation, or investment targets.
                            </p>
                        </div>
                    </div>
                    <!-- Tip 3: Review -->
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
                    <!-- Tip 4: Automate -->
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
                    <!-- Tip 5: Debt -->
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
                    <!-- Tip 6: Educate -->
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
</x-app-layout>
