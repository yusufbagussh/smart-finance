{{-- resources/views/transactions/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('transactions.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Transaction') }}
            </h2>
        </div>
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            input[readonly].flatpickr-input {
                background-color: white !important;
            }

            .dark input[readonly].flatpickr-input {
                background-color: #374151 !important;
            }

            input#date {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%239ca3af' class='w-6 h-6'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0h18' /%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.75rem center;
                background-size: 1.25rem;
                padding-right: 2.5rem;
            }
        </style>
    @endpush

    <div class="max-w-2xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">

                {{-- Form Edit --}}
                <form method="POST" action="{{ route('transactions.update', $transaction) }}" x-data="transactionForm()">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Transaction Type</label>
                        <div class="grid grid-cols-3 gap-3">
                            {{-- Expense --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" x-model="type" class="sr-only">
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="type === 'expense' ? 'border-red-500 bg-red-50 text-red-700' :
                                        'border-gray-300 hover:border-red-300'">
                                    <i class="fas fa-arrow-down text-2xl mb-2 text-red-600"></i>
                                    <p class="font-semibold">Expense</p>
                                </div>
                            </label>

                            {{-- Income --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="income" x-model="type" class="sr-only">
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="type === 'income' ? 'border-green-500 bg-green-50 text-green-700' :
                                        'border-gray-300 hover:border-green-300'">
                                    <i class="fas fa-arrow-up text-2xl mb-2 text-green-600"></i>
                                    <p class="font-semibold">Income</p>
                                </div>
                            </label>

                            {{-- Transfer (BARU) --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="transfer" x-model="type" class="sr-only">
                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="type === 'transfer' ? 'border-blue-500 bg-blue-50 text-blue-700' :
                                        'border-gray-300 hover:border-blue-300'">
                                    <i class="fas fa-retweet text-2xl mb-2 text-blue-600"></i>
                                    <p class="font-semibold">Transfer</p>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="currencyInput('{{ old('amount', (int) $transaction->amount) }}')">

                        {{-- <div class="mb-6" x-data="{
                        rawValue: '{{ old('amount', (int) $transaction->amount) }}',
                        formattedValue: '',
                        formatCurrency(value) {
                            let number = String(value).replace(/[^0-9]/g, '');
                            if (!number) return '';
                            return new Intl.NumberFormat('id-ID').format(number);
                        },
                        updateValues(event) {
                            let inputVal = event.target.value;
                            this.rawValue = inputVal.replace(/\./g, '');
                            this.formattedValue = this.formatCurrency(this.rawValue);
                        }
                    }" x-init="formattedValue = formatCurrency(rawValue)"> --}}

                        <label for="amount_display" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Pinjaman Awal
                        </label>

                        <div class="relative mt-1">
                            <span
                                class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 font-bold">Rp</span>

                            {{-- Input Tampilan --}}
                            <input type="text" id="amount_display" x-model="formattedValue" @input="handleInput"
                                class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="0">

                            {{-- Input Asli --}}
                            <input type="hidden" name="amount" :value="rawValue">
                        </div>

                        <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Perhatian: Mengubah jumlah ini sebaiknya hanya dilakukan jika ada kesalahan input awal.
                        </p>

                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="text" name="date" id="date"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md flatpickr-input"
                            placeholder="Select date..." value="{{ old('date', $transaction->date->format('Y-m-d')) }}"
                            required>
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6" x-show="type === 'expense' || type === 'income'" x-transition>

                        <label for="liability_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <span
                                x-text="type === 'expense' ? 'Link to Debt (Cicilan / Beri Pinjaman)' : 'Link to Debt (Terima Pinjaman / Terima Pelunasan)'"></span>
                            <span class="text-gray-400 text-xs ml-1">(Optional)</span>
                        </label>

                        <select name="liability_id" id="liability_id"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- None --</option>
                            @foreach ($liabilities as $liability)
                                <option value="{{ $liability->id }}" {{-- Pre-select liability yang tersimpan --}}
                                    {{ old('liability_id', $transaction->liability_id ?? '') == $liability->id ? 'selected' : '' }}>

                                    @if ($liability->type == 'payable')
                                        [Hutang] {{ $liability->name }}
                                    @else
                                        [Piutang] {{ $liability->name }}
                                    @endif
                                    (Sisa: Rp {{ number_format($liability->current_balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>

                        {{-- Penjelasan Dinamis --}}
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-show="type === 'expense'">
                            <i class="fas fa-info-circle mr-1"></i>
                            Saldo hutang/piutang ini akan <strong>berkurang</strong> (jika bayar hutang) atau
                            <strong>bertambah</strong> (jika memberi pinjaman).
                        </p>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-show="type === 'income'"
                            style="display: none;">
                            <i class="fas fa-info-circle mr-1"></i>
                            Saldo hutang/piutang ini akan <strong>berkurang</strong> (jika terima pelunasan) atau
                            <strong>bertambah</strong> (jika terima hutang baru).
                        </p>

                        @error('liability_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Dropdown 'To Account' (destination_account_id) dipindahkan ke bawah sini --}}


                    <div class="mb-6" x-show="type === 'expense' || type === 'transfer'" x-transition>
                        <label for="source_account_id" class="block text-sm font-medium text-gray-700 mb-2">From
                            Account</label>
                        <select name="source_account_id" id="source_account_id"
                            :required="type === 'expense' || type === 'transfer'"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select Source Account</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ old('source_account_id', $transaction->source_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} (Rp
                                    {{ number_format($account->current_balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('source_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6" x-show="type === 'income' || type === 'transfer'" x-transition>
                        <label for="destination_account_id" class="block text-sm font-medium text-gray-700 mb-2">To
                            Account</label>
                        <select name="destination_account_id" id="destination_account_id"
                            :required="type === 'income' || type === 'transfer'"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select Destination Account</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ old('destination_account_id', $transaction->destination_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} (Rp
                                    {{ number_format($account->current_balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('destination_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6" x-show="type !== 'transfer'" x-transition>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" id="category_id" x-ref="categorySelect"
                            :required="type !== 'transfer'"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}"
                                    {{ old('category_id', $transaction->category_id) == $category->category_id ? 'selected' : '' }}
                                    x-show="type === '{{ $category->type }}'">
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <div class="relative">
                            <textarea name="description" id="description" rows="3" x-model="description"
                                @input.debounce.500ms="if(type !== 'transfer') classifyTransaction()"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="Describe your transaction..." required>{{ old('description', $transaction->description) }}</textarea>
                            <div x-show="isLoading" class="absolute top-2 right-2 text-gray-400"><i
                                    class="fas fa-spinner fa-spin"></i></div>
                        </div>

                        <div x-show="suggestion.predicted_category && type !== 'transfer'" x-transition
                            class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex items-center">
                                <i class="fas fa-robot text-blue-600 mr-2"></i>
                                <span class="text-sm text-blue-800">
                                    AI suggests: <strong x-text="suggestion.category_name"></strong>
                                    (<span x-text="suggestion.predicted_type"></span>)
                                </span>
                                <button type="button" @click="applySuggestion()"
                                    class="ml-auto text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                    Apply
                                </button>
                            </div>
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('transactions.index') }}"
                            class="mb-3 sm:mb-0 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 transition ease-in-out duration-150">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150"
                            :class="{
                                'bg-red-600 hover:bg-red-700': type === 'expense',
                                'bg-green-600 hover:bg-green-700': type === 'income',
                                'bg-blue-600 hover:bg-blue-700': type === 'transfer'
                            }">
                            <i class="fas fa-save mr-2"></i>
                            <span
                                x-text="type === 'expense' ? 'Update Expense' : (type === 'income' ? 'Update Income' : 'Update Transfer')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                flatpickr("#date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "F j, Y",
                    defaultDate: "{{ old('date', $transaction->date->format('Y-m-d')) }}",
                    maxDate: "today"
                });
            });

            function transactionForm() {
                return {
                    // Inisialisasi type dari data yang ada
                    type: '{{ old('type', $transaction->type) }}',
                    description: `{!! e(old('description', $transaction->description)) !!}`,
                    suggestion: {},
                    isLoading: false,

                    init() {
                        // Logic untuk mereset kategori jika tipe berubah manual oleh user
                        this.$watch('type', (newValue) => {
                            let currentCategory = this.$refs.categorySelect.value;
                            let selectedOption = this.$refs.categorySelect.querySelector(
                                `option[value="${currentCategory}"]`);

                            // Jika pindah ke Transfer, atau kategori saat ini tidak cocok dengan tipe baru -> Reset
                            if (newValue === 'transfer' || (selectedOption && selectedOption.style.display ===
                                    'none')) {
                                this.$refs.categorySelect.value = '';
                            }
                            if (newValue === 'transfer') {
                                this.suggestion = {};
                            }
                        });
                    },

                    async classifyTransaction() {
                        if (this.description.length <= 3) {
                            this.suggestion = {};
                            return;
                        }
                        this.isLoading = true;
                        this.suggestion = {};

                        try {
                            const response = await fetch('{{ route('ml.classify') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    description: this.description
                                })
                            });
                            this.isLoading = false;
                            if (response.ok) {
                                this.suggestion = await response.json();
                            }
                        } catch (error) {
                            this.isLoading = false;
                            console.error(error);
                        }
                    },

                    applySuggestion() {
                        if (this.suggestion.predicted_type) {
                            this.type = this.suggestion.predicted_type;
                        }
                        this.$nextTick(() => {
                            if (this.suggestion.suggested_category_id) {
                                this.$refs.categorySelect.value = this.suggestion.suggested_category_id;
                            }
                            this.suggestion = {};
                        });
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
