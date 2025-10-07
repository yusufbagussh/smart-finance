{{-- resources/views/transactions/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Transactions') }}
            </h2>
            <a href="{{ route('transactions.create') }}"
                class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Transaction
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700">Search
                            Description</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            placeholder="Search transactions...">
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense
                            </option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category_id" id="category_id"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}"
                                    {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Date Range</label>
                        <div class="mt-1 grid grid-cols-2 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="From">
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="To">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200">
                    <div class="flex space-x-2 mb-2 sm:mb-0">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('transactions.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                    </div>

                    <div class="text-sm text-gray-600">
                        Showing {{ $transactions->count() }} of {{ $transactions->total() }} transactions
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @if ($transactions->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach ($transactions as $transaction)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0 flex-1">
                                <!-- Category Icon -->
                                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-lg"
                                    style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}">
                                    {{ $transaction->category->icon }}
                                </div>

                                <!-- Transaction Details -->
                                <div class="ml-4 min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $transaction->description }}
                                            </p>
                                            <div class="flex items-center mt-1 space-x-4">
                                                <p class="text-xs text-gray-500">
                                                    {{ $transaction->category->name }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $transaction->date->format('M d, Y') }}
                                                </p>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Amount and Actions -->
                                        <div class="flex items-center space-x-4">
                                            <div class="text-right">
                                                <p
                                                    class="text-lg font-bold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $transaction->type === 'income' ? '+' : '-' }}Rp
                                                    {{ number_format($transaction->amount, 0, ',', '.') }}
                                                </p>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('transactions.edit', $transaction) }}"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('transactions.destroy', $transaction) }}"
                                                    class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-receipt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                <p class="text-gray-500 mb-6">
                    @if (request()->hasAny(['search', 'type', 'category_id', 'date_from', 'date_to']))
                        Try adjusting your filters or search terms.
                    @else
                        Start tracking your finances by adding your first transaction.
                    @endif
                </p>
                <a href="{{ route('transactions.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Transaction
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
