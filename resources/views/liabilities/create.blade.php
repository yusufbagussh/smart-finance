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
                <form method="POST" action="{{ route('liabilities.store') }}" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-3 mb-4">
                        Loan Details
                    </h3>

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
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pemberi Pinjaman
                                (Creditor)</label>
                            <input type="text" name="creditor_name" id="creditor_name"
                                value="{{ old('creditor_name') }}" required
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('creditor_name') border-red-500 @enderror"
                                placeholder="Misal: Bank Mandiri">
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
                            <input type="number" name="original_amount" id="original_amount"
                                value="{{ old('original_amount') }}" required step="0.01" min="0.01"
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('original_amount') border-red-500 @enderror"
                                placeholder="Rp 10,000,000">
                            @error('original_amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="received_account_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Akun Penerima
                                Dana</label>
                            <select name="received_account_id" id="received_account_id" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                <option value="">Pilih akun tujuan (Bank)...</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}"
                                        {{ old('received_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ Str::title($account->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('received_account_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            <i class="fas fa-plus mr-2"></i> Record Loan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
