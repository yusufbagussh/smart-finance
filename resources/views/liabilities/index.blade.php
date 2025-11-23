<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Liabilities (Loans)') }}
            </h2>
            <a href="{{ route('liabilities.create') }}"
                class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Record New Loan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('liabilities.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                            <div class="lg:col-span-2">
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Search Description
                                </label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                                    placeholder="Search loans, creditors...">
                            </div>

                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select name="status" id="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200">
                                    <option value="">All Statuses</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                        (Outstanding)</option>
                                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Paid
                                        Off (Lunas)</option>
                                </select>
                            </div>

                            <div>
                                <label for="creditor"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Creditor</label>
                                <select name="creditor" id="creditor"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200">
                                    <option value="">All Creditors</option>
                                    @foreach ($creditors as $creditor)
                                        <option value="{{ $creditor }}"
                                            {{ request('creditor') === $creditor ? 'selected' : '' }}>
                                            {{ $creditor }}</option>
                                    @endforeach
                                </select>
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
                                <a href="{{ route('liabilities.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                                    <i class="fas fa-times mr-2"></i>
                                    Clear
                                </a>
                            </div>

                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Showing {{ $liabilities->count() }} of {{ $liabilities->total() }} loans
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div
                    class="bg-red-50 dark:bg-gray-800 border border-red-200 dark:border-red-900/50 shadow-sm rounded-lg p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600 dark:text-red-400">Total Debt (Payable)</p>
                        <p class="text-2xl font-bold text-red-800 dark:text-red-300">
                            Rp {{ number_format($totalLiabilities, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full text-red-600 dark:text-red-400">
                        <i class="fas fa-hand-holding-usd text-xl"></i>
                    </div>
                </div>
                {{-- (Opsional: Anda bisa menambahkan card Total Piutang di sini jika sudah menerapkan fitur Receivable) --}}
                <div
                    class="bg-green-50 dark:bg-gray-800 border border-green-200 dark:border-green-900/50 shadow-sm rounded-lg p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Total Receivable</p>
                        <p class="text-2xl font-bold text-green-800 dark:text-green-300">
                            Rp {{ number_format($totalReceivables, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600 dark:text-green-400">
                        <i class="fas fa-hand-holding-heart text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($liabilities as $liability)
                        @php
                            $isClosed = $liability->current_balance <= 0;
                            $isReceivable = $liability->type === 'receivable';

                            // Warna Status
                            $statusColor = $isClosed
                                ? 'text-green-600 dark:text-green-400'
                                : 'text-red-600 dark:text-red-400';
                            $statusBg = $isClosed
                                ? 'bg-green-100 dark:bg-green-900/30'
                                : 'bg-red-100 dark:bg-red-900/30';
                            $statusIcon = $isClosed ? 'fa-check-circle' : 'fa-clock';
                            $statusText = $isClosed ? 'Paid Off' : 'Active';

                            // Warna Tipe
                            $typeBadgeColor = $isReceivable
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                            $typeLabel = $isReceivable ? 'Piutang' : 'Hutang';
                        @endphp

                        <div class="p-4 sm:p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-between space-y-4 sm:space-y-0">

                                {{-- Kiri: Detail Utama --}}
                                <div class="flex items-start space-x-4">
                                    <div
                                        class="hidden sm:flex flex-shrink-0 w-10 h-10 rounded-full items-center justify-center {{ $typeBadgeColor }}">
                                        <i
                                            class="fas {{ $isReceivable ? 'fa-hand-holding-heart' : 'fa-hand-holding-usd' }}"></i>
                                    </div>

                                    <div>
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $typeBadgeColor }}">
                                                {{ $typeLabel }}
                                            </span>
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $liability->name }}
                                            </h4>
                                        </div>

                                        <div
                                            class="text-sm text-gray-500 dark:text-gray-400 space-y-1 sm:space-y-0 sm:space-x-3 flex flex-col sm:flex-row">
                                            <span class="flex items-center">
                                                <i class="fas fa-user-circle mr-1.5 opacity-70"></i>
                                                {{ $liability->creditor_name }}
                                            </span>
                                            <span class="hidden sm:inline text-gray-300 dark:text-gray-600">|</span>
                                            <span class="flex items-center">
                                                <i class="fas fa-percentage mr-1.5 opacity-70"></i>
                                                {{ number_format($liability->interest_rate, 2) }}%
                                            </span>
                                            @if ($liability->due_date)
                                                <span class="hidden sm:inline text-gray-300 dark:text-gray-600">|</span>
                                                <span class="flex items-center">
                                                    <i class="far fa-calendar-alt mr-1.5 opacity-70"></i> Due:
                                                    {{ $liability->due_date->format('M d, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Kanan: Saldo & Status --}}
                                <div
                                    class="flex items-center justify-between sm:justify-end sm:space-x-6 w-full sm:w-auto border-t sm:border-t-0 border-gray-100 dark:border-gray-700 pt-3 sm:pt-0">

                                    <div class="text-left sm:text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Remaining</p>
                                        <p class="text-lg font-bold {{ $statusColor }}">
                                            Rp {{ number_format($liability->current_balance, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="px-2 py-1 rounded text-xs font-medium flex items-center {{ $statusBg }} {{ $statusColor }}">
                                            <i class="fas {{ $statusIcon }} mr-1"></i> {{ $statusText }}
                                        </div>
                                        <a href="{{ route('liabilities.edit', $liability) }}"
                                            class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                            title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form method="POST" action="{{ route('liabilities.destroy', $liability) }}"
                                            class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                                title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                                <i class="fas fa-file-invoice-dollar text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No loans found</h3>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">Get started by recording a new loan or
                                debt.</p>
                            <div class="mt-6">
                                <a href="{{ route('liabilities.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none">
                                    <i class="fas fa-plus mr-2"></i> Record New Loan
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- 4. Pagination (Penting!) --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{-- withQueryString() memastikan filter search/status tetap ada saat pindah halaman --}}
                    {{ $liabilities->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert Script --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Delete Loan?',
                            text: "Warning: This will also REVERT all associated transactions (initial income & repayments) from your account balance!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete & revert!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        })
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
