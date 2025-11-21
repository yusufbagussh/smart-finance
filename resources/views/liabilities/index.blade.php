<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Liabilities (Debt)') }}
            </h2>
            <a href="{{ route('liabilities.create') }}"
                class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                <i class="fas fa-plus mr-2"></i>
                Record New Loan
            </a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('liabilities.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">

                    <div class="sm:col-span-3 lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Search (Loan Name or Creditor)
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="mt-1 block w-full py-2 px-3 shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                            placeholder="Cari KPR, Bank BCA, dll.">
                    </div>

                    <div>
                        <label for="status"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" id="status"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm sm:text-sm dark:text-gray-200">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                (Outstanding)</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Paid Off
                                (Lunas)</option>
                        </select>
                    </div>

                    <div>
                        <label for="creditor"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Creditor</label>
                        <select name="creditor" id="creditor"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm sm:text-sm dark:text-gray-200">
                            <option value="">All Creditors</option>
                            @foreach ($creditors as $creditor)
                                <option value="{{ $creditor }}"
                                    {{ request('creditor') === $creditor ? 'selected' : '' }}>
                                    {{ $creditor }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex space-x-2 pt-6 lg:pt-0 items-center justify-end">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('liabilities.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div
            class="bg-red-50 dark:bg-gray-800 border border-red-200 dark:border-red-800 shadow-sm sm:rounded-lg p-6 mb-6">
            <p class="text-sm font-medium text-red-600 dark:text-red-400">Total Outstanding Debt</p>
            <p class="text-3xl font-bold text-red-800 dark:text-red-300">
                Rp {{ number_format($totalLiabilities, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($liabilities as $liability)
                    @php
                        $isClosed = $liability->current_balance <= 0;
                        $statusColor = $isClosed
                            ? 'text-green-600 dark:text-green-400'
                            : 'text-red-600 dark:text-red-300';
                        $statusText = $isClosed ? 'Paid Off' : 'Active';
                    @endphp
                    <div class="p-4 sm:p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex-1 min-w-0">
                            <p class="text-base font-medium text-gray-900 dark:text-gray-100">
                                {{ $liability->name }}
                            </p>
                            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center space-x-2">
                                <span>Creditor: {{ $liability->creditor_name }}</span>
                                <span>•</span>
                                <span>Rate: {{ number_format($liability->interest_rate, 2) }}%</span>
                                <span class="hidden sm:inline">•</span>
                                <span class="hidden sm:inline {{ $statusColor }}">Status: {{ $statusText }}</span>
                            </div>
                        </div>

                        {{-- Kanan: Saldo & Aksi --}}
                        <div class="text-right flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Remaining Balance</p>
                                <p class="text-lg font-semibold {{ $statusColor }}">
                                    Rp {{ number_format($liability->current_balance, 0, ',', '.') }}
                                </p>
                            </div>

                            {{-- Tombol Aksi --}}
                            <a href="{{ route('liabilities.edit', $liability) }}"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('liabilities.destroy', $liability) }}"
                                class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800"
                                    title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        Anda tidak memiliki hutang aktif yang tercatat.
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            <div class="mt-4 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $liabilities->appends(request()->except('page'))->links() }}
            </div>

        </div>
    </div>
    {{-- (Tambahkan script SweetAlert untuk konfirmasi hapus 'delete-form') --}}
</x-app-layout>
