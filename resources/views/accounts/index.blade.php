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

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Balance (All Accounts)</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            Rp {{ number_format($totalBalance, 0, ',', '.') }}
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($accounts as $account)
                <div class="p-4 sm:p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg"
                            style="background-color: {{ $account->color ?? '#6B7280' }}20; color: {{ $account->color ?? '#6B7280' }};">
                            <i class="{{ $account->icon ?? 'fas fa-wallet' }}"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900 dark:text-gray-100">
                                {{ $account->name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ Str::title(str_replace('_', ' ', $account->type)) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 sm:space-x-4 self-end sm:self-center">
                        <div class="text-right flex-shrink-0"> {{-- flex-shrink-0 agar amount tidak menyusut --}}
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Rp {{ number_format($account->current_balance, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <a href="{{ route('accounts.edit', $account) }}"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('accounts.destroy', $account) }}"
                                class="inline delete-form" data-id="{{ $account->id }}">
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
            @empty
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    Anda belum memiliki akun. Silakan buat akun pertama Anda.
                </div>
            @endforelse
        </div>
    </div>

    {{-- (Tambahkan script SweetAlert untuk konfirmasi hapus 'delete-form') --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone.",
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
