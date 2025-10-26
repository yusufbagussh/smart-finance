{{-- resources/views/ml/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-brain mr-2"></i>
            {{ __('AI Features') }}
        </h2>
    </x-slot>

    <!-- Feature Overview -->
    <div class="mb-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 text-white">
            <h3 class="text-2xl font-bold mb-2">
                <i class="fas fa-robot mr-2"></i>
                Smart Finance Assistant
            </h3>
            <p class="text-purple-100">
                Leverage AI to automatically classify transactions, predict future expenses, and get personalized
                financial recommendations.
            </p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                        <i class="fas fa-tags text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Auto Classification
                    </h3>
                </div>

                {{-- Hapus Statistik Mock --}}
                {{-- <div class="space-y-3 mb-4"> ... </div> --}}
                {{-- <div class="w-full bg-gray-200 ..."> ... </div> --}}

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Automatically categorizes your transactions as you add them using a Naive Bayes model trained on
                    your data.
                </p>
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <span class="font-semibold">{{ $classificationStats['total_transactions'] }}</span> transactions
                    processed.
                </div>


                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Test Classification</h4>
                    {{-- Alpine JS component (pastikan Alpine.js terinstal) --}}
                    <div x-data="classificationTest()">
                        <input type="text" x-model="testDescription" @input.debounce.500ms="testClassification"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Type a description (e.g., Gofood Nasi Goreng)">

                        {{-- Hasil Prediksi (Format Baru) --}}
                        <div x-show="!isLoading && (resultCategory || resultType || errorText)" x-transition
                            class="mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded text-sm space-y-1">
                            {{-- Tampilkan Error jika ada --}}
                            <p x-show="errorText" class="text-red-500" x-text="errorText"></p>

                            {{-- Tampilkan Hasil Kategori --}}
                            <div x-show="resultCategory">
                                <span class="font-medium text-gray-800 dark:text-gray-100">Category:</span>
                                <span class="font-semibold text-blue-600 dark:text-blue-400"
                                    x-text="resultCategory"></span>
                                <span class="text-gray-500 dark:text-gray-400">(<span
                                        class="text-green-600 dark:text-green-400"
                                        x-text="resultConfidenceCategory"></span>)</span>
                            </div>

                            {{-- Tampilkan Hasil Tipe --}}
                            <div x-show="resultType">
                                <span class="font-medium text-gray-800 dark:text-gray-100">Type:</span>
                                <span class="font-semibold"
                                    :class="resultType === 'income' ? 'text-green-600 dark:text-green-400' :
                                        'text-red-600 dark:text-red-400'"
                                    x-text="resultType"></span>
                                <span class="text-gray-500 dark:text-gray-400">(<span
                                        class="text-green-600 dark:text-green-400"
                                        x-text="resultConfidenceType"></span>)</span>
                            </div>
                        </div>
                        {{-- Tampilkan Spinner saat loading --}}
                        <div x-show="isLoading" class="mt-2 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                        <i class="fas fa-chart-line text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Expense Predictions
                    </h3>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    AI-powered forecast for your next 30 days of expenses based on historical daily spending.
                </p>

                <div
                    class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 p-4 rounded-lg mb-4">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Predicted Total (Next 30 Days)
                        </p>
                        {{-- Tampilkan hasil dari controller --}}
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $nextMonthPredictionSummary }}
                        </p>
                        {{-- Kita tidak dapat confidence dari API /predict yang disederhanakan --}}
                        {{-- <p class="text-xs text-gray-500 dark:text-gray-400">...</p> --}}
                        @if ($nextMonthPredictionSummary == 'Data Kurang')
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Need more transaction data.</p>
                        @elseif($nextMonthPredictionSummary == 'Error')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">Prediction currently unavailable.</p>
                        @endif
                    </div>
                </div>

                <a href="{{ route('ml.predictions') }}" {{-- Pastikan nama route benar --}}
                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-eye mr-2"></i>
                    View Detailed Predictions
                </a>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                        <i class="fas fa-lightbulb text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Smart Recommendations
                    </h3>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Get personalized AI insights and tips based on your current month's spending.
                </p>

                <div class="space-y-3 mb-4">
                    {{-- Loop melalui $recommendationPreview --}}
                    @forelse ($recommendationPreview as $rec)
                        @php
                            // Logika icon & theme SAMA seperti di recommendations.blade.php
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
                            class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border-l-4 border-{{ $theme }}-500">
                            <div class="flex items-center">
                                <i class="{{ $icon }} text-{{ $theme }}-500 mr-2 flex-shrink-0"></i>
                                <p class="text-sm text-gray-800 dark:text-gray-200">
                                    {{-- Tampilkan pesan preview --}}
                                    {{ Str::limit($rec['message'], 80) }} {{-- Batasi panjang teks --}}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border-l-4 border-green-500">
                            <p class="text-sm text-gray-800 dark:text-gray-200">No specific insights right now. Keep up
                                the good work!</p>
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('ml.recommendations') }}" {{-- Pastikan nama route benar --}}
                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-eye mr-2"></i>
                    View All Recommendations
                </a>
            </div>
        </div>
    </div>


    <!-- How It Works -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">
                <i class="fas fa-cogs mr-2"></i>
                How Our AI Works
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-database text-blue-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Data Analysis</h4>
                    <p class="text-sm text-gray-600">
                        Our AI analyzes your transaction patterns, spending habits, and financial behavior to understand
                        your unique profile.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-brain text-green-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Smart Learning</h4>
                    <p class="text-sm text-gray-600">
                        Machine learning algorithms process your data to identify patterns, predict future expenses, and
                        classify transactions automatically.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-magic text-purple-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Personalized Insights</h4>
                    <p class="text-sm text-gray-600">
                        Get tailored recommendations, spending alerts, and financial tips based on your specific
                        financial situation and goals.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Classification Test --}}
    <script>
        function classificationTest() {
            return {
                testDescription: '',
                // Pisahkan hasil agar lebih mudah diakses di HTML
                resultCategory: null,
                resultConfidenceCategory: null,
                resultType: null,
                resultConfidenceType: null,
                // State management
                isLoading: false,
                errorText: '',
                testTimeout: null,

                async testClassification() {
                    // Reset state
                    this.clearResult();
                    if (this.testDescription.length < 3) return;

                    this.isLoading = true;

                    try {
                        const response = await fetch('{{ route('ml.classify') }}', { // Pastikan route name benar
                            method: 'POST',
                            headers: {
                                /* ... headers ... */
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                description: this.testDescription
                            })
                        });

                        this.isLoading = false;
                        const data = await response.json();

                        if (response.ok) {
                            // Isi properti hasil yang terpisah
                            this.resultCategory = data.predicted_category;
                            this.resultConfidenceCategory = `${Math.round(data.confidence_category * 100)}%`;
                            this.resultType = data.predicted_type;
                            this.resultConfidenceType = `${Math.round(data.confidence_type * 100)}%`;
                            this.errorText = '';
                        } else {
                            this.errorText = data.error || `Error ${response.status}`;
                            this.clearResult(false); // Clear result tapi jangan clear error
                        }
                    } catch (error) {
                        console.error('Classification test failed:', error);
                        this.isLoading = false;
                        this.errorText = 'Network error or invalid response.';
                        this.clearResult(false); // Clear result tapi jangan clear error
                    }
                },
                clearResult(clearError = true) { // Tambahkan opsi untuk tidak clear error
                    this.resultCategory = null;
                    this.resultConfidenceCategory = null;
                    this.resultType = null;
                    this.resultConfidenceType = null;
                    this.isLoading = false;
                    if (clearError) this.errorText = '';
                }
            }
        }
    </script>
</x-app-layout>
