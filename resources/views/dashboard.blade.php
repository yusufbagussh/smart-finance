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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="fas fa-wallet text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    {{-- Ubah nama agar lebih jelas --}}
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Cash Balance</p>
                    <p
                        class="text-2xl font-bold {{ $currentBalance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        Rp {{ number_format($currentBalance, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- KARTU 2: TOTAL INVESTMENT VALUE --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            {{-- Kita buat bisa diklik untuk ke halaman portofolio --}}
            <a href="{{ route('portfolios.index') }}" class="block hover:opacity-80 transition-opacity">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900">
                        <i class="fas fa-landmark text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Investment Value</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($totalInvestmentValue, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- KARTU 3: TOTAL NET WORTH (KARTU BARU) --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <i class="fas fa-file-invoice-dollar text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Net Worth</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Rp {{ number_format($totalNetWorth, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

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
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
                @php
                    /* Logika icon & bg sama */
                @endphp
                <div class="p-3 rounded-full {{ $budgetBgClass ?? 'bg-yellow-100 dark:bg-yellow-900' }}">
                    <i
                        class="fas fa-bullseye {{ $budgetIconClass ?? 'text-yellow-600 dark:text-yellow-400' }} text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Budget Status</p>
                    @if ($budgetSummary)
                        <p
                            class="text-xl font-bold {{ $budgetSummary->isOverBudget ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ number_format($budgetSummary->progress, 1) }}%
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Rp {{ number_format(abs($budgetSummary->remaining), 0, ',', '.') }}
                            {{ $budgetSummary->isOverBudget ? 'over budget' : 'remaining' }}
                        </p>
                    @else
                        <p class="text-xl font-bold text-gray-600 dark:text-gray-400">No Budget Set</p>
                        <a href="{{ route('budgets.create') }}"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Set Budget</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                {{-- Filter Buttons --}}
                {{-- Form untuk Filter --}}
                <form method="GET" action="{{ route('dashboard') }}" id="chart-filter-form" class="mb-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                        {{-- Judul Grafik --}}
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap mr-4">
                            <i class="fas fa-chart-line mr-2"></i>
                            {{ $chartTitle }}
                        </h3>

                        {{-- Kontainer Filter --}}
                        <div class="flex flex-wrap items-center justify-end gap-2"> {{-- Gap untuk jarak --}}
                            {{-- Tombol Filter Daily/Monthly --}}
                            <div class="flex rounded-md shadow-sm">
                                {{-- Hidden input agar filter tetap terkirim saat submit date range --}}
                                <input type="hidden" name="filter" value="{{ $filter }}">

                                {{-- <a href="{{ route('dashboard', ['filter' => 'daily'] + request()->except('filter')) }}"
                                    class="px-3 py-1 rounded-l-md text-xs font-medium transition-colors border border-gray-300 dark:border-gray-600 {{ $filter === 'daily' ? 'bg-blue-600 text-white border-blue-600 z-10' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                    Daily
                                </a> --}}
                                <a href="{{ route('dashboard', ['filter' => 'daily']) }}"
                                    class="px-3 py-1 rounded-l-md text-xs font-medium transition-colors border border-gray-300 dark:border-gray-600 {{ $filter === 'daily' ? 'bg-blue-600 text-white border-blue-600 z-10' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                    Daily
                                </a>
                                {{-- <a href="{{ route('dashboard', ['filter' => 'monthly'] + request()->except('filter')) }}"
                                    class="px-3 py-1 rounded-r-md text-xs font-medium transition-colors border border-l-0 border-gray-300 dark:border-gray-600 {{ $filter === 'monthly' ? 'bg-blue-600 text-white border-blue-600 z-10' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                    Monthly
                                </a> --}}
                                <a href="{{ route('dashboard', ['filter' => 'monthly']) }}"
                                    class="px-3 py-1 rounded-r-md text-xs font-medium transition-colors border border-l-0 border-gray-300 dark:border-gray-600 {{ $filter === 'monthly' ? 'bg-blue-600 text-white border-blue-600 z-10' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                    Monthly
                                </a>
                            </div>

                            {{-- Input Flatpickr Date Range --}}
                            <div class="relative">
                                <input type="text" id="chart_date_range" name="chart_date_range"
                                    {{-- Nama berbeda agar tidak konflik --}}
                                    class="text-xs focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:w-56 shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md py-1 px-3"
                                    placeholder="{{ $filter === 'daily' ? 'Select date range (max 30d)' : 'Select month range (max 12m)' }}"
                                    value="{{ $dateFrom && $dateTo ? $dateFrom . ' to ' . $dateTo : '' }}">
                                {{-- Input hidden --}}
                                <input type="hidden" name="date_from" id="chart_date_from"
                                    value="{{ $dateFrom }}">
                                <input type="hidden" name="date_to" id="chart_date_to" value="{{ $dateTo }}">
                            </div>

                            {{-- Tombol Submit Filter --}}
                            <button type="submit" title="Apply Date Filter"
                                class="px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-filter"></i>
                                {{-- <span class="ml-1 hidden sm:inline">Apply</span> --}}
                            </button>
                            {{-- Tombol Reset (opsional) --}}
                            @if ($dateFrom || $dateTo)
                                <a href="{{ route('dashboard', ['filter' => $filter]) }}" title="Reset Date Filter"
                                    class="px-3 py-1 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
                {{-- Akhir Form Filter --}}
                {{-- Chart Canvas --}}
                <div class="relative h-64">
                    <canvas id="trendChart"></canvas> {{-- Ganti ID agar lebih generik --}}
                </div>
            </div>
        </div>

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
                                        style="background-color: {{ $transaction->category->color ?? '#cccccc' }}20; color: {{ $transaction->category->color ?? '#666666' }}">
                                        {!! $transaction->category->icon ?? '<i class="fas fa-question"></i>' !!}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ Str::limit($transaction->description, 20) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $transaction->category->name ?? 'N/A' }} •
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
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
                        @foreach ($categoryBreakdown->take(5) as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2"
                                        style="background-color: {{ $item->category->color ?? '#cccccc' }}"></div>
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300">{{ $item->category->name ?? 'N/A' }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                    ({{ number_format(($item->total / max($monthlyExpense, 1)) * 100, 1) }}%)
                                </span>
                            </div>
                        @endforeach
                        @if ($categoryBreakdown->count() > 5)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2 bg-gray-400"></div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Others</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($categoryBreakdown->slice(5)->sum('total'), 0, ',', '.') }}
                                    ({{ number_format(($categoryBreakdown->slice(5)->sum('total') / max($monthlyExpense, 1)) * 100, 1) }}%)
                                </span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No expense data for this month</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-tasks mr-2"></i>
                        Budget Progress by Category
                    </h3>
                    {{-- Tombol "Set Budget" hanya jika TIDAK ADA budget sama sekali --}}
                    @if ($currentMonthBudgets->isEmpty() && !$budgetSummary)
                        <a href="{{ route('budgets.create') }}"
                            class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                            Set Budget
                        </a>
                    @endif
                </div>

                {{-- Cek apakah ada budget yang diatur untuk bulan ini --}}
                @if ($currentMonthBudgets->isNotEmpty())
                    <div class="space-y-5 max-h-96 overflow-y-auto pr-2"> {{-- Added max height and scroll --}}
                        {{-- Loop melalui setiap budget kategori bulan ini --}}
                        @foreach ($currentMonthBudgets as $budget)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                {{-- Nama Kategori dan Limit --}}
                                <div class="flex justify-between items-center mb-1">
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-200">{{ $budget->category->name ?? 'Uncategorized' }}</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Limit: Rp
                                        {{ number_format($budget->limit, 0, ',', '.') }}</span>
                                </div>

                                {{-- Progress Bar --}}
                                @php
                                    $progress = $budget->progressPercentage();
                                    $barColor = $budget->isOverBudget() ? 'bg-red-500' : 'bg-blue-500';
                                    $width = $budget->isOverBudget() ? 100 : min($progress, 100); // Width capped at 100%
                                @endphp
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 mb-1">
                                    <div class="h-3 rounded-full {{ $barColor }}"
                                        style="width: {{ $width }}%"></div>
                                </div>

                                {{-- Detail Spent vs Remaining/Over --}}
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        Spent: Rp {{ number_format($budget->spent, 0, ',', '.') }}
                                        ({{ number_format($progress, 1) }}%)
                                    </span>
                                    <span
                                        class="{{ $budget->isOverBudget() ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                        {{ $budget->isOverBudget() ? 'Over by' : 'Remaining' }}: Rp
                                        {{ number_format(abs($budget->remainingBudget()), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Tampilan jika tidak ada budget sama sekali --}}
                    <div class="text-center py-8">
                        <i class="fas fa-bullseye text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No budgets set for this month</p>
                        <a href="{{ route('budgets.create') }}"
                            class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            <i class="fas fa-plus mr-2"></i>
                            Set Budget
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-rocket mr-2"></i>
                Quick Actions
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {{-- <a href="{{ route('transactions.create') }}"
                    class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900 rounded-lg hover:bg-green-100 dark:hover:bg-green-800 transition-colors">
                    <i class="fas fa-plus-circle text-green-600 dark:text-green-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-center text-green-700 dark:text-green-300">Add
                        Transaction</span>
                </a> --}}
                <a href="{{ route('investment-transactions.create') }}"
                    class="flex flex-col items-center p-4 bg-indigo-50 dark:bg-indigo-900 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800 transition-colors">
                    <i class="fas fa-chart-line text-indigo-600 dark:text-indigo-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-center text-indigo-700 dark:text-indigo-300">Add
                        Investment</span>
                </a>
                <a href="{{ route('budgets.create') }}"
                    class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors">
                    <i class="fas fa-chart-pie text-blue-600 dark:text-blue-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-center text-blue-700 dark:text-blue-300">Set Budget</span>
                </a>
                <a href="{{ route('transactions.index') }}?type=expense"
                    class="flex flex-col items-center p-4 bg-red-50 dark:bg-red-900 rounded-lg hover:bg-red-100 dark:hover:bg-red-800 transition-colors">
                    <i class="fas fa-search text-red-600 dark:text-red-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-center text-red-700 dark:text-red-300">View Expenses</span>
                </a>
                <a href="{{ route('ml.index') }}"
                    class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-800 transition-colors">
                    <i class="fas fa-brain text-purple-600 dark:text-purple-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-center text-purple-700 dark:text-purple-300">AI
                        Insights</span>
                </a>
            </div>
        </div>
    </div>

    {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"> ... </div> --}}

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dateRangeInput = document.getElementById('chart_date_range');
                const dateFromInput = document.getElementById('chart_date_from');
                const dateToInput = document.getElementById('chart_date_to');
                const currentFilter = '{{ $filter }}'; // 'daily' or 'monthly'

                let flatpickrInstance = flatpickr(dateRangeInput, {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: currentFilter === 'daily' ? "M d, Y" : "M Y",
                    maxDate: "today", // Tetap batasi hingga hari ini
                    plugins: currentFilter === 'monthly' ? [
                        new monthSelectPlugin({
                            shorthand: true,
                            dateFormat: "Y-m",
                            altFormat: "M Y",
                            theme: document.documentElement.classList.contains('dark') ? "dark" :
                                "light"
                        })
                    ] : [],

                    // !! VALIDASI RANGE DIMULAI DI SINI !!
                    onReady: function(selectedDates, dateStr, instance) {
                        // Set nilai awal dari input hidden
                        const initialDates = [dateFromInput.value, dateToInput.value].filter(d => d);
                        if (initialDates.length === 2) {
                            instance.setDate(initialDates, false); // Set tanggal tanpa memicu onChange
                        }
                    },
                    onChange: function(selectedDates, dateStr, instance) {
                        // Reset maxDate setiap kali pilihan berubah
                        instance.set('maxDate', "today");

                        // Jika baru memilih tanggal/bulan PERTAMA
                        if (selectedDates.length === 1) {
                            const startDate = selectedDates[0];
                            let maxEndDate = null;

                            if (currentFilter === 'daily') {
                                // Hitung tanggal maksimal: startDate + 29 hari
                                maxEndDate = new Date(startDate.getTime());
                                maxEndDate.setDate(startDate.getDate() + 29); // Max 30 hari range
                            } else { // monthly
                                // Hitung bulan maksimal: startDate + 11 bulan
                                maxEndDate = new Date(startDate.getFullYear(), startDate.getMonth() + 11,
                                    startDate.getDate()); // Max 12 bulan range
                            }

                            // Batasi juga agar tidak melebihi hari ini
                            const today = new Date();
                            if (maxEndDate > today) {
                                maxEndDate = today;
                            }

                            // Set maxDate dinamis untuk pilihan tanggal KEDUA
                            instance.set('maxDate', maxEndDate);
                            // Kosongkan input hidden saat baru memilih tanggal pertama
                            dateFromInput.value = '';
                            dateToInput.value = '';

                        }
                        // Jika sudah memilih tanggal/bulan KEDUA (range lengkap)
                        else if (selectedDates.length === 2) {
                            const startDate = selectedDates[0];
                            const endDate = selectedDates[1];

                            // Validasi ulang (sebagai fallback, jika maxDate gagal)
                            let isValid = true;
                            if (currentFilter === 'daily') {
                                let diffDays = (endDate.getTime() - startDate.getTime()) / (1000 * 60 * 60 *
                                    24);
                                if (diffDays > 29) isValid = false;
                            } else { // monthly
                                let diffMonths = (endDate.getFullYear() - startDate.getFullYear()) * 12 +
                                    endDate.getMonth() - startDate.getMonth();
                                if (diffMonths > 11) isValid = false;
                            }

                            if (isValid) {
                                // Isi input hidden JIKA valid
                                if (currentFilter === 'monthly') {
                                    dateFromInput.value = instance.formatDate(new Date(startDate
                                        .getFullYear(), startDate.getMonth(), 1), "Y-m-d");
                                    dateToInput.value = instance.formatDate(new Date(endDate.getFullYear(),
                                        endDate.getMonth(), 1), "Y-m-d");
                                } else {
                                    dateFromInput.value = instance.formatDate(startDate, "Y-m-d");
                                    dateToInput.value = instance.formatDate(endDate, "Y-m-d");
                                }
                            } else {
                                // Jika tidak valid (misal user mengetik manual range terlalu lebar)
                                // Beri tahu user dan reset pilihan
                                Swal.fire({ // Gunakan SweetAlert jika ada
                                    icon: 'warning',
                                    title: 'Invalid Range',
                                    text: `Maximum range allowed is ${currentFilter === 'daily' ? '30 days' : '12 months'}. Please select again.`,
                                });
                                instance.clear(); // Hapus pilihan
                                dateFromInput.value = '';
                                dateToInput.value = '';
                                return; // Hentikan proses lebih lanjut
                            }
                        }
                        // Jika pilihan dihapus (selectedDates kosong)
                        else {
                            dateFromInput.value = '';
                            dateToInput.value = '';
                        }
                    },
                    // onClose tidak perlu diubah, tapi pastikan tidak konflik
                    onClose: function(selectedDates, dateStr, instance) {
                        // Jika hanya 1 tanggal dipilih (mode daily), set end = start
                        if (selectedDates.length === 1 && currentFilter === 'daily') {
                            const theDate = selectedDates[0];
                            dateFromInput.value = instance.formatDate(theDate, "Y-m-d");
                            dateToInput.value = instance.formatDate(theDate, "Y-m-d");
                            instance.setDate([theDate, theDate], true);
                        }
                        // Reset maxDate setelah kalender ditutup
                        instance.set('maxDate', "today");
                    },
                    // defaultDate: [dateFromInput.value, dateToInput.value].filter(d => d)
                });

                // Fungsi validasi sederhana (opsional)
                function validateDateRange(selectedDates) {
                    if (selectedDates.length !== 2) return;
                    let start = selectedDates[0];
                    let end = selectedDates[1];
                    let diffDays = (end.getTime() - start.getTime()) / (1000 * 60 * 60 * 24);
                    let diffMonths = (end.getFullYear() - start.getFullYear()) * 12 + end.getMonth() - start.getMonth();

                    if (currentFilter === 'daily' && diffDays > 29) {
                        Swal.fire('Info', 'Maximum date range for daily view is 30 days.', 'info');
                        // Reset? Atau biarkan controller yg membatasi
                    } else if (currentFilter === 'monthly' && diffMonths > 11) {
                        Swal.fire('Info', 'Maximum range for monthly view is 12 months.', 'info');
                        // Reset?
                    }
                }

                // !! AKHIR INISIALISASI FLATPICKR !!

                // Monthly Trend Chart (Kode chart Anda sudah benar, saya salin saja)
                const trendCtx = document.getElementById('trendChart'); // Gunakan ID baru
                if (trendCtx && typeof Chart !== 'undefined') {
                    const trendChart = new Chart(trendCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            // Gunakan data dari Controller
                            labels: {!! json_encode($chartLabels) !!},
                            datasets: [{
                                label: 'Income',
                                // Gunakan data dari Controller
                                data: {!! json_encode($chartIncomeData) !!},
                                borderColor: '#10B981', // green-500
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'Expense',
                                // Gunakan data dari Controller
                                data: {!! json_encode($chartExpenseData) !!},
                                borderColor: '#EF4444', // red-500
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: { // Opsi chart sama seperti sebelumnya
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            // Format Rupiah
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
                } else {
                    console.error("Trend chart canvas not found or Chart.js not loaded.");
                }

                // Category Pie Chart (Kode chart Anda sudah benar, saya salin saja)
                @if ($categoryBreakdown->count() > 0)
                    const categoryCtx = document.getElementById('categoryChart');
                    // Prepare data, limiting to top 5 + others
                    const categoryDataRaw = @json($categoryBreakdown);
                    const topCategories = categoryDataRaw.slice(0, 5);
                    const otherTotal = categoryDataRaw.slice(5).reduce((sum, item) => sum + parseFloat(item
                        .total), 0);

                    const categoryLabels = topCategories.map(item => item.category?.name || 'N/A');
                    const categoryTotals = topCategories.map(item => item.total);
                    const categoryColors = topCategories.map(item => item.category?.color || '#CCCCCC');

                    if (otherTotal > 0) {
                        categoryLabels.push('Others');
                        categoryTotals.push(otherTotal);
                        categoryColors.push('#9CA3AF'); // gray-400
                    }

                    if (categoryCtx && typeof Chart !== 'undefined') {
                        const categoryChart = new Chart(categoryCtx.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: categoryLabels,
                                datasets: [{
                                    data: categoryTotals,
                                    backgroundColor: categoryColors,
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
                                                let label = context.label || '';
                                                let value = context.parsed || 0;
                                                let total = context.chart.data.datasets[0].data
                                                    .reduce((a,
                                                        b) => a + b, 0);
                                                let percentage = total > 0 ? ((value / total) *
                                                        100)
                                                    .toFixed(1) : 0;
                                                return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        console.error("Category chart canvas not found or Chart.js not loaded.");
                    }
                @endif
            });
        </script>
    @endpush
</x-app-layout>
