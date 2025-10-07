{{-- resources/views/budgets/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('budgets.index') }}"
                class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Set New Budget') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-800 mb-1">Budget Planning Tips
                            </h4>
                            <ul class="text-xs text-blue-700 space-y-1">
                                <li>• Follow the 50/30/20 rule: 50% needs, 30% wants, 20% savings</li>
                                <li>• Set realistic limits based on your income and expenses</li>
                                <li>• Review and adjust your budget monthly</li>
                                <li>• Leave some buffer for unexpected expenses</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('budgets.store') }}">
                    @csrf

                    <!-- Month Selection -->
                    <div class="mb-6">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                            Budget Month
                        </label>
                        <input type="month" name="month" id="month"
                            value="{{ old('month', now()->format('Y-m')) }}" min="{{ now()->format('Y-m') }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            required>
                        <p class="mt-1 text-xs text-gray-500">
                            Select the month you want to set a budget for
                        </p>
                        @error('month')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Budget Limit -->
                    <div class="mb-6">
                        <label for="limit" class="block text-sm font-medium text-gray-700 mb-2">
                            Monthly Budget Limit (IDR)
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="limit" id="limit" value="{{ old('limit') }}"
                                step="1000" min="1000"
                                class="pl-10 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="5000000" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Set a realistic spending limit for the month
                        </p>
                        @error('limit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Budget Breakdown Suggestions -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">
                            <i class="fas fa-calculator mr-2"></i>
                            Budget Breakdown Suggestions
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                            <div class="text-center p-3 bg-white rounded">
                                <div class="text-green-600 font-semibold">50%</div>
                                <div class="text-gray-600">Needs</div>
                                <div class="text-xs text-gray-500">Housing, food, utilities</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded">
                                <div class="text-blue-600 font-semibold">30%</div>
                                <div class="text-gray-600">Wants</div>
                                <div class="text-xs text-gray-500">Entertainment, dining out</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded">
                                <div class="text-purple-600 font-semibold">20%</div>
                                <div class="text-gray-600">Savings</div>
                                <div class="text-xs text-gray-500">Emergency fund, investments</div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Budget Presets -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Quick Budget Presets
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <button type="button" onclick="setAmount(2000000)"
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-colors">
                                <div class="font-semibold text-gray-900">2M</div>
                                <div class="text-xs text-gray-500">Student</div>
                            </button>
                            <button type="button" onclick="setAmount(5000000)"
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-colors">
                                <div class="font-semibold text-gray-900">5M</div>
                                <div class="text-xs text-gray-500">Basic</div>
                            </button>
                            <button type="button" onclick="setAmount(10000000)"
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-colors">
                                <div class="font-semibold text-gray-900">10M</div>
                                <div class="text-xs text-gray-500">Comfortable</div>
                            </button>
                            <button type="button" onclick="setAmount(20000000)"
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-colors">
                                <div class="font-semibold text-gray-900">20M</div>
                                <div class="text-xs text-gray-500">Premium</div>
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
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-save mr-2"></i>
                            Create Budget
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function setAmount(amount) {
            document.getElementById('limit').value = amount;
        }
    </script>
</x-app-layout>
