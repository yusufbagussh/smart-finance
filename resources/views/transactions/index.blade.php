{{-- resources/views/transactions/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Transactions') }}
            </h2>
            <a href="{{ route('transactions.create') }}"
                class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Transaction
            </a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search
                            Description</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                            placeholder="Search transactions...">
                    </div>

                    <div>
                        <label for="type"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                        <select name="type" id="type"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200">
                            <option value="">All Types</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense
                            </option>
                            <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="category_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select name="category_id" id="category_id"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}"
                                    {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-1">
                        <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date
                            Range</label>
                        {{-- Input teks untuk Flatpickr --}}
                        <input type="text" id="date_range" name="date_range"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200"
                            placeholder="Select date range..."
                            value="{{ request('date_from') && request('date_to') ? request('date_from') . ' to ' . request('date_to') : '' }}">
                        {{-- Tampilkan range yg ada --}}

                        {{-- Input hidden untuk mengirim date_from dan date_to --}}
                        <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex space-x-2 mb-2 sm:mb-0">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('transactions.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                    </div>

                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Showing {{ $transactions->count() }} of {{ $transactions->total() }} transactions
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        @if ($transactions->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($transactions as $transaction)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        {{-- !! PERUBAHAN CONTAINER UTAMA !! --}}
                        {{-- Dibuat flex-col di mobile, sm:flex-row di layar lebih besar --}}
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">

                            {{-- Bagian Kiri: Ikon & Detail --}}
                            <div class="flex items-center min-w-0 flex-1">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-lg"
                                    style="background-color: {{ $transaction->category->color ?? ($transaction->type == 'transfer' ? '#3B82F6' : '#CCCCCC') }}20; color: {{ $transaction->category->color ?? ($transaction->type == 'transfer' ? '#3B82F6' : '#666666') }}">
                                    @if ($transaction->type == 'transfer')
                                        <i class="fas fa-retweet"></i>
                                    @else
                                        {!! $transaction->category->icon ?? '<i class="fas fa-question"></i>' !!}
                                    @endif
                                </div>

                                <div class="ml-4 min-w-0 flex-1">
                                    {{-- Deskripsi --}}
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ $transaction->description }}
                                    </p>
                                    {{-- Info Tambahan (Kategori, Tanggal, Tipe, Akun) --}}
                                    {{-- !! PERUBAHAN DI SINI: Dibuat flex wrap !! --}}
                                    <div
                                        class="flex flex-wrap items-center mt-1 space-x-3 text-xs text-gray-500 dark:text-gray-400">

                                        {{-- Kategori / Info Transfer --}}
                                        @if ($transaction->type == 'transfer')
                                            <span
                                                class="whitespace-nowrap font-semibold text-blue-600 dark:text-blue-400">
                                                {{ $transaction->sourceAccount->name ?? 'Unknown' }} <i
                                                    class="fas fa-arrow-right mx-1"></i>
                                                {{ $transaction->destinationAccount->name ?? 'Unknown' }}
                                            </span>
                                        @else
                                            <span
                                                class="whitespace-nowrap">{{ $transaction->category->name ?? 'Uncategorized' }}</span>
                                            @if ($transaction->type == 'expense' && $transaction->sourceAccount)
                                                <span class="text-gray-400">|</span>
                                                <span class="whitespace-nowrap"><i
                                                        class="fas fa-wallet mr-1"></i>{{ $transaction->sourceAccount->name }}</span>
                                            @elseif($transaction->type == 'income' && $transaction->destinationAccount)
                                                <span class="text-gray-400">|</span>
                                                <span class="whitespace-nowrap"><i
                                                        class="fas fa-wallet mr-1"></i>{{ $transaction->destinationAccount->name }}</span>
                                            @endif
                                        @endif

                                        <span class="text-gray-400 hidden sm:inline">•</span>
                                        <span class="whitespace-nowrap"><i
                                                class="far fa-calendar-alt mr-1"></i>{{ $transaction->date->format('M d, Y') }}</span>

                                        {{-- Status Badge (tetap inline-flex) --}}
                                        @php
                                            $badgeColor = match ($transaction->type) {
                                                'income'
                                                    => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                'expense'
                                                    => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                'transfer'
                                                    => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded font-medium {{ $badgeColor }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </div>
                                    {{-- !! AKHIR PERUBAHAN FLEX WRAP !! --}}
                                </div>
                            </div>

                            {{-- Bagian Kanan: Amount & Actions --}}
                            {{-- Diberi align-self-end di mobile agar ke pojok kanan bawah --}}
                            <div class="flex items-center space-x-3 sm:space-x-4 self-end sm:self-center">
                                <div class="text-right flex-shrink-0"> {{-- flex-shrink-0 agar amount tidak menyusut --}}
                                    @php
                                        $amountColor = match ($transaction->type) {
                                            'income' => 'text-green-600 dark:text-green-400',
                                            'expense' => 'text-red-600 dark:text-red-400',
                                            'transfer' => 'text-blue-600 dark:text-blue-400',
                                            default => 'text-gray-600',
                                        };
                                        $sign = match ($transaction->type) {
                                            'income' => '+',
                                            'expense' => '-',
                                            'transfer' => '',
                                            default => '',
                                        };
                                    @endphp
                                    <p class="text-base sm:text-lg font-bold {{ $amountColor }}">
                                        {{ $sign }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="flex items-center space-x-2 flex-shrink-0">
                                    <a href="{{ route('transactions.edit', $transaction) }}"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('transactions.destroy', $transaction) }}"
                                        class="inline delete-form" data-id="{{ $transaction->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                        {{-- !! AKHIR PERUBAHAN CONTAINER UTAMA !! --}}
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-receipt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No transactions found</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    @if (request()->hasAny(['search', 'type', 'category_id', 'date_from', 'date_to']))
                        Try adjusting your filters or search terms.
                    @else
                        Start tracking your finances by adding your first transaction.
                    @endif
                </p>
                <a href="{{ route('transactions.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Transaction
                </a>
            </div>
        @endif
    </div>

    {{-- !! Tambahkan Flatpickr JS & Inisialisasi Script !! --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                flatpickr("#date_range", {
                    mode: "range", // Aktifkan mode rentang
                    dateFormat: "Y-m-d", // Format database (sesuaikan jika berbeda)
                    altInput: true, // Tampilkan format yg mudah dibaca user
                    altFormat: "M d, Y", // Format tampilan (misal: Oct 25, 2025)
                    // Atur input hidden saat tanggal dipilih
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            document.getElementById('date_from').value = instance.formatDate(selectedDates[
                                0], "Y-m-d");
                            document.getElementById('date_to').value = instance.formatDate(selectedDates[1],
                                "Y-m-d");
                        } else if (selectedDates.length === 0) {
                            // Clear hidden inputs if range is cleared
                            document.getElementById('date_from').value = '';
                            document.getElementById('date_to').value = '';
                        }
                    },
                    // Atur nilai awal jika ada dari request
                    defaultDate: [
                        document.getElementById('date_from').value, // Ambil dari hidden input
                        document.getElementById('date_to').value // Ambil dari hidden input
                    ].filter(d => d) // Filter array jika salah satu kosong
                });


                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault(); // Hentikan submit form asli

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This transaction will be deleted permanently.",
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
