{{-- resources/views/budgets/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('budgets.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Budget') }} - {{ Carbon\Carbon::createFromFormat('Y-m', $budget->month)->format('F Y') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <!-- Current Budget Status -->
        <div class="mb-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 text-white">
                <h3 class="text-lg font-semibold mb-4">Current Budget Status</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-sm opacity-90">Current Limit</p>
                        <p class="text-xl font-bold">Rp {{ number_format($budget->limit, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm opacity-90">Amount Spent</p>
                        <p class="text-xl font-bold">Rp {{ number_format($budget->spent, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm opacity-90">{{ $budget->isOverBudget() ? 'Over Budget' : 'Remaining' }}</p>
                        <p class="text-xl font-bold">Rp
                            {{ number_format(abs($budget->remainingBudget()), 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-white bg-opacity-20 rounded-full h-3">
                    <div class="h-3 rounded-full {{ $budget->isOverBudget() ? 'bg-red-400' : 'bg-green-400' }}"
                        style="width: {{ min($budget->progressPercentage(), 100) }}%"></div>
                </div>
                <p class="text-sm mt-2 opacity-90">{{ number_format($budget->progressPercentage(), 1) }}% of budget used
                </p>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('budgets.update', $budget) }}">
                    @csrf
                    @method('PATCH')

                    <!-- Month Display (Read-only) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Budget Month
                        </label>
                        <div
                            class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm text-gray-900">
                            {{ Carbon\Carbon::createFromFormat('Y-m', $budget->month)->format('F Y') }}
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Month cannot be changed after budget creation
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" id="category_id" disabled
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select a category</option>
                            {{-- Loop melalui $categories dari controller --}}
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}" {{-- Pilih kategori yang sesuai dengan budget saat ini --}}
                                    {{ old('category_id', $budget->category_id) == $category->category_id ? 'selected' : '' }}>
                                    {{-- Tampilkan icon jika ada --}}
                                    {!! $category->icon ?? '' !!} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Select the spending category for this budget.
                        </p>
                    </div>

                    <!-- Budget Limit -->
                    <div class="mb-6">
                        <label for="limit" class="block text-sm font-medium text-gray-700 mb-2">
                            Monthly Budget Limit (IDR)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="limit" id="limit"
                                value="{{ old('limit', $budget->limit) }}" step="1000" min="1000"
                                class="pl-10 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                required>
                        </div>
                        @error('limit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Budget Impact Warning -->
                    @if ($budget->spent > 0)
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-yellow-800 mb-1">Budget
                                        Change Impact</h4>
                                    <p class="text-xs text-yellow-700">
                                        You have already spent Rp {{ number_format($budget->spent, 0, ',', '.') }} this
                                        month.
                                        Changing your budget limit will affect your remaining budget calculation.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Budget Presets -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Quick Adjustments
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <button type="button" onclick="setAmount({{ $budget->limit - 1000000 }})"
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-colors">
                                <div class="font-semibold text-red-600">-1M</div>
                                <div class="text-xs text-gray-500">Decrease</div>
                            </button>
                            <button type="button" onclick="setAmount({{ $budget->limit - 500000 }})"
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-colors">
                                <div class="font-semibold text-orange-600">-500K</div>
                                <div class="text-xs text-gray-500">Small decrease</div>
                            </button>
                            <button type="button" onclick="setAmount({{ $budget->limit + 500000 }})"
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-colors">
                                <div class="font-semibold text-blue-600">+500K</div>
                                <div class="text-xs text-gray-500">Small increase</div>
                            </button>
                            <button type="button" onclick="setAmount({{ $budget->limit + 1000000 }})"
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-colors">
                                <div class="font-semibold text-green-600">+1M</div>
                                <div class="text-xs text-gray-500">Increase</div>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('budgets.index') }}"
                            class="mb-3 sm:mb-0 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 transition ease-in-out duration-150">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <div class="flex space-x-2">
                            {{-- <form method="POST" action="{{ route('budgets.destroy', $budget) }}" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this budget?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete
                                </button>
                            </form> --}}
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i>
                                Update Budget
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function setAmount(amount) {
            if (amount < 0) amount = 0;
            document.getElementById('limit').value = amount;
        }
    </script>
</x-app-layout>
