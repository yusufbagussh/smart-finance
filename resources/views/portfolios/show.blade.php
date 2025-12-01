<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            {{-- Bagian Kiri: Judul dan Tombol Kembali --}}
            <div class="flex items-center">
                <a href="{{ route('portfolios.index') }}"
                    class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                    title="Back to Portfolios">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ $portfolio->name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $portfolio->description ?? 'Portfolio Summary' }}
                    </p>
                </div>
            </div>

            {{-- Bagian Kanan: Tombol Aksi --}}
            <a href="{{ route('investment-transactions.create') }}?portfolio_id={{ $portfolio->id }}"
                class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Transaction
            </a>
        </div>
    </x-slot>

    {{-- Session Success Message --}}
    {{-- @if (session('success'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-600 dark:text-green-300"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif --}}

    {{-- 1. HASIL ANALISIS AI (Sudah ada) --}}
    <div x-data="{ open: false }"
        class="mb-6 bg-blue-50 dark:bg-gray-800 border border-blue-200 dark:border-gray-700 shadow-sm rounded-lg">

        {{-- Header (Tombol Toggle) --}}
        <div @click="open = !open" class="p-5 flex justify-between items-center cursor-pointer">
            <div class="flex items-start space-x-4">
                <div
                    class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        AI Portfolio Analysis
                    </h3>
                </div>
            </div>
            {{-- Ikon Chevron --}}
            <div class="text-gray-500 dark:text-gray-400">
                <i class="fas fa-chevron-down transform transition-transform" :class="{ 'rotate-180': !open }"></i>
            </div>
        </div>

        {{-- Konten Collapsible --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-300"
            x-transition:leave="transition ease-in duration-200" class="px-5 pb-5">

            {{--
                      !! PERBAIKAN FINAL (DARI ANDA) !!
                      Menggunakan class prose (untuk format markdown)
                      DAN class warna teks eksplisit (untuk light/dark mode).
                    --}}
            <div class="prose prose-sm text-gray-900 dark:text-gray-100 max-w-none">
                {!! Str::markdown($geminiAnalysisText) !!}
            </div>
        </div>
    </div>

    {{-- !! KARTU BARU: PROFIL PORTOFOLIO !! --}}
    <div class="mb-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="p-4 sm:p-6 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <h5 class="text-lg font-medium text-gray-900 dark:text-white">
                <i class="fas fa-user-shield mr-2 text-gray-500 dark:text-gray-400"></i>
                My Portfolio Profile
            </h5>
            <a href="{{ route('portfolios.edit', $portfolio) }}"
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
        </div>
        {{-- Gunakan Description List (dl) untuk data profil --}}
        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Profil Risiko</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2 capitalize">
                    {{ $portfolio->risk_profile ?? 'Belum diatur' }}
                </dd>
            </div>
            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tujuan Utama</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                    {{ $portfolio->goal ?? 'Belum diatur' }}
                </dd>
            </div>
            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Horizon Waktu</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                    {{ $portfolio->time_horizon ? $portfolio->time_horizon . ' tahun' : 'Belum diatur' }}
                </dd>
            </div>
            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rencana ke Depan</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                    {{ $portfolio->future_plans ?? 'Belum diatur' }}
                </dd>
            </div>
        </dl>
    </div>


    {{-- 2. Ringkasan Total Portofolio (PERBAIKI VARIABEL) --}}
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
            Portfolio Overview
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Total Nilai Saat Ini --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Value</p>
                        {{-- Ganti variabel totals --}}
                        <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($totals['total_value'], 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            {{-- Total Modal (Cost Basis) --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cost</p>
                        {{-- Ganti variabel totals --}}
                        <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($totals['total_cost_basis'], 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            {{-- Total Keuntungan/Kerugian (Unrealized) --}}
            @php
                // Ganti variabel totals
                $pnl = $totals['total_unrealized_pnl'];
                $pnlColorClass =
                    $pnl > 0
                        ? 'text-green-600 dark:text-green-400'
                        : ($pnl < 0
                            ? 'text-red-600 dark:text-red-400'
                            : 'text-gray-900 dark:text-gray-100');
                $pnlIcon = $pnl > 0 ? 'fa-arrow-up' : ($pnl < 0 ? 'fa-arrow-down' : 'fa-equals');
            @endphp
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                {{-- ... (isinya tetap sama) ... --}}
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
            {{ $pnl > 0 ? 'bg-green-100 dark:bg-green-900' : ($pnl < 0 ? 'bg-red-100 dark:bg-red-900' : 'bg-gray-100 dark:bg-gray-700') }}">
                        <i class="fas {{ $pnlIcon }} {{ $pnlColorClass }}"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unrealized P&L</p>
                        <p class="text-xl font-semibold {{ $pnlColorClass }}">
                            {{ $pnl >= 0 ? '+' : '' }}Rp {{ number_format($pnl, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Daftar Aset yang Dimiliki (Holdings) --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <h5 class="text-lg font-medium text-gray-900 dark:text-white">
                My Holdings
            </h5>
        </div>

        @if (count($summary) > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($summary as $asset)
                    {{-- Kita hanya tampilkan aset yang masih dimiliki --}}
                    @if ($asset['total_quantity'] > 0)
                        @php
                            $assetPnl = $asset['unrealized_pnl'];
                            $assetPnlColor =
                                $assetPnl > 0
                                    ? 'text-green-600 dark:text-green-400'
                                    : ($assetPnl < 0
                                        ? 'text-red-600 dark:text-red-400'
                                        : 'text-gray-500 dark:text-gray-400');
                        @endphp
                        <div class="p-4 sm:p-6 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">

                                {{-- Kiri: Nama Aset & Kuantitas --}}
                                <div class="flex items-center min-w-0 flex-1">
                                    <div
                                        class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-lg {{ $asset['asset_code'] === 'ANTM' ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300' }}">
                                        <i
                                            class="fas {{ $asset['asset_code'] === 'ANTM' ? 'fa-gem' : 'fa-file-invoice-dollar' }}"></i>
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <p class="text-base font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $asset['asset_name'] }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ number_format($asset['total_quantity'], 4, ',', '.') }}
                                            {{ $asset['price_unit'] }}s
                                            @ Rp {{ number_format($asset['average_buy_price'], 2, ',', '.') }}
                                            (Avg)
                                        </p>
                                    </div>
                                </div>

                                {{-- Kanan: Nilai & P&L --}}
                                <div class="flex items-center space-x-4 self-end sm:self-center">
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">
                                            Rp {{ number_format($asset['current_value'], 2, ',', '.') }}
                                        </p>
                                        <p class="text-sm {{ $assetPnlColor }}">
                                            {{ $assetPnl >= 0 ? '+' : '' }}Rp
                                            {{ number_format($assetPnl, 2, ',', '.') }}
                                        </p>
                                    </div>
                                    {{-- Tambahkan tombol aksi per aset jika perlu (misal: 'Sell') --}}
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            {{-- Empty State untuk Holdings --}}
            <div class="text-center py-12 px-6">
                <i class="fas fa-search-dollar text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    No holdings yet
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Start by adding your first 'Buy' transaction for this portfolio.
                </p>
                <a href="{{ route('investment-transactions.create') }}?portfolio_id={{ $portfolio->id }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Transaction
                </a>
            </div>
        @endif
    </div>

    {{-- TODO: Tampilkan juga Riwayat Transaksi (investment_transactions) untuk portofolio ini --}}
    <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <h5 class="text-lg font-medium text-gray-900 dark:text-white">
                Transaction History
            </h5>
        </div>

        @if ($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Type</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Asset</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Quantity</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total Amount</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($transactions as $tx)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $tx->transaction_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($tx->transaction_type === 'buy')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            BUY
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            SELL
                                        </span>
                                    @endif
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $tx->asset->name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                    {{ number_format($tx->quantity, 4, ',', '.') }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-3">
                                    {{-- Tombol Ubah (Edit) --}}
                                    <a href="{{ route('investment-transactions.edit', $tx) }}"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- Tombol Hapus (Delete) --}}
                                    <form method="POST" action="{{ route('investment-transactions.destroy', $tx) }}"
                                        class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                No transaction history for this portfolio yet.
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault(); // Hentikan submit
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This will also delete the linked transaction from your main history!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33', // Merah
                            cancelButtonColor: '#3085d6', // Biru
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // Lanjutkan submit
                            }
                        })
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
