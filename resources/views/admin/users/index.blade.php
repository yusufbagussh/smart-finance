<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('admin.dashboard') }}"
                    class="mr-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    <i class="fas fa-users mr-2"></i>
                    {{ __('User Management') }}
                </h2>
            </div>
        </div>
    </x-slot>

    {{-- Container Konten (Jarak Konsisten) --}}
    <div class="space-y-6">

        {{-- Filter Users Card --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" x-data="{ showFilters: {{ request()->hasAny(['search', 'status', 'sort_by']) ? 'true' : 'false' }} }">

            <div class="p-6">
                {{-- Header Filter (Collapsible Trigger) --}}
                <div class="flex items-center justify-between cursor-pointer" @click="showFilters = !showFilters">
                    <div class="flex items-center">
                        <i class="fas fa-filter text-gray-500 dark:text-gray-400 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Filter Users
                        </h3>
                        {{-- Badge Indikator Filter Aktif --}}
                        @if (request()->hasAny(['search', 'status', 'sort_by']))
                            <span
                                class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                Active
                            </span>
                        @endif
                    </div>

                    {{-- Ikon Panah --}}
                    <button type="button"
                        class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition-transform duration-200"
                        :class="{ 'rotate-180': showFilters }">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>

                {{-- Form Filter Content --}}
                <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">

                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">

                            {{-- Search Input (4 Kolom) --}}
                            <div class="sm:col-span-2 lg:col-span-4">
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search
                                    Users</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                                        class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Search by name or email...">
                                </div>
                            </div>

                            {{-- Status Filter (3 Kolom) --}}
                            <div class="lg:col-span-3">
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select name="status" id="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200">
                                    <option value="">All Users</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                        (Last 30 days)</option>
                                </select>
                            </div>

                            {{-- Sort By Filter (2 Kolom) --}}
                            <div class="lg:col-span-2">
                                <label for="sort_by"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort By</label>
                                <select name="sort_by" id="sort_by"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-gray-200">
                                    <option value="created_at"
                                        {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Registration Date
                                    </option>
                                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name
                                    </option>
                                    <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email
                                    </option>
                                </select>
                            </div>

                            {{-- Tombol Action Sejajar di Samping (3 Kolom) --}}
                            <div class="sm:col-span-2 lg:col-span-3 flex items-center justify-end space-x-2 pb-0.5">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    <i class="fas fa-filter mr-2"></i>
                                    Apply Filter
                                </button>
                                <a href="{{ route('admin.users.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                                    <i class="fas fa-times mr-2"></i>
                                    Clear
                                </a>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>

        {{-- Users Table Card --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            @if ($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Transactions
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Budgets
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Registered
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            <i class="fas fa-exchange-alt text-blue-500 mr-1"></i>
                                            {{ $user->transactions_count }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            <i class="fas fa-chart-pie text-green-500 mr-1"></i>
                                            {{ $user->budgets_count }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $user->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->isActive())
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Active
                                            </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Inactive
                                            </span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">
                                                since {{ $user->deactivated_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Toggle Status --}}
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                                            class="inline toggle-status-form" data-user-name="{{ $user->name }}">
                                            @csrf
                                            @method('PATCH')
                                            @if ($user->isActive())
                                                <button type="submit"
                                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                                    title="Deactivate User">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            @else
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                    title="Activate User">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            @endif
                                        </form>

                                        {{-- Delete User --}}
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            class="inline delete-user-form" data-user-name="{{ $user->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No users found</h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if (request()->hasAny(['search', 'status', 'sort_by']))
                            Try adjusting your filters
                        @else
                            No users have registered yet
                        @endif
                    </p>
                </div>
            @endif
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Konfirmasi Toggle Status
                const toggleForms = document.querySelectorAll('.toggle-status-form');
                toggleForms.forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        const userName = form.dataset.userName;
                        const isActivating = form.querySelector('button i.fa-user-check');
                        const actionText = isActivating ? 'activate' : 'deactivate';
                        const confirmButtonColor = isActivating ? '#3085d6' : '#d33';
                        const confirmButtonText = isActivating ? 'Yes, activate!' : 'Yes, deactivate!';

                        Swal.fire({
                            title: `Confirm ${actionText}?`,
                            text: `Do you want to ${actionText} the user "${userName}"? They will ${isActivating ? 'be able to' : 'not be able to'} log in.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: confirmButtonColor,
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: confirmButtonText
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                // Konfirmasi Delete User
                const deleteUserForms = document.querySelectorAll('.delete-user-form');
                deleteUserForms.forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        const userName = form.dataset.userName;
                        Swal.fire({
                            title: 'Delete User?',
                            text: `This will permanently delete "${userName}" and all their data! This action cannot be undone.`,
                            icon: 'error',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, DELETE user!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
