<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Portfolios') }}
            </h2>
            <a href="{{ route('portfolios.create') }}"
                class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Portfolio
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @if ($portfolios->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($portfolios as $portfolio)
                            <div class="p-4 sm:p-6 transition-colors duration-150">
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">

                                    {{-- Bagian Kiri: Ikon & Detail --}}
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                        <div class="ml-4 min-w-0 flex-1">
                                            <p class="text-base font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $portfolio->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                {{ $portfolio->description ?? 'No description' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Bagian Kanan: Actions --}}
                                    <div class="flex items-center space-x-2 sm:space-x-3 self-end sm:self-center">
                                        {{-- Tombol View (menuju dashboard portofolio) --}}
                                        <a href="{{ route('portfolios.show', $portfolio) }}" {{-- PERUBAHAN: Dibuat sama persis seperti tombol Edit --}}
                                            class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150"
                                            title="View">
                                            <i class="fas fa-eye"></i> {{-- Teks "View" dihapus --}}
                                        </a>
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('portfolios.edit', $portfolio) }}"
                                            class="p-2 rounded-md text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Tombol Delete --}}
                                        <form method="POST" action="{{ route('portfolios.destroy', $portfolio) }}"
                                            class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-md text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-12 px-6">
                        <i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            No portfolios found
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Get started by creating your first investment portfolio.
                        </p>
                        <a href="{{ route('portfolios.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Add Your First Portfolio
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- CDN untuk SweetAlert (jika belum ada di layout utama) --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault(); // Hentikan submit

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this! All associated transactions will also be deleted.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6', // Biru
                            cancelButtonColor: '#d33', // Merah
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // Lanjutkan submit
                            }
                        })
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
