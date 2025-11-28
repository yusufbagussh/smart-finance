<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('assets.index') }}"
                class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Asset') }}
            </h2>
        </div>
    </x-slot>

    {{-- Konten Utama (di luar header) --}}
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8">

                    <form method="POST" action="{{ route('assets.update', $asset) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div x-data="currencyInput('{{ old('current_price', (int) $asset->current_price) }}')">
                            <label for="current_price_display" class="block text-sm font-medium text-gray-700 mb-2">
                                Initial Price
                            </label>
                            <div class="relative mt-1">
                                <span
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">Rp</span>
                                <input type="text" inputmode="numeric" id="current_price_display"
                                    x-model="formattedValue" @input="updateValues"
                                    class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 font-mono font-semibold"
                                    placeholder="0.00">
                                <input type="hidden" name="current_price" :value="rawValue">
                            </div>
                            @error('current_price')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-6">
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Asset Name
                                </label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $asset->name) }}"
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                    required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="code"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Asset Code / Ticker
                                </label>
                                <input type="text" name="code" id="code"
                                    value="{{ old('code', $asset->code) }}"
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                    required>
                                @error('code')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="issuer"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Issuer (Optional)
                                </label>
                                <input type="text" name="issuer" id="issuer"
                                    value="{{ old('issuer', $asset->issuer) }}"
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                            </div>

                            {{-- Tipe & Satuan (Read-only) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="asset_type"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Asset Type
                                    </label>
                                    <input type="text" id="asset_type"
                                        value="{{ Str::title(str_replace('_', ' ', $asset->asset_type)) }}"
                                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-400"
                                        disabled readonly>
                                </div>
                                <div>
                                    <label for="price_unit"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Price Unit
                                    </label>
                                    <input type="text" id="price_unit" value="{{ Str::title($asset->price_unit) }}"
                                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-400"
                                        disabled readonly>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('assets.index') }}"
                                class="mb-3 sm:mb-0 inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 transition ease-in-out duration-150">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i>
                                Update Asset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
