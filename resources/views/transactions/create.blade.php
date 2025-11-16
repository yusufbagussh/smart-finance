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

    {{-- !! 1. Tambahkan Flatpickr CSS !! --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        {{-- Style tambahan agar ikon kalender muncul (sama seperti sebelumnya) --}}
        <style>
            input[readonly].flatpickr-input {
                background-color: white !important;
            }

            .dark input[readonly].flatpickr-input {
                background-color: #374151 !important;
                /* bg-gray-700 */
            }

            /* Styling agar icon muncul di input */
            input#date {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%239ca3af' class='w-6 h-6'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0h18M5.25 6.75h.008v.008H5.25V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm1.5.008h.008v.008H7.125V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm1.875.008h.008v.008h-.008V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm1.5.008h.008v.008H10.875V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm1.875.008h.008v.008h-.008V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm1.5.008h.008v.008H14.625V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm1.875.008h.008v.008h-.008V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z' /%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.75rem center;
                /* Sesuaikan posisi ikon */
                background-size: 1.25rem;
                /* Sesuaikan ukuran ikon */
                padding-right: 2.5rem;
                /* Beri ruang untuk ikon */
            }
        </style>
    @endpush

    <div class="max-w-2xl mx-auto">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

            <div class="p-6">

                {{-- Initialize Alpine.js component --}}

                <form method="POST" action="{{ route('transactions.store') }}" x-data="transactionForm()">

                    @csrf
                    <!-- Amount -->

                    <div class="mb-6">

                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (IDR)</label>

                        <div class="relative">

                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>

                            <input type="number" name="amount" id="amount" value="{{ old('amount') }}"
                                step="0.01" min="0.01"
                                class="pl-10 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="0.00" required>

                        </div>

                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    </div>

                    <!-- Date -->

                    <div class="mb-6">
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        {{-- Ganti type="date" menjadi type="text", ID tetap 'date' --}}
                        <input type="text" name="date" id="date"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md flatpickr-input"
                            {{-- Tambah class flatpickr-input --}} placeholder="Select date..." value="{{ old('date', date('Y-m-d')) }}"
                            {{-- Value tetap Y-m-d --}} required>
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>



                    <!-- Description with AI Classification -->

                    <div class="mb-6">

                        <label for="description"
                            class="block text-sm font-medium text-gray-700 mb-2">Description</label>

                        <div class="relative">

                            <textarea name="description" id="description" rows="3" x-model="description"
                                @input.debounce.500ms="classifyTransaction()" {{-- Simplified debounce --}}
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="Describe your transaction..." required>{{ old('description') }}</textarea>

                            {{-- Spinner inside textarea --}}

                            <div x-show="isLoading" class="absolute top-2 right-2 text-gray-400">

                                <i class="fas fa-spinner fa-spin"></i>

                            </div>

                        </div>



                        <!-- AI Suggestion -->

                        <div x-show="suggestion.predicted_category" x-transition
                            class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md">

                            <div class="flex items-center">

                                <i class="fas fa-robot text-blue-600 mr-2"></i>

                                <span class="text-sm text-blue-800">

                                    {{-- Tampilkan type prediksi juga --}}

                                    AI suggests: <strong x-text="suggestion.category_name"></strong>

                                    (<span x-text="suggestion.predicted_type"></span>, <span
                                        x-text="Math.round(suggestion.confidence_category * 100)"></span>% sure)

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

                    <!-- Category -->

                    <div class="mb-6">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" id="category_id" required x-ref="categorySelect"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                {{-- <option value="{{ $category->category_id }}" x-show="type.toLowerCase() === '{{ strtolower($category->type) }}'" --}}
                                <option value="{{ $category->category_id }}"
                                    {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Type -->

                    <div class="mb-6">

                        <label class="block text-sm font-medium text-gray-700 mb-3">Transaction Type</label>

                        <div class="grid grid-cols-2 gap-3">

                            <label class="cursor-pointer">

                                {{-- TAMBAHKAN ID DI SINI --}}

                                <input type="radio" name="type" value="income" id="type_income" x-model="type"
                                    class="sr-only" {{ old('type') === 'income' ? 'checked' : '' }}>

                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="type === 'income' ? 'border-green-500 bg-green-50 text-green-700' :

                                        'border-gray-300 hover:border-green-300'">

                                    <i class="fas fa-arrow-up text-2xl mb-2 text-green-600"></i>

                                    <p class="font-semibold">Income</p>

                                    <p class="text-xs text-gray-500">Money received</p>

                                </div>

                            </label>



                            <label class="cursor-pointer">

                                {{-- TAMBAHKAN ID DI SINI --}}

                                <input type="radio" name="type" value="expense" id="type_expense"
                                    x-model="type" class="sr-only"
                                    {{ old('type', 'expense') === 'expense' ? 'checked' : '' }}>

                                <div class="p-4 border-2 rounded-lg text-center transition-colors"
                                    :class="type === 'expense' ? 'border-red-500 bg-red-50 text-red-700' :

                                        'border-gray-300 hover:border-red-300'">

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


                    <!-- Submit Buttons -->

                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200">

                        <a href="{{ route('transactions.index') }}"
                            class="mb-3 sm:mb-0 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 transition ease-in-out duration-150">

                            <i class="fas fa-times mr-2"></i>

                            Cancel

                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">

                            <i class="fas fa-save mr-2"></i>

                            Save Transaction

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
                    // mode: "single", // Default
                    dateFormat: "Y-m-d", // Format backend
                    altInput: true, // Tampilkan format ramah
                    altFormat: "F j, Y", // Format tampilan
                    defaultDate: "{{ old('date', date('Y-m-d')) }}", // Tanggal default
                    allowInput: true, // Izinkan ketik manual
                    // Tambahkan opsi maxDate agar user tidak bisa input tanggal di masa depan
                    maxDate: "today"
                });

            });

            function transactionForm() {
                return {
                    type: '{{ old('type', 'expense') }}',
                    description: `{!! e(old('description')) !!}`, // Use
                    suggestion: {},
                    isLoading: false, // Tambah state loading

                    // Watcher agar kategori disesuaikan saat type berubah
                    init() {
                        this.$watch('type', (newValue) => {
                            // Reset category selection if type changes
                            this.$refs.categorySelect.value = '';
                        });
                    },

                    async classifyTransaction() {
                        if (this.description.length <= 3) {
                            this.suggestion = {}; // Clear suggestion if too short
                            return;
                        }
                        this.isLoading = true; // Tampilkan spinner
                        this.suggestion = {}; // Clear previous suggestion

                        try {
                            const response = await fetch('{{ route('ml.classify') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Accept': 'application/json' // Penting untuk error handling
                                },
                                body: JSON.stringify({
                                    description: this.description
                                })
                            });

                            this.isLoading = false; // Sembunyikan spinner

                            if (!response.ok) {
                                const errorData = await response.json().catch(() => ({
                                    message: 'Unknown error'
                                }));
                                console.error('Classification failed:', response.status, errorData);
                                // Tampilkan error (opsional)
                                this.suggestion = {
                                    explanation: `Error: ${errorData.error || 'Could not classify.'}`
                                };
                                return; // Stop execution
                            }

                            // Jika OK, proses data
                            this.suggestion = await response.json();
                            console.log('Classification result:', this.suggestion);

                        } catch (error) {
                            this.isLoading = false;
                            console.error('Classification fetch error:', error);
                            this.suggestion = {
                                explanation: 'Error connecting to AI service.'
                            };
                        }
                    },

                    applySuggestion() {
                        // !! PERUBAHAN UTAMA DI SINI !!
                        if (this.suggestion.predicted_type) {
                            // 1. Set tipe radio button
                            this.type = this.suggestion.predicted_type;
                        }

                        // Tunggu sebentar agar Alpine sempat memfilter opsi kategori
                        this.$nextTick(() => {
                            if (this.suggestion.suggested_category_id) {
                                // 2. Set kategori dropdown
                                // Periksa apakah opsi masih ada setelah difilter
                                const categoryOption = this.$refs.categorySelect.querySelector(
                                    `option[value="${this.suggestion.suggested_category_id}"]`);
                                if (categoryOption) {
                                    this.$refs.categorySelect.value = this.suggestion.suggested_category_id;
                                } else {
                                    console.warn(
                                        `Suggested category ID ${this.suggestion.suggested_category_id} not found for type ${this.type}.`
                                    );
                                }
                            }
                            // Sembunyikan suggestion box setelah apply
                            this.suggestion = {};
                        });

                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
