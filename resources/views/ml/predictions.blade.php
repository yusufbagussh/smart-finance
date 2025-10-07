{{-- resources/views/ml/predictions.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('ml.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-chart-line mr-2"></i>
                {{ __('Expense Predictions') }}
            </h2>
        </div>
    </x-slot>

    <!-- Prediction Overview -->
    <div class="mb-8 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 text-white">
            <h3 class="text-2xl font-bold mb-2">
                <i class="fas fa-crystal-ball mr-2"></i>
                AI Expense Predictions
            </h3>
            <p class="text-green-100">
                Based on your spending patterns from the last 3 months, here are our predictions for your upcoming
                expenses.
            </p>
        </div>
    </div>

    <!-- Predictions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach ($predictions as $index => $prediction)
            <div
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ $index === 0 ? 'ring-2 ring-green-500' : '' }}">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $prediction['month'] }}
                        </h3>
                        @if ($index === 0)
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                Next Month
                            </span>
                        @endif
                    </div>

                    <!-- Predicted Amount -->
                    <div class="text-center mb-4">
                        <p class="text-3xl font-bold text-gray-900 mb-2">
                            Rp {{ number_format($prediction['predicted_expense'], 0, ',', '.') }}
                        </p>
                        <div class="flex items-center justify-center space-x-2">
                            <div class="flex items-center">
                                <i
                                    class="fas fa-{{ $prediction['trend'] === 'increase' ? 'arrow-up text-red-500' : 'arrow-down text-green-500' }} mr-1"></i>
                                <span class="text-sm text-gray-600">
                                    {{ $prediction['trend'] === 'increase' ? 'Increase' : 'Decrease' }} expected
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Confidence Score -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Confidence</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $prediction['confidence'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $prediction['confidence'] }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Prediction Quality Indicator -->
                    <div class="text-center">
                        @if ($prediction['confidence'] >= 85)
                            <span
                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                <i class="fas fa-check-circle mr-1"></i>
                                High Confidence
                            </span>
                        @elseif($prediction['confidence'] >= 70)
                            <span
                                class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Medium Confidence
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Low Confidence
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Prediction Chart -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-area mr-2"></i>
                Prediction Trend
            </h3>
            <div class="relative h-64">
                <canvas id="predictionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Model Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- How Predictions Work -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    How Predictions Work
                </h3>
                <div class="space-y-4 text-sm text-gray-600">
                    <div class="flex items-start">
                        <i class="fas fa-chart-line text-blue-500 mr-3 mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-900">Historical Analysis</p>
                            <p>We analyze your spending patterns from the last 3 months to identify trends and seasonal
                                variations.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-calendar text-green-500 mr-3 mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-900">Seasonal Factors</p>
                            <p>The model considers monthly variations, holidays, and recurring expenses in your
                                spending.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-brain text-purple-500 mr-3 mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-900">Machine Learning</p>
                            <p>Advanced algorithms learn from your data to make increasingly accurate predictions over
                                time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Factors Affecting Predictions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Important Notes
                </h3>
                <div class="space-y-4">
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <i class="fas fa-lightbulb text-yellow-600 mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-medium text-yellow-800 mb-1">Accuracy
                                    Improves Over Time</p>
                                <p class="text-xs text-yellow-700">
                                    The more transaction data you have, the more accurate our predictions become.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex">
                            <i class="fas fa-sync text-blue-600 mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-medium text-blue-800 mb-1">Regular Updates</p>
                                <p class="text-xs text-blue-700">
                                    Predictions are updated daily based on your latest transactions and spending
                                    patterns.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="flex">
                            <i class="fas fa-shield-alt text-gray-600 mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-800 mb-1">Privacy & Security
                                </p>
                                <p class="text-xs text-gray-600">
                                    All predictions are processed locally and your data never leaves our secure servers.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            // Prediction Chart
            const predictionCtx = document.getElementById('predictionChart').getContext('2d');
            const predictionChart = new Chart(predictionCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(collect($predictions)->pluck('month')) !!},
                    datasets: [{
                        label: 'Predicted Expenses',
                        data: {!! json_encode(collect($predictions)->pluck('predicted_expense')) !!},
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
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
                                    return 'Predicted: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
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
        </script>
    @endpush
</x-app-layout>
