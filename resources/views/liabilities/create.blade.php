<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('liabilities.index') }}" class="mr-4 text-gray-600 dark:text-gray-400"><i
                    class="fas fa-arrow-left"></i></a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Record New Liability (Loan)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                {{-- Initialize Alpine x-data here --}}
                <form method="POST" action="{{ route('liabilities.store') }}" class="p-6 sm:p-8 space-y-6"
                    x-data="{ type: '{{ old('type', 'payable') }}' }">
                    @csrf

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-3 mb-4">
                        Loan Details
                    </h3>

                    {{-- 1. TIPE TRANSAKSI (PENTING: INI HARUS ADA) --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Jenis
                            Transaksi</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="payable" x-model="type" class="sr-only">
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="type === 'payable' ?
                                        'border-red-500 bg-red-50 text-red-700 dark:bg-red-900 dark:text-red-200' :
                                        'border-gray-300 dark:border-gray-600'">
                                    <i class="fas fa-hand-holding-usd text-2xl mb-2"></i>
                                    <p class="font-semibold">Saya Berhutang</p>
                                    <span class="text-xs">(Kewajiban)</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="receivable" x-model="type" class="sr-only">
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="type === 'receivable' ?
                                        'border-green-500 bg-green-50 text-green-700 dark:bg-green-900 dark:text-green-200' :
                                        'border-gray-300 dark:border-gray-600'">
                                    <i class="fas fa-hand-holding-heart text-2xl mb-2"></i>
                                    <p class="font-semibold">Saya Memberi Pinjaman</p>
                                    <span class="text-xs">(Aset)</span>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pinjaman</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('name') border-red-500 @enderror"
                                placeholder="Misal: KPR BCA">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="creditor_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span
                                    x-text="type === 'payable' ? 'Pemberi Pinjaman (Creditor)' : 'Peminjam (Debtor)'"></span>
                            </label>
                            <input type="text" name="creditor_name" id="creditor_name"
                                value="{{ old('creditor_name') }}" required
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('creditor_name') border-red-500 @enderror"
                                placeholder="Nama Bank / Orang">
                            @error('creditor_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="original_amount"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Pinjaman
                                Awal</label>
                            <div class="relative mt-1">
                                <span
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">Rp</span>
                                <input type="number" name="original_amount" id="original_amount"
                                    value="{{ old('original_amount') }}" required step="0.01" min="0.01"
                                    class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('original_amount') border-red-500 @enderror"
                                    placeholder="10.000.000">
                            </div>
                            @error('original_amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span
                                    x-text="type === 'payable' ? 'Akun Penerima Dana (Masuk ke...)' : 'Akun Sumber Dana (Keluar dari...)'"></span>
                            </label>
                            <select name="account_id" id="account_id" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">-- Pilih Akun --</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}"
                                        {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ Str::title($account->type) }}) - Rp
                                        {{ number_format($account->current_balance, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span x-show="type === 'payable'">Dana pinjaman akan <strong>ditambahkan</strong> ke
                                    saldo akun ini.</span>
                                <span x-show="type === 'receivable'" style="display: none;">Dana pinjaman akan
                                    <strong>diambil</strong> dari saldo akun ini.</span>
                            </p>
                            @error('account_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tenor_months"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tenor (Bulan)</label>
                            <input type="number" name="tenor_months" id="tenor_months"
                                value="{{ old('tenor_months') }}" min="1"
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('tenor_months') border-red-500 @enderror"
                                placeholder="Misal: 60">
                            @error('tenor_months')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="interest_rate"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Suku Bunga
                                (%)</label>
                            <input type="number" name="interest_rate" id="interest_rate"
                                value="{{ old('interest_rate') }}" step="0.01" min="0"
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('interest_rate') border-red-500 @enderror"
                                placeholder="Misal: 5.50">
                            @error('interest_rate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="start_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Terima
                                Dana</label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ old('start_date', date('Y-m-d')) }}" required
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="due_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Jatuh Tempo
                                (Akhir)</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('due_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsional. Kosongkan jika tidak ada
                                tanggal pasti.</p>
                            @error('due_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div
                        class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700 space-x-3">
                        <a href="{{ route('liabilities.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase hover:bg-gray-300">Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
