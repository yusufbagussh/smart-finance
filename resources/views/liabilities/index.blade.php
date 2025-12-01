<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Liabilities (Loans)') }}
            </h2>
            <a href="{{ route('liabilities.create') }}"
                class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i>
                Record New Loan
            </a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6" x-data="{ showFilters: {{ request()->hasAny(['search', 'status', 'creditor']) ? 'true' : 'false' }} }">

        <div class="p-6">
            {{-- Header Filter (Selalu Tampil) --}}
            <div class="flex items-center justify-between cursor-pointer" @click="showFilters = !showFilters">
                <div class="flex items-center">
                    <i class="fas fa-filter text-gray-500 dark:text-gray-400 mr-2"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Filter Loans
                    </h3>
                    {{-- Badge Indikator --}}
                    @if (request()->hasAny(['search', 'status', 'creditor']))
                        <span
                            class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            Active
                        </span>
                    @endif
                </div>

                {{-- Ikon Panah --}}
                <button type="button"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition-transform duration-200"
                    :class="{ 'rotate-180': showFilters }">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>

            {{-- Form Filter (Collapsible Content) --}}
            <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">

                <form method="GET" action="{{ route('liabilities.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">

                        <div class="sm:col-span-2 lg:col-span-5">
                            <label for="search"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Search loans, creditors...">
                            </div>
                        </div>

                        <div class="lg:col-span-3">
                            <label for="status"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select name="status" id="status"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Paid Off
                                </option>
                            </select>
                        </div>

                        <div class="lg:col-span-4">
                            <label for="creditor"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Creditor</label>
                            <select name="creditor" id="creditor"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200">
                                <option value="">All Creditors</option>
                                @foreach ($creditors as $creditor)
                                    <option value="{{ $creditor }}"
                                        {{ request('creditor') === $creditor ? 'selected' : '' }}>
                                        {{ $creditor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-2">
                        <a href="{{ route('liabilities.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Reset
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition-colors">
                            Apply Filter
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total
                    Debt (Payable)</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                    Rp {{ number_format($totalLiabilities, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full text-red-600 dark:text-red-400">
                <i class="fas fa-hand-holding-usd text-2xl"></i>
            </div>
        </div>

        {{-- Asumsi variabel $totalReceivables dikirim dari controller --}}
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total
                    Receivables</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                    Rp {{ number_format($totalReceivables ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600 dark:text-green-400">
                <i class="fas fa-hand-holding-heart text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($liabilities as $liability)
                @php
                    $isClosed = $liability->current_balance <= 0;
                    $isReceivable = $liability->type === 'receivable';

                    // Styling
                    $statusColor = $isClosed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                    $statusBg = $isClosed ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30';
                    $statusIcon = $isClosed ? 'fa-check-circle' : 'fa-clock';
                    $statusText = $isClosed ? 'Lunas' : 'Aktif';

                    $typeColor = $isReceivable
                        ? 'text-green-700 bg-green-50 dark:text-green-300 dark:bg-green-900/50'
                        : 'text-red-700 bg-red-50 dark:text-red-300 dark:bg-red-900/50';
                    $typeLabel = $isReceivable ? 'Piutang' : 'Hutang';
                @endphp

                <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                        {{-- Kiri: Informasi Utama --}}
                        <div class="flex items-start space-x-4">
                            {{-- Icon Besar --}}
                            <div
                                class="hidden sm:flex flex-shrink-0 w-12 h-12 rounded-full items-center justify-center {{ $isReceivable ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} dark:bg-opacity-20">
                                <i
                                    class="fas {{ $isReceivable ? 'fa-hand-holding-heart' : 'fa-hand-holding-usd' }} text-xl"></i>
                            </div>

                            <div>
                                <div class="flex items-center flex-wrap gap-2 mb-1">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ $liability->name }}
                                    </h4>
                                    <span
                                        class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wide {{ $typeColor }}">
                                        {{ $typeLabel }}
                                    </span>
                                </div>

                                {{-- PERBAIKAN: Meta Info Memanjang ke Kanan (Flex Wrap) --}}
                                <div
                                    class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-circle mr-1.5 w-4 text-center"></i>
                                        <span class="font-medium">{{ $liability->creditor_name }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-percentage mr-1.5 w-4 text-center"></i>
                                        {{ number_format($liability->interest_rate, 2) }}%
                                    </div>
                                    <div class="flex items-center" title="Tenor">
                                        <i class="fas fa-hourglass-half mr-1.5 w-4 text-center"></i>
                                        {{ $liability->tenor_months }} Bln
                                    </div>
                                    @if ($liability->due_date)
                                        <div
                                            class="flex items-center {{ $liability->due_date < now() && !$isClosed ? 'text-red-600 font-bold' : '' }}">
                                            <i class="far fa-calendar-alt mr-1.5 w-4 text-center"></i>
                                            Due: {{ $liability->due_date->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Kanan: Saldo & Actions --}}
                        <div
                            class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center gap-y-2 w-full md:w-auto border-t md:border-t-0 border-gray-100 dark:border-gray-700 pt-3 md:pt-0">

                            <div class="text-left md:text-right">
                                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Sisa
                                    Tagihan</span>
                                <div class="text-xl font-bold {{ $statusColor }}">
                                    Rp {{ number_format($liability->current_balance, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                {{-- Status Badge --}}
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold flex items-center {{ $statusBg }} {{ $statusColor }}">
                                    <i class="fas {{ $statusIcon }} mr-1"></i> {{ $statusText }}
                                </span>

                                {{-- Action Buttons --}}
                                <div class="h-4 w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>

                                <a href="{{ route('liabilities.edit', $liability) }}"
                                    class="p-1.5 text-blue-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                    title="Edit Details">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>

                                <form method="POST" action="{{ route('liabilities.destroy', $liability) }}"
                                    class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 text-red-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                        title="Delete (Revert)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="p-12 text-center flex flex-col items-center justify-center">
                    <div
                        class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <i class="fas fa-file-invoice-dollar text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No loans found</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400 max-w-xs mx-auto">
                        Mulai catat hutang atau piutang Anda untuk memantau kewajiban finansial dengan lebih
                        baik.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('liabilities.create') }}"
                            class="inline-flex items-center px-5 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
                            <i class="fas fa-plus mr-2"></i> Record New Loan
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $liabilities->withQueryString()->links() }}
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
                            title: 'Hapus Data Pinjaman?',
                            html: "Peringatan: Ini akan <strong>MEMBATALKAN (REVERT)</strong> semua transaksi terkait!<br><br>Saldo akun Anda akan dikembalikan ke kondisi sebelum pinjaman/pembayaran terjadi.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus & Revert!',
                            cancelButtonText: 'Batal'
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
