<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Accounts') }}
            </h2>
            <a href="{{ route('accounts.create') }}"
                class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add New Account
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6" x-data="{ showFilters: {{ request()->hasAny(['search', 'type']) ? 'true' : 'false' }} }">

                <div class="p-6">
                    {{-- Header Filter --}}
                    <div class="flex items-center justify-between cursor-pointer" @click="showFilters = !showFilters">
                        <div class="flex items-center">
                            <i class="fas fa-filter text-gray-500 dark:text-gray-400 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Filter Accounts
                            </h3>
                            @if (request()->hasAny(['search', 'type']))
                                <span
                                    class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Active
                                </span>
                            @endif
                        </div>
                        <button type="button"
                            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition-transform duration-200"
                            :class="{ 'rotate-180': showFilters }">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    {{-- Form Filter --}}
                    <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">

                        <form method="GET" action="{{ route('accounts.index') }}" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="sm:col-span-2 lg:col-span-1">
                                    <label for="search"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search
                                        Name</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                        <input type="text" name="search" id="search"
                                            value="{{ request('search') }}"
                                            class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="BCA, OVO...">
                                    </div>
                                </div>

                                <div>
                                    <label for="type"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account
                                        Type</label>
                                    <select name="type" id="type"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200">
                                        <option value="">All Types</option>
                                        <option value="cash" {{ request('type') === 'cash' ? 'selected' : '' }}>Cash
                                        </option>
                                        <option value="bank_account"
                                            {{ request('type') === 'bank_account' ? 'selected' : '' }}>Bank Account
                                        </option>
                                        <option value="e_wallet" {{ request('type') === 'e_wallet' ? 'selected' : '' }}>
                                            E-Wallet</option>
                                        <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                </div>

                                <div class="flex items-end space-x-2">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-indigo-700 focus:bg-indigo-700 transition-colors h-[38px]">
                                        Apply
                                    </button>
                                    <a href="{{ route('accounts.index') }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors h-[38px]">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 border-l-4 border-blue-500 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Balance (Visible Accounts)</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                        Rp {{ number_format($totalBalance, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full text-blue-600 dark:text-blue-400">
                    <i class="fas fa-coins text-2xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($accounts as $account)
                        <div
                            class="p-4 sm:p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-xl"
                                    style="background-color: {{ $account->color ?? '#6B7280' }}20; color: {{ $account->color ?? '#6B7280' }};">
                                    <i class="{{ $account->icon ?? 'fas fa-wallet' }}"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $account->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ Str::title(str_replace('_', ' ', $account->type)) }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($account->current_balance, 0, ',', '.') }}
                                </p>
                                <div class="flex items-center justify-end space-x-3 mt-2">
                                    <a href="{{ route('accounts.edit', $account) }}"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium flex items-center"
                                        title="Edit">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <form method="POST" action="{{ route('accounts.destroy', $account) }}"
                                        class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-medium flex items-center"
                                            title="Delete">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                                <i class="fas fa-wallet text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No accounts found</h3>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">Try adjusting your filters or add a new
                                account.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Deleting this account will also delete ALL associated transactions history.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
