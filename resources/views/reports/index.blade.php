{{-- resources/views/reports/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-file-download mr-2"></i>
            {{ __('Export Reports') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Transaction Report -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <i class="fas fa-list text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Transaction Report
                    </h3>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Export detailed list of your transactions with filters. Choose between Excel or PDF format.
                </p>

                <form method="POST" action="{{ route('reports.transactions.excel') }}" x-data="{ format: 'excel' }">
                    @csrf

                    <!-- Filters -->
                    <div class="space-y-4 mb-6">
                        <!-- Transaction Type -->
                        <div>
                            <label for="type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Transaction Type
                            </label>
                            <select name="type" id="type"
                                class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">All Types</option>
                                <option value="income">Income Only</option>
                                <option value="expense">Expense Only</option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Category
                            </label>
                            <select name="category_id" id="category_id"
                                class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->icon }}
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="date_from"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    From Date
                                </label>
                                <input type="date" name="date_from" id="date_from"
                                    class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="date_to"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    To Date
                                </label>
                                <input type="date" name="date_to" id="date_to"
                                    class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Export Format -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Export Format
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" x-model="format" value="excel" class="sr-only">
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="format === 'excel' ? 'border-green-500 bg-green-50 dark:bg-green-900' :
                                        'border-gray-300 dark:border-gray-600 hover:border-green-300'">
                                    <i class="fas fa-file-excel text-green-600 text-2xl mb-2"></i>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">Excel</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">.xlsx format</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" x-model="format" value="pdf" class="sr-only">
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="format === 'pdf' ? 'border-red-500 bg-red-50 dark:bg-red-900' :
                                        'border-gray-300 dark:border-gray-600 hover:border-red-300'">
                                    <i class="fas fa-file-pdf text-red-600 text-2xl mb-2"></i>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">PDF</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">.pdf format</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            @click="$el.form.action = format === 'excel' ? '{{ route('reports.transactions.excel') }}' : '{{ route('reports.transactions.pdf') }}'"
                            class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-download mr-2"></i>
                            <span x-text="format === 'excel' ? 'Download Excel' : 'Download PDF'"></span>
                        </button>

                        <button type="button" x-show="format === 'pdf'"
                            @click="$el.form.action = '{{ route('reports.transactions.preview') }}'; $el.form.submit()"
                            class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-eye mr-2"></i>
                            Preview PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Financial Report -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <i class="fas fa-chart-pie text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Financial Report
                    </h3>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Comprehensive financial report with summary, income/expense breakdown, and category analysis.
                </p>

                <form method="POST" action="{{ route('reports.financial-report.excel') }}" x-data="{ format: 'excel' }">
                    @csrf

                    <!-- Date Range -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="start_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Report Period - Start Date
                            </label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                        </div>

                        <div>
                            <label for="end_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Report Period - End Date
                            </label>
                            <input type="date" name="end_date" id="end_date"
                                value="{{ now()->endOfMonth()->format('Y-m-d') }}"
                                class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                        </div>
                    </div>

                    <!-- Quick Date Presets -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Quick Select
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="setDateRange('month')"
                                class="px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors">
                                This Month
                            </button>
                            <button type="button" onclick="setDateRange('quarter')"
                                class="px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors">
                                This Quarter
                            </button>
                            <button type="button" onclick="setDateRange('year')"
                                class="px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors">
                                This Year
                            </button>
                        </div>
                    </div>

                    <!-- Export Format -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Export Format
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" x-model="format" value="excel" class="sr-only">
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="format === 'excel' ? 'border-green-500 bg-green-50 dark:bg-green-900' :
                                        'border-gray-300 dark:border-gray-600 hover:border-green-300'">
                                    <i class="fas fa-file-excel text-green-600 text-2xl mb-2"></i>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">Excel</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Multiple sheets</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" x-model="format" value="pdf" class="sr-only">
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="format === 'pdf' ? 'border-red-500 bg-red-50 dark:bg-red-900' :
                                        'border-gray-300 dark:border-gray-600 hover:border-red-300'">
                                    <i class="fas fa-file-pdf text-red-600 text-2xl mb-2"></i>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">PDF</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Detailed report</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div
                        class="mb-6 p-3 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-2 mt-0.5"></i>
                            <div>
                                <p class="text-xs text-blue-800 dark:text-blue-200">
                                    <strong>Excel format includes:</strong> Summary, Income list, Expense list, and
                                    Category breakdown in separate sheets.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        @click="$el.form.action = format === 'excel' ? '{{ route('reports.financial-report.excel') }}' : '{{ route('reports.financial-report.pdf') }}'"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-purple-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-download mr-2"></i>
                        <span x-text="format === 'excel' ? 'Download Excel Report' : 'Download PDF Report'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Export History (Optional) -->
    <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-history mr-2"></i>
                About Export Features
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Transaction Report</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Detailed transaction list</li>
                        <li>• Filter by type, category, and date</li>
                        <li>• Excel: Single sheet with all data</li>
                        <li>• PDF: Formatted for printing</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Financial Report</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Comprehensive financial summary</li>
                        <li>• Income and expense breakdown</li>
                        <li>• Excel: 4 sheets (Summary, Income, Expense, Categories)</li>
                        <li>• PDF: Professional formatted report</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function setDateRange(period) {
                const startInput = document.getElementById('start_date');
                const endInput = document.getElementById('end_date');
                const today = new Date();

                if (period === 'month') {
                    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    startInput.value = startOfMonth.toISOString().split('T')[0];
                    endInput.value = endOfMonth.toISOString().split('T')[0];
                } else if (period === 'quarter') {
                    const quarter = Math.floor(today.getMonth() / 3);
                    const startOfQuarter = new Date(today.getFullYear(), quarter * 3, 1);
                    const endOfQuarter = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
                    startInput.value = startOfQuarter.toISOString().split('T')[0];
                    endInput.value = endOfQuarter.toISOString().split('T')[0];
                } else if (period === 'year') {
                    const startOfYear = new Date(today.getFullYear(), 0, 1);
                    const endOfYear = new Date(today.getFullYear(), 11, 31);
                    startInput.value = startOfYear.toISOString().split('T')[0];
                    endInput.value = endOfYear.toISOString().split('T')[0];
                }
            }
        </script>
    @endpush
</x-app-layout>
