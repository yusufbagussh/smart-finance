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

    <!-- Feature Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <!-- Auto Classification -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-tags text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">
                        Auto Classification
                    </h3>
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Transactions:</span>
                        <span class="font-semibold">{{ $classificationStats['total_transactions'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Auto-Classified:</span>
                        <span class="font-semibold text-blue-600">{{ $classificationStats['auto_classified'] }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Accuracy Rate:</span>
                        <span class="font-semibold text-green-600">{{ $classificationStats['accuracy_rate'] }}%</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                    <div class="bg-blue-500 h-2 rounded-full"
                        style="width: {{ $classificationStats['auto_classified'] }}%"></div>
                </div>

                <!-- Test Classification -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="font-semibold text-gray-900 mb-2">Test Classification</h4>
                    <div x-data="classificationTest()">
                        <input type="text" x-model="testDescription" @input="debounceTest()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                            placeholder="Type a transaction description...">

                        <div x-show="result.category_name" x-transition class="mt-2 p-2 bg-gray-50 rounded text-sm">
                            <span class="font-medium" x-text="result.category_name"></span>
                            <span class="text-gray-500"> (</span>
                            <span class="text-green-600" x-text="Math.round(result.confidence * 100) + '%'"></span>
                            <span class="text-gray-500">)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Predictions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">
                        Expense Predictions
                    </h3>
                </div>

                <p class="text-sm text-gray-600 mb-4">
                    AI-powered predictions for your next 3 months expenses based on historical data.
                </p>

                <!-- Next Month Prediction -->
                @if (isset($predictions[0]))
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-lg mb-4">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-700">Next Month
                                ({{ $predictions[0]['month'] }})</p>
                            <p class="text-2xl font-bold text-green-600">
                                Rp {{ number_format($predictions[0]['predicted_expense'], 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $predictions[0]['confidence'] }}% confidence
                            </p>
                        </div>
                    </div>
                @endif

                <a href="{{ route('ml.predictions') }}"
                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-eye mr-2"></i>
                    View Detailed Predictions
                </a>
            </div>
        </div>

        <!-- Smart Recommendations -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-lightbulb text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">
                        Smart Recommendations
                    </h3>
                </div>

                <p class="text-sm text-gray-600 mb-4">
                    Get personalized tips to improve your financial health based on your spending patterns.
                </p>

                <!-- Quick Recommendations Preview -->
                <div class="space-y-2 mb-4">
                    @foreach ($recommendations as $recommendation)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        @if ($recommendation['type'] === 'reduce_spending')
                                            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                                            Reduce {{ $recommendation['category'] }} spending
                                        @elseif($recommendation['type'] === 'emergency_fund')
                                            <i class="fas fa-piggy-bank text-green-500 mr-1"></i>
                                            Build emergency fund
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Potential savings: Rp
                                        {{ number_format($recommendation['potential_savings'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $recommendation['priority'] === 'high' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($recommendation['priority']) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('ml.recommendations') }}"
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

    <script>
        function classificationTest() {
            return {
                testDescription: '',
                result: {},
                testTimeout: null,

                debounceTest() {
                    clearTimeout(this.testTimeout);
                    this.testTimeout = setTimeout(() => {
                        if (this.testDescription.length > 2) {
                            this.testClassification();
                        } else {
                            this.result = {};
                        }
                    }, 500);
                },

                async testClassification() {
                    try {
                        const response = await fetch('{{ route('ml.classify') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                description: this.testDescription
                            })
                        });

                        if (response.ok) {
                            this.result = await response.json();
                        }
                    } catch (error) {
                        console.log('Classification test failed:', error);
                    }
                }
            }
        }
    </script>
</x-app-layout>
