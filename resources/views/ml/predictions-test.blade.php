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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
            <h3 class="text-lg font-semibold text-indigo-100 mb-2">
                <i class="fas fa-calculator mr-2"></i>
                Predicted Expense (Next 30 Days)
            </h3>
            <div class="text-4xl font-bold">
                {{ $nextMonthPrediction }}
            </div>
            <p class="text-indigo-200 text-sm mt-2">
                This is the total estimated spending based on your historical data.
            </p>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                How This Works
            </h3>
            <p class="text-sm text-gray-600">
                Our AI (ARIMA Model) analyzes your past daily spending to find patterns and forecast your expenses for the next 30 days.
            </p>
            @if (empty($forecastData))
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Not Enough Data.</strong> We need more transaction history to generate a forecast.
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-area mr-2"></i>
                30-Day Predicted Spending Trend
            </h3>
            <div class="relative h-96"> {{-- Dibuat lebih tinggi --}}
                <canvas id="predictionChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Kita menggunakan script CDN Chart.js jika belum ada --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}

    <script>
        // Memastikan ada data sebelum mencoba membuat chart
        @if (!empty($forecastData))
            // 1. Ambil data dari Controller
            const forecastData = @json($forecastData);

            // 2. Proses data untuk Chart.js menggunakan JavaScript
            const labels = forecastData.map(item => item.date);
            const data = forecastData.map(item => item.amount);

            // 3. Render Chart
            const predictionCtx = document.getElementById('predictionChart').getContext('2d');
            const predictionChart = new Chart(predictionCtx, {
                type: 'line',
                data: {
                    labels: labels, // Sumbu X (Tanggal)
                    datasets: [{
                        label: 'Predicted Expenses',
                        data: data, // Sumbu Y (Amount)
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3B82F6',
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
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        // Format ke Rupiah
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR'
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false, // Prediksi tidak harus mulai dari 0
                            ticks: {
                                callback: function(value) {
                                    // Format Sumbu Y (misal: 10M, 5M)
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: {
                            ticks: {
                                // Hanya tampilkan beberapa label tanggal agar tidak penuh
                                maxTicksLimit: 10
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        @endif
    </script>
    @endpush
</x-app-layout>
