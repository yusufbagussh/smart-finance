<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('accounts.index') }}" class="mr-4 text-gray-600 dark:text-gray-400"><i
                    class="fas fa-arrow-left"></i></a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Account') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('accounts.store') }}" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            Akun</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('name') border-red-500 @enderror"
                            placeholder="Misal: BCA, Dompet Tunai, OVO">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe
                            Akun</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash (Uang Tunai)
                            </option>
                            <option value="bank_account" {{ old('type') == 'bank_account' ? 'selected' : '' }}>Bank
                                Account (Rekening Bank)</option>
                            <option value="e_wallet" {{ old('type') == 'e_wallet' ? 'selected' : '' }}>E-Wallet (OVO,
                                Gopay, dll)</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other (Lainnya)
                            </option>
                        </select>
                    </div>

                    <div x-data="currencyInput('{{ old('initial_balance', 0) }}')">
                        <label for="initial_balance_display"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Saldo Awal (Initial Balance)
                        </label>

                        <div class="relative mt-1">
                            <span
                                class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 font-bold">Rp</span>

                            {{-- 1. Input Tampilan --}}
                            <input type="text" id="initial_balance_display" inputmode="numeric" {{-- <<-- INI KUNCINYA --}}
                                x-model="formattedValue" @input="handleInput"
                                class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 font-mono font-semibold"
                                placeholder="0.00">

                            {{-- 2. Input Asli (Hidden) --}}
                            {{-- PERBAIKAN: Ubah name="amount" menjadi "initial_balance" agar terbaca controller --}}
                            <input type="hidden" name="initial_balance" :value="rawValue">
                        </div>

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Masukkan saldo Anda saat ini di akun ini. Transaksi "Saldo Awal" akan otomatis dibuat.
                        </p>

                        @error('initial_balance')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- (Opsional: Tambahkan input untuk icon & color picker) --}}

                    <div
                        class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700 space-x-3">
                        <a href="{{ route('accounts.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase hover:bg-gray-300">Cancel</a>
                        <button type_exists("submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i> Save Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
