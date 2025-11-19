<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('accounts.index') }}" class="mr-4 text-gray-600 dark:text-gray-400"><i
                    class="fas fa-arrow-left"></i></a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Account') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('accounts.update', $account) }}" class="p-6 sm:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            Akun</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $account->name) }}"
                            required
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe
                            Akun</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            <option value="cash" {{ old('type', $account->type) == 'cash' ? 'selected' : '' }}>Cash
                                (Uang Tunai)</option>
                            <option value="bank_account"
                                {{ old('type', $account->type) == 'bank_account' ? 'selected' : '' }}>Bank Account
                                (Rekening Bank)</option>
                            <option value="e_wallet" {{ old('type', $account->type) == 'e_wallet' ? 'selected' : '' }}>
                                E-Wallet (OVO, Gopay, dll)</option>
                            <option value="other" {{ old('type', $account->type) == 'other' ? 'selected' : '' }}>Other
                                (Lainnya)</option>
                        </select>
                    </div>

                    <div>
                        <label for="current_balance"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Saldo Saat Ini (Current
                            Balance)</label>
                        <div class="relative mt-1">
                            <span
                                class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">Rp</span>
                            <input type="text" name="current_balance" id="current_balance"
                                value="{{ number_format($account->current_balance, 0, ',', '.') }}" disabled readonly
                                class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 cursor-not-allowed">
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Saldo hanya dapat diubah melalui
                            pencatatan transaksi (Income, Expense, atau Transfer).</p>
                    </div>

                    <div
                        class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700 space-x-3">
                        <a href="{{ route('accounts.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase hover:bg-gray-300">Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i> Update Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
