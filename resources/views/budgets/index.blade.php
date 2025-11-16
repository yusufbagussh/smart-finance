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


    <!-- Current Month Budget (if exists) -->
    {{-- @php
            $currentMonth = now()->format('Y-m');
            $currentBudget = $budgets->firstWhere('month', $currentMonth);
        @endphp --}}

    @if ($currentMonthBudgets->isNotEmpty())
        <div
            class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-blue-200 dark:border-gray-700">
            {{-- Bagian Atas: Ringkasan Total --}}
            <div
                class="p-6 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 border-b border-blue-100 dark:border-gray-600">
                <div class="flex flex-col sm:flex-row items-center justify-between mb-4">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        {{ now()->format('F Y') }} Budget Summary
                    </h3>
                    <div class="text-right mt-2 sm:mt-0">
                        <p
                            class="text-lg font-semibold {{ $currentMonthSummary->isOverBudget ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ number_format($currentMonthSummary->progress, 1) }}% Used
                        </p>
                    </div>
                </div>

                {{-- Progress Bar Total --}}
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4 mb-4">
                    <div class="h-4 rounded-full {{ $currentMonthSummary->isOverBudget ? 'bg-red-500' : 'bg-green-500' }}"
                        style="width: {{ min($currentMonthSummary->progress, 100) }}%"></div>
                </div>

                {{-- Angka Total --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Limit</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100">Rp
                            {{ number_format($currentMonthSummary->limit, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Spent</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100">Rp
                            {{ number_format($currentMonthSummary->spent, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $currentMonthSummary->isOverBudget ? 'Over Budget' : 'Remaining' }}</p>
                        <p
                            class="text-xl font-bold {{ $currentMonthSummary->isOverBudget ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            Rp {{ number_format(abs($currentMonthSummary->remaining), 0, ',', '.') }}</p>
                    </div>
                </div>
                @if ($currentMonthSummary->isOverBudget)
                    <div class="mt-4 text-center text-sm text-red-600 dark:text-red-400">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Your total spending has exceeded the total
                        budget limit!
                    </div>
                @endif
            </div>

            {{-- Bagian Bawah: Detail per Kategori Bulan Ini --}}
            <div class="p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-tasks mr-2"></i> Category Budgets (This Month)
                </h4>
                <div class="space-y-5 max-h-96 overflow-y-auto pr-2"> {{-- Scroll jika banyak --}}
                    @foreach ($currentMonthBudgets as $budget)
                        <div
                            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            {{-- Nama Kategori & Tombol Aksi --}}
                            <div class="flex justify-between items-center mb-2">
                                <span
                                    class="font-medium text-gray-800 dark:text-gray-200">{{ $budget->category->name ?? 'Uncategorized' }}</span>
                                <div class="flex items-center space-x-3"> {{-- Tambahkan div ini --}}
                                    <a href="{{ route('budgets.edit', $budget) }}"
                                        class="text-xs text-blue-600 hover:text-blue-900 font-medium"
                                        title="Edit Budget">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('budgets.destroy', $budget) }}" method="POST"
                                        class="inline mb-0 delete-form"> {{-- Pastikan form inline dan margin bawah 0 --}}
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-xs text-red-600 hover:text-red-900 font-medium"
                                            title="Delete Budget">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            @php
                                $progress = $budget->progressPercentage();
                                $barColor = $budget->isOverBudget()
                                    ? 'bg-red-500'
                                    : ($progress > 80
                                        ? 'bg-yellow-500'
                                        : 'bg-green-500');
                                $width = $budget->isOverBudget() ? 100 : min($progress, 100);
                            @endphp
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 mb-1">
                                <div class="h-3 rounded-full {{ $barColor }}" style="width: {{ $width }}%">
                                </div>
                            </div>

                            {{-- Detail Spent vs Remaining/Over --}}
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400">
                                    Rp {{ number_format($budget->spent, 0, ',', '.') }} /
                                    {{ number_format($budget->limit, 0, ',', '.') }}
                                    ({{ number_format($progress, 1) }}%)
                                </span>
                                <span
                                    class="{{ $budget->isOverBudget() ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ $budget->isOverBudget() ? 'Over:' : 'Left:' }} Rp
                                    {{ number_format(abs($budget->remainingBudget()), 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Status Badge --}}
                            <div class="text-center mt-2">
                                @if ($budget->isOverBudget())
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Over Budget
                                    </span>
                                @elseif($progress > 80)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Near Limit
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i> On Track
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($budgets->count() > 0 || $searchHistory || $categoryHistory || $monthHistory)
        <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div x-data="{ open: {{ $searchHistory || $categoryHistory || $monthHistory ? 'true' : 'false' }} }" class="p-6"> {{-- Buka jika ada filter aktif --}}
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-filter mr-2"></i> Filter Budget History
                    </h3>
                    <button @click="open = !open" type="button" {{-- Tambahkan type="button" agar tidak men-submit form --}}
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                        {{-- Tampilkan ikon dan teks berdasarkan state 'open' --}}
                        <span x-show="!open" class="flex items-center">
                            <i class="fas fa-filter mr-2"></i> Show Filters
                        </span>
                        <span x-show="open" class="flex items-center">
                            <i class="fas fa-chevron-up mr-2"></i> Hide Filters
                        </span>
                    </button>
                </div>

                <form method="GET" action="{{ route('budgets.index') }}" x-show="open" x-transition
                    class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        {{-- Search by Category Name --}}
                        <div>
                            <label for="search_history"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search
                                Category</label>
                            <input type="text" name="search_history" id="search_history"
                                value="{{ $searchHistory }}"
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter category name...">
                        </div>

                        {{-- Filter by Category --}}
                        <div>
                            <label for="category_history"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                            <select name="category_history" id="category_history"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}"
                                        {{ $categoryHistory == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter by Month --}}
                        <div>
                            <label for="month_history_display" {{-- Ganti ID untuk display --}}
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
                            {{-- Input teks untuk Flatpickr --}}
                            <input type="text" id="month_history_display" {{-- ID untuk Flatpickr --}}
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-indigo-500 focus:border-indigo-500 flatpickr-input"
                                placeholder="Select month..." readonly> {{-- Readonly agar hanya bisa via picker --}}
                            {{-- Input hidden untuk mengirim nilai YYYY-MM --}}
                            <input type="hidden" name="month_history" id="month_history"
                                value="{{ $monthHistory }}">
                        </div>
                    </div>
                    <div class="flex space-x-2 mb-2 sm:mb-0">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('budgets.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <!-- Budget History -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    <i class="fas fa-history mr-2"></i>
                    All Budgets
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($budgets as $budget)
                        @php
                            $currentMonth = now()->format('Y-m');
                        @endphp
                        <div
                            class="bg-gray-50 rounded-lg p-6 border {{ $budget->month === $currentMonth ? 'ring-2 ring-blue-500 border-blue-500' : 'border-gray-200' }}">

                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        {{-- Tampilkan Nama Kategori --}}
                                        {{ $budget->category->name ?? 'Uncategorized' }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        {{-- Tampilkan Bulan sebagai sub-judul --}}
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $budget->month)->format('F Y') }}
                                    </p>
                                </div>
                                @if ($budget->month === $currentMonth)
                                    <span
                                        class="flex-shrink-0 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                        Current
                                    </span>
                                @endif
                            </div>
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

                            @php
                                $progress = $budget->progressPercentage();
                                $barColor = $budget->isOverBudget() ? 'bg-red-500' : 'bg-blue-500';
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                <div class="h-2 rounded-full {{ $barColor }}"
                                    style="width: {{ min($progress, 100) }}%"></div>
                            </div>

                            <div class="text-center text-sm text-gray-600 mb-4">
                                {{ number_format($progress, 1) }}% of budget used
                            </div>

                            <div class="text-center mb-4">
                                @if ($budget->isOverBudget())
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Over Budget
                                    </span>
                                @elseif($progress > 80)
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

                            <div class="flex space-x-2">
                                <a href="{{ route('budgets.edit', $budget) }}"
                                    class="flex-1 text-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('budgets.destroy', $budget) }}"
                                    class="flex-1 delete-form">
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Inisialisasi Flatpickr untuk filter bulan
                flatpickr("#month_history_display", { // Target input display
                    plugins: [
                        new monthSelectPlugin({
                            shorthand: true, // "Jan", "Feb", etc.
                            dateFormat: "Y-m", // Format value untuk input hidden
                            altFormat: "F Y", // Format yang dilihat user
                            theme: document.documentElement.classList.contains('dark') ? "dark" :
                                "light"
                        })
                    ],
                    altInput: true, // Wajib agar altFormat bekerja
                    maxDate: "{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}", // Batasi hingga akhir bulan lalu
                    defaultDate: "{{ $monthHistory }}", // Set nilai awal dari controller (format Y-m)

                    // Saat bulan dipilih, update input hidden
                    onChange: function(selectedDates, dateStr, instance) {
                        const monthInputHidden = document.getElementById('month_history');
                        if (selectedDates.length > 0) {
                            // Format tanggal pertama yang dipilih ke YYYY-MM
                            monthInputHidden.value = instance.formatDate(selectedDates[0], "Y-m");
                        } else {
                            monthInputHidden.value = ''; // Kosongkan jika pilihan dihapus
                        }
                    }
                });

                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault(); // Hentikan submit form asli

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this budget!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6', // Biru
                            cancelButtonColor: '#d33', // Merah
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Jika user konfirmasi, submit form
                                form.submit();
                            }
                        })
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
