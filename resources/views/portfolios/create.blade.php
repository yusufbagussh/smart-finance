<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('portfolios.index') }}"
                class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Portfolio') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('portfolios.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Portfolio Name
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                placeholder="e.g., Retirement Fund" required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Description (Optional)
                            </label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                placeholder="e.g., Long-term investments in stocks and bonds.">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                <i class="fas fa-user-shield mr-2 text-gray-500 dark:text-gray-400"></i>
                                AI Investment Profile (Optional)
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 -mt-4">
                                Data ini akan membantu AI memberikan analisis yang lebih personal untuk portofolio ini.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="risk_profile"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profil
                                        Risiko</label>
                                    <select name="risk_profile" id="risk_profile"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('risk_profile') border-red-500 @enderror">
                                        <option value="">Pilih profil risiko...</option>
                                        <option value="conservative"
                                            {{ old('risk_profile') == 'conservative' ? 'selected' : '' }}>Konservatif
                                            (Stabil, pertumbuhan lambat)</option>
                                        <option value="moderate"
                                            {{ old('risk_profile') == 'moderate' ? 'selected' : '' }}>Moderat (Seimbang
                                            antara risiko & hasil)</option>
                                        <option value="aggressive"
                                            {{ old('risk_profile') == 'aggressive' ? 'selected' : '' }}>Agresif (Siap
                                            ambil risiko tinggi)</option>
                                    </select>
                                    @error('risk_profile')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="time_horizon"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Horizon Waktu
                                        (Tahun)</label>
                                    <input type="number" name="time_horizon" id="time_horizon"
                                        value="{{ old('time_horizon') }}"
                                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500 @error('time_horizon') border-red-500 @enderror"
                                        placeholder="Misal: 5 (untuk 5 tahun)">
                                    @error('time_horizon')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="goal"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tujuan Utama
                                    Portofolio</label>
                                <input type="text" name="goal" id="goal" value="{{ old('goal') }}"
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500 @error('goal') border-red-500 @enderror"
                                    placeholder="Misal: Dana Pensiun, Dana Pendidikan Anak">
                                @error('goal')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="future_plans"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rencana Investasi
                                    Anda ke Depan?</label>
                                <textarea name="future_plans" id="future_plans" rows="3"
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500 @error('future_plans') border-red-500 @enderror"
                                    placeholder="Misal: Fokus menambah Reksadana, mulai masuk saham, dll.">{{ old('future_plans') }}</textarea>
                                @error('future_plans')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="risk_tolerance_notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Toleransi
                                    Risiko Anda?</label>
                                <textarea name="risk_tolerance_notes" id="risk_tolerance_notes" rows="3"
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-500 @error('risk_tolerance_notes') border-red-500 @enderror"
                                    placeholder="Misal: Saya tidak nyaman jika portofolio turun lebih dari 10% setahun.">{{ old('risk_tolerance_notes') }}</textarea>
                                @error('risk_tolerance_notes')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('portfolios.index') }}"
                                class="mb-3 sm:mb-0 inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 transition ease-in-out duration-150">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i>
                                Save Portfolio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
