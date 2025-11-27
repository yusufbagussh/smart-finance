<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-heartbeat mr-2"></i>
            {{ __('System Monitoring') }}
        </h2>
    </x-slot>


    {{-- Grid untuk Status (yang sudah ada) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">ML API Status</h3>
            @if ($apiStatus === 'ONLINE')
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    <i class="fas fa-check-circle mr-2"></i> ONLINE
                </span>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Response Time: {{ $apiResponseTime }} ms</p>
            @else
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                    <i class="fas fa-exclamation-triangle mr-2"></i> OFFLINE
                </span>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Database Status</h3>
            @if ($dbStatus === 'TERHUBUNG')
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    <i class="fas fa-check-circle mr-2"></i> TERHUBUNG
                </span>
            @else
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                    <i class="fas fa-exclamation-triangle mr-2"></i> GAGAL
                </span>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Server Disk Usage</h3>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                <div class="h-4 rounded-full {{ $diskUsedPercent > 90 ? 'bg-red-500' : ($diskUsedPercent > 75 ? 'bg-yellow-500' : 'bg-blue-500') }}"
                    style="width: {{ $diskUsedPercent }}%">
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 text-center">
                {{ number_format($diskUsedPercent, 2) }}% Used</p>
        </div>
    </div>

    {{-- !! CARD BARU: KINERJA MODEL AI !! --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            <i class="fas fa-robot mr-2"></i> AI Model Performance (Last 100 calls / 24h)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Kolom Klasifikasi --}}
            <div>
                <h4 class="font-medium text-gray-700 dark:text-gray-300">Transaction Classification</h4>
                <ul class="mt-2 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>Avg. Latency: <span
                            class="font-semibold text-gray-900 dark:text-gray-100">{{ $classifyAvgLatency }}
                            ms</span></li>
                    <li>Errors (24h): <span
                            class="font-semibold {{ $classifyErrors > 0 ? 'text-red-500' : 'text-green-500' }}">{{ $classifyErrors }}</span>
                    </li>
                    <li>Avg. Category Confidence: <span
                            class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($classifyAvgConfidenceCat, 1) }}%</span>
                    </li>
                    <li>Avg. Type Confidence: <span
                            class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($classifyAvgConfidenceType, 1) }}%</span>
                    </li>
                </ul>
            </div>

            {{-- Kolom Prediksi --}}
            <div>
                <h4 class="font-medium text-gray-700 dark:text-gray-300">Expense Prediction</h4>
                <ul class="mt-2 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>Avg. Latency: <span
                            class="font-semibold text-gray-900 dark:text-gray-100">{{ $predictAvgLatency }}
                            ms</span></li>
                    <li>Errors (24h): <span
                            class="font-semibold {{ $predictErrors > 0 ? 'text-red-500' : 'text-green-500' }}">{{ $predictErrors }}</span>
                    </li>
                </ul>
            </div>

            {{-- Kolom Rekomendasi/Insights --}}
            <div>
                <h4 class="font-medium text-gray-700 dark:text-gray-300">Insights & Recommendations</h4>
                <ul class="mt-2 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>Python API Latency: <span
                            class="font-semibold text-gray-900 dark:text-gray-100">{{ $recommendPythonAvgLatency }}
                            ms</span></li>
                    <li>Python API Errors (24h): <span
                            class="font-semibold {{ $recommendPythonErrors > 0 ? 'text-red-500' : 'text-green-500' }}">{{ $recommendPythonErrors }}</span>
                    </li>
                    <li>Gemini API Latency: <span
                            class="font-semibold text-gray-900 dark:text-gray-100">{{ $recommendGeminiAvgLatency }}
                            ms</span></li>
                    <li>Gemini API Errors (24h): <span
                            class="font-semibold {{ $recommendGeminiErrors > 0 ? 'text-red-500' : 'text-green-500' }}">{{ $recommendGeminiErrors }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    {{-- !! AKHIR CARD BARU !! --}}

    {{-- Grid untuk Statistik Aplikasi (yang sudah ada) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- ... (Card Total Users, Transactions, Budgets, Failed Jobs) ... --}}
    </div>


</x-app-layout>
