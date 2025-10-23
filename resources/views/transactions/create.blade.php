{{-- resources/views/transactions/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('transactions.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Transaction') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('transactions.store') }}" x-data="transactionForm()">
                    @csrf

                    <!-- Transaction Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Transaction Type</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="income" x-model="type"
                                    class="sr-only" {{ old('type') === 'income' ? 'checked' : '' }}>
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="type === 'income' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-300 hover:border-green-300'">
                                    <i class="fas fa-arrow-up text-2xl mb-2 text-green-600"></i>
                                    <p class="font-semibold">Income</p>
                                    <p class="text-xs text-gray-500">Money received</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" x-model="type"
                                    class="sr-only" {{ old('type', 'expense') === 'expense' ? 'checked' : '' }}>
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="type === 'expense' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-300 hover:border-red-300'">
                                    <i class="fas fa-arrow-down text-2xl mb-2 text-red-600"></i>
                                    <p class="font-semibold">Expense</p>
                                    <p class="text-xs text-gray-500">Money spent</p>
                                </div>
                            </label>
                        </div>
                        @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (IDR)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0.01"
                                class="pl-10 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="0.00" required>
                        </div>
                        @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-6">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" id="category_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="mb-6">
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description with AI Classification -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <div class="relative">
                            <textarea name="description" id="description" rows="3" x-model="description" @input="debounceClassify()"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="Describe your transaction..." required>{{ old('description') }}</textarea>
                        </div>

                        <!-- AI Suggestion -->
                        <div x-show="suggestion.category_name" x-transition class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex items-center">
                                <i class="fas fa-robot text-blue-600 mr-2"></i>
                                <span class="text-sm text-blue-800">
                                    AI suggests: <strong x-text="suggestion.category_name"></strong>
                                    (<span x-text="Math.round(suggestion.confidence * 100)"></span>% confidence)
                                </span>
                                <button type="button" @click="applySuggestion()"
                                    class="ml-auto text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                    Apply
                                </button>
                            </div>
                            <p class="text-xs text-blue-600 mt-1" x-text="suggestion.explanation"></p>
                        </div>

                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('transactions.index') }}" class="mb-3 sm:mb-0 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 transition ease-in-out duration-150">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-save mr-2"></i>
                            Save Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function transactionForm() {
            return {
                type: '{{ old("type", "expense") }}',
                description: '{{ old("description") }}',
                suggestion: {},
                classifyTimeout: null,

                debounceClassify() {
                    clearTimeout(this.classifyTimeout);
                    this.classifyTimeout = setTimeout(() => {
                        if (this.description.length > 3) {
                            this.classifyTransaction();
                        }
                    }, 500);
                },

                async classifyTransaction() {
                    try {
                        const response = await fetch('{{ route("ml.classify") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                description: this.description
                            })
                        });

                        if (response.ok) {
                            this.suggestion = await response.json();
                        }
                    } catch (error) {
                        console.log('Classification failed:', error);
                    }
                },

                applySuggestion() {
                    if (this.suggestion.suggested_category) {
                        document.getElementById('category_id').value = this.suggestion.suggested_category;
                    }
                    this.suggestion = {};
                }
            }
        }
    </script>
</x-app-layout>
