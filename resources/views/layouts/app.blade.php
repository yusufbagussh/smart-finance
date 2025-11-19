{{-- resources/views/layouts/app.blade.php - Bagian yang diperbaiki --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Finance Tracker') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Success Messages -->
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div
                    class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>


    {{-- Mobile Bottom Navigation - UPDATE untuk Admin --}}
    @if (!auth()->user()->isAdmin())
        <div
            class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 md:hidden z-50">
            <div class="flex justify-around items-center h-16 px-2"> {{-- Beri tinggi eksplisit & padding x --}}

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                    <i class="fas fa-home text-xl mb-1"></i>
                    <span class="text-xs leading-none">Dashboard</span> {{-- leading-none agar lebih rapat --}}
                </a>

                {{-- Transactions --}}
                <a href="{{ route('transactions.index') }}"
                    class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('transactions.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                    <i class="fas fa-exchange-alt text-xl mb-1"></i>
                    <span class="text-xs leading-none">History</span> {{-- Disingkat agar muat --}}
                </a>

                {{-- Tombol Add (Styling Disesuaikan) --}}
                <div class="w-1/5 flex justify-center"> {{-- Wrapper untuk centering --}}
                    <a href="{{ route('transactions.create') }}" title="Add Transaction"
                        class="flex flex-col items-center justify-center w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg transform hover:scale-105 transition-transform">
                        {{-- Hapus -mt-4, beri w/h eksplisit --}}
                        <i class="fas fa-plus text-2xl"></i>
                        {{-- <span class="text-xs mt-1">Add</span> --}} {{-- Teks bisa dihilangkan agar fokus ke ikon --}}
                    </a>
                </div>

                {{-- Budget --}}
                <a href="{{ route('budgets.index') }}"
                    class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('budgets.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                    <i class="fas fa-chart-pie text-xl mb-1"></i>
                    <span class="text-xs leading-none">Budget</span>
                </a>

                {{-- AI Features --}}
                {{-- <a href="{{ route('ml.index') }}"
                    class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('ml.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                    <i class="fas fa-brain text-xl mb-1"></i>
                    <span class="text-xs leading-none">AI</span>
                </a> --}}
                <a href="{{ route('portfolios.index') }}"
                    class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('portfolios.*') || request()->routeIs('investment-transactions.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                    <i class="fas fa-wallet text-xl mb-1"></i>
                    <span class="text-xs leading-none">Invest</span> {{-- Ganti nama jadi 'Invest' agar singkat --}}
                </a>
            </div>
        </div>
        <!-- Add bottom padding on mobile to prevent content being hidden by bottom nav -->
        <div class="h-16 md:hidden"></div>
    @else
        {{-- Cek route --}}
        @if (request()->routeIs('admin.*'))
            {{-- Admin Mobile Bottom Navigation --}}
            <div class="fixed bottom-0 left-0 right-0 border-t border-purple-700 dark:border-purple-900 md:hidden z-50">
                {{-- Background Ungu --}}
                <div class="bg-purple-600 dark:bg-purple-800">
                    <div class="flex justify-around items-center h-16 px-2 text-purple-200 dark:text-purple-300">
                        {{-- Tinggi h-16 & padding x --}}

                        {{-- 1. Admin Dashboard --}}
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('admin.dashboard') ? 'text-white dark:text-white font-semibold' : 'hover:text-white' }}">
                            <i class="fas fa-tachometer-alt text-xl mb-1"></i>
                            <span class="text-xs leading-none">Admin</span>
                        </a>

                        {{-- 2. Users --}}
                        <a href="{{ route('admin.users.index') }}"
                            class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('admin.users.*') ? 'text-white dark:text-white font-semibold' : 'hover:text-white' }}">
                            <i class="fas fa-users text-xl mb-1"></i>
                            <span class="text-xs leading-none">Users</span>
                        </a>

                        {{-- 3. Tombol Add Category (Styling Mirip User Add) --}}
                        <div class="w-1/5 flex justify-center"> {{-- Wrapper --}}
                            <a href="{{ route('admin.categories.create') }}" title="Add Category"
                                class="flex flex-col items-center justify-center w-14 h-14 bg-white text-purple-600 rounded-full shadow-lg transform hover:scale-105 transition-transform">
                                {{-- Hapus -mt-4, beri w/h --}}
                                <i class="fas fa-plus text-2xl"></i>
                            </a>
                        </div>

                        {{-- 4. Categories --}}
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('admin.categories.*') ? 'text-white dark:text-white font-semibold' : 'hover:text-white' }}">
                            <i class="fas fa-tags text-xl mb-1"></i>
                            <span class="text-xs leading-none">Categories</span>
                        </a>

                        {{-- 5. User View --}}
                        {{-- <a href="{{ route('dashboard') }}"
                            class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('dashboard') ? 'text-white dark:text-white font-semibold' : 'hover:text-white' }}">
                            <i class="fas fa-eye text-xl mb-1"></i>
                            <span class="text-xs leading-none">User View</span>
                        </a> --}}
                        <a href="{{ route('admin.monitoring.index') }}"
                            class="flex flex-col items-center justify-center w-1/5 p-2 {{ request()->routeIs('admin.monitoring.*') ? 'text-white dark:text-white font-semibold' : 'text-purple-200 dark:text-purple-300 hover:text-white' }}">
                            <i class="fas fa-heartbeat text-xl mb-1"></i>
                            <span class="text-xs leading-none">Monitoring</span>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div
                class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 md:hidden z-50">
                <div class="flex justify-around items-center h-16 px-2"> {{-- Beri tinggi eksplisit & padding x --}}

                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                        class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                        <i class="fas fa-home text-xl mb-1"></i>
                        <span class="text-xs leading-none">Dashboard</span> {{-- leading-none agar lebih rapat --}}
                    </a>

                    {{-- Transactions --}}
                    <a href="{{ route('transactions.index') }}"
                        class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('transactions.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                        <i class="fas fa-exchange-alt text-xl mb-1"></i>
                        <span class="text-xs leading-none">History</span> {{-- Disingkat agar muat --}}
                    </a>

                    {{-- Tombol Add (Styling Disesuaikan) --}}
                    <div class="w-1/5 flex justify-center"> {{-- Wrapper untuk centering --}}
                        <a href="{{ route('transactions.create') }}" title="Add Transaction"
                            class="flex flex-col items-center justify-center w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg transform hover:scale-105 transition-transform">
                            {{-- Hapus -mt-4, beri w/h eksplisit --}}
                            <i class="fas fa-plus text-2xl"></i>
                            {{-- <span class="text-xs mt-1">Add</span> --}} {{-- Teks bisa dihilangkan agar fokus ke ikon --}}
                        </a>
                    </div>

                    {{-- Budget --}}
                    <a href="{{ route('budgets.index') }}"
                        class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('budgets.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                        <i class="fas fa-chart-pie text-xl mb-1"></i>
                        <span class="text-xs leading-none">Budget</span>
                    </a>

                    {{-- AI Features --}}
                    {{-- <a href="{{ route('ml.index') }}"
                        class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('ml.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                        <i class="fas fa-brain text-xl mb-1"></i>
                        <span class="text-xs leading-none">AI</span>
                    </a> --}}
                    {{-- AI Features diganti Portfolios --}}
                    <a href="{{ route('portfolios.index') }}"
                        class="flex flex-col items-center justify-center w-1/5 p-1 {{ request()->routeIs('portfolios.*') || request()->routeIs('investment-transactions.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-500' }}">
                        <i class="fas fa-wallet text-xl mb-1"></i>
                        <span class="text-xs leading-none">Invest</span>
                    </a>
                </div>
            </div>
        @endif
        <div class="h-16 md:hidden"></div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

    <!-- Custom Scripts Stack -->
    @stack('scripts')
</body>

</html>
