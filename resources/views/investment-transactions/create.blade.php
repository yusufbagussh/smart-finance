<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            {{-- Tombol kembali bisa diarahkan ke dashboard utama atau halaman portofolio --}}
            <a href="{{ url()->previous(route('dashboard')) }}"
                class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add Investment Transaction') }}
            </h2>
        </div>
    </x-slot>

    {{-- Kita butuh Flatpickr untuk tanggal --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            input[readonly].flatpickr-input {
                background-color: white !important;
            }

            .dark input[readonly].flatpickr-input {
                background-color: #374151 !important;
                /* bg-gray-700 */
            }

            input#transaction_date {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%239ca3af' class='w-6 h-6'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0h18' /%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.75rem center;
                background-size: 1.25rem;
                padding-right: 2.5rem;
            }
        </style>
    @endpush

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8">

                    {{-- Set Alpine.js untuk logic Beli/Jual --}}
                    <form method="POST" action="{{ route('investment-transactions.store') }}" x-data="{ transactionType: '{{ old('transaction_type', 'buy') }}' }">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Transaction Type
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="transaction_type" value="buy"
                                            x-model="transactionType" class="sr-only">
                                        <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                            :class="transactionType === 'buy' ?
                                                'border-green-500 bg-green-50 text-green-700 dark:bg-green-900 dark:text-green-300' :
                                                'border-gray-300 dark:border-gray-600 hover:border-green-300'">
                                            <i class="fas fa-arrow-down text-2xl mb-2 text-green-600"></i>
                                            <p class="font-semibold">Buy</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="transaction_type" value="sell"
                                            x-model="transactionType" class="sr-only">
                                        <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                            :class="transactionType === 'sell' ?
                                                'border-red-500 bg-red-50 text-red-700 dark:bg-red-900 dark:text-red-300' :
                                                'border-gray-300 dark:border-gray-600 hover:border-red-300'">
                                            <i class="fas fa-arrow-up text-2xl mb-2 text-red-600"></i>
                                            <p class="font-semibold">Sell</p>
                                        </div>
                                    </label>
                                </div>
                                @error('transaction_type')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-show="transactionType === 'buy'" x-transition>
                                <label for="source_account_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Source Account (Sumber Dana)
                                </label>
                                <select name="source_account_id" id="source_account_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm sm:text-sm dark:text-gray-200">
                                    <option value="">Pilih akun pembayaran...</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ old('source_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} (Rp
                                            {{ number_format($account->current_balance, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Saldo akun ini akan berkurang.
                                </p>
                                @error('source_account_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-show="transactionType === 'sell'" x-transition>
                                <label for="destination_account_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Destination Account (Tujuan Dana)
                                </label>
                                <select name="destination_account_id" id="destination_account_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm sm:text-sm dark:text-gray-200">
                                    <option value="">Pilih akun penerima...</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ old('destination_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} (Rp
                                            {{ number_format($account->current_balance, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hasil penjualan akan masuk ke
                                    akun
                                    ini.</p>
                                @error('destination_account_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label for="portfolio_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Portfolio
                                </label>
                                <select name="portfolio_id" id="portfolio_id" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a portfolio</option>
                                    @foreach ($portfolios as $portfolio)
                                        <option value="{{ $portfolio->id }}"
                                            {{ old('portfolio_id') == $portfolio->id ? 'selected' : '' }}>
                                            {{ $portfolio->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('portfolio_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="asset_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Asset
                                </label>
                                <select name="asset_id" id="asset_id" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select an asset</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}"
                                            {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->name }} ({{ $asset->code }}
                                            - Rp{{ number_format($asset->current_price, 2, ',', '.') }}
                                            -
                                            {{ $asset->price_last_updated_at ? $asset->price_last_updated_at->format('M d, H:i') : 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="transaction_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Date
                                </label>
                                <input type="text" name="transaction_date" id="transaction_date"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md flatpickr-input dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                    placeholder="Select date..." value="{{ old('transaction_date', date('Y-m-d')) }}"
                                    required>
                                @error('transaction_date')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="quantity"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Quantity
                                    </label>
                                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}"
                                        step="0.00000001" min="0"
                                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                        placeholder="e.g., 10 (untuk gram/unit)" required>
                                    @error('quantity')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div x-data="currencyInput('{{ old('price_per_unit', 0) }}')">
                                    <label for="price_per_unit"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Price (per Unit/Gram)
                                    </label>
                                    <div class="relative mt-1">
                                        <span
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">Rp</span>
                                        <input type="text" id="price_per_unit_model" x-model="formattedValue"
                                            @input="handleInput"
                                            class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                            placeholder="100.000">
                                        <input type="hidden" name="price_per_unit" :value="rawValue">
                                        {{-- <input type="number" name="price_per_unit" id="price_per_unit"
                                            value="{{ old('price_per_unit') }}" step="0.01" min="0"
                                            class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                            placeholder="0.00" required> --}}
                                    </div>
                                    @error('price_per_unit')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div x-data="currencyInput('{{ old('fees', 0) }}')">
                                <label for="fees"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Transaction Fee (Optional)
                                </label>
                                <div class="relative mt-1">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">Rp</span>
                                    <input type="text" id="fees_model" x-model="formattedValue"
                                        @input="handleInput"
                                        class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                        placeholder="100.000">
                                    <input type="hidden" name="fees" :value="rawValue">
                                    {{-- <input type="number" name="fees" id="fees"
                                        value="{{ old('fees', 0) }}" step="0.01" min="0"
                                        class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                        placeholder="0.00"> --}}
                                </div>
                                @error('fees')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div
                                class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700 space-x-3">
                                <a href="{{ url()->previous(route('dashboard')) }}"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150"
                                    :class="transactionType === 'buy' ?
                                        'bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900' :
                                        'bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-900'">
                                    <i class="fas fa-save mr-2"></i>
                                    <span x-text="transactionType === 'buy' ? 'Record Buy' : 'Record Sell'"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                flatpickr("#transaction_date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "F j, Y",
                    defaultDate: "{{ old('transaction_date', date('Y-m-d')) }}",
                    maxDate: "today" // Tidak bisa mencatat transaksi di masa depan
                });
            });
        </script>
    @endpush
</x-app-layout>
