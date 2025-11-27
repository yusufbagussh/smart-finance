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
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                    class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                    class="fixed top-24 right-4 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg max-w-sm">
                    <div class="flex items-start">
                        <div class="py-1"><i class="fas fa-exclamation-circle mr-3 text-2xl"></i></div>
                        <div>
                            <p class="font-bold">Error / Warning</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="ml-4 text-red-700 hover:text-red-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                    class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif --}}

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
            class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 md:hidden z-50 pb-safe">
            {{-- Grid 7 Kolom: Pembagian Ruang Sempurna --}}
            <div class="grid grid-cols-7 h-16 relative">

                {{-- 1. HOME --}}
                <a href="{{ route('dashboard') }}"
                    class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-home text-lg mb-0.5"></i>
                    <span class="text-[9px] font-medium leading-none">Home</span>
                </a>

                {{-- 2. ACCTS (Accounts) --}}
                <a href="{{ route('accounts.index') }}"
                    class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('accounts.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-wallet text-lg mb-0.5"></i>
                    <span class="text-[9px] font-medium leading-none">Accts</span>
                </a>

                {{-- 3. HISTORY (Transactions) --}}
                <a href="{{ route('transactions.index') }}"
                    class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('transactions.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-exchange-alt text-lg mb-0.5"></i>
                    <span class="text-[9px] font-medium leading-none">Trx</span>
                </a>

                {{-- 4. ADD BUTTON (FLOATING CENTER) --}}
                <div class="relative flex justify-center items-center">
                    {{-- Tombol melayang keluar (-top-6) --}}
                    <a href="{{ route('transactions.create') }}"
                        class="absolute -top-3 w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg border-[4px] border-gray-50 dark:border-gray-900 flex items-center justify-center hover:bg-blue-700 transition-transform active:scale-95">
                        <i class="fas fa-plus text-xl"></i>
                    </a>
                </div>

                {{-- 5. DEBT (Liabilities) --}}
                <a href="{{ route('liabilities.index') }}"
                    class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('liabilities.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-file-invoice-dollar text-lg mb-0.5"></i>
                    <span class="text-[9px] font-medium leading-none">Debt</span>
                </a>

                {{-- 6. INVEST (Portfolios) --}}
                <a href="{{ route('portfolios.index') }}"
                    class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('portfolios.*') || request()->routeIs('investment-transactions.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-chart-line text-lg mb-0.5"></i>
                    <span class="text-[9px] font-medium leading-none">Invest</span>
                </a>

                {{-- 7. AI (Insight) --}}
                <a href="{{ route('ml.index') }}"
                    class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('ml.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                    <i class="fas fa-brain text-lg mb-0.5"></i>
                    <span class="text-[9px] font-medium leading-none">AI</span>
                </a>

            </div>
        </div>
        <!-- Add bottom padding on mobile to prevent content being hidden by bottom nav -->
        <div class="h-16 md:hidden"></div>
    @else
        {{-- Cek route --}}
        @if (request()->routeIs('admin.*'))
            {{-- Admin Mobile Bottom Navigation --}}
            <div
                class="fixed bottom-0 left-0 right-0 border-t border-purple-700 dark:border-purple-900 md:hidden z-50 pb-safe">
                {{-- Background Ungu --}}
                <div class="bg-purple-600 dark:bg-purple-800">

                    {{-- Grid 5 Kolom --}}
                    <div class="grid grid-cols-5 h-16">

                        {{-- 1. ADMIN DASHBOARD --}}
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex flex-col items-center justify-center w-full h-full hover:bg-purple-700 dark:hover:bg-purple-900 transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-white font-bold' : 'text-purple-200 hover:text-white' }}">
                            <i class="fas fa-tachometer-alt text-lg mb-0.5"></i>
                            <span class="text-[10px] font-medium leading-none">Admin</span>
                        </a>

                        {{-- 2. USERS --}}
                        <a href="{{ route('admin.users.index') }}"
                            class="flex flex-col items-center justify-center w-full h-full hover:bg-purple-700 dark:hover:bg-purple-900 transition-colors {{ request()->routeIs('admin.users.*') ? 'text-white font-bold' : 'text-purple-200 hover:text-white' }}">
                            <i class="fas fa-users text-lg mb-0.5"></i>
                            <span class="text-[10px] font-medium leading-none">Users</span>
                        </a>

                        {{-- 3. MONITORING (TENGAH - PUSAT PERHATIAN) --}}
                        <a href="{{ route('admin.monitoring.index') }}"
                            class="flex flex-col items-center justify-center w-full h-full hover:bg-purple-700 dark:hover:bg-purple-900 transition-colors {{ request()->routeIs('admin.monitoring.*') ? 'text-white font-bold' : 'text-purple-200 hover:text-white' }}">
                            <i class="fas fa-heartbeat text-xl mb-0.5"></i> {{-- Icon sedikit lebih besar --}}
                            <span class="text-[10px] font-medium leading-none">Monitor</span>
                        </a>

                        {{-- 4. CATEGORIES --}}
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex flex-col items-center justify-center w-full h-full hover:bg-purple-700 dark:hover:bg-purple-900 transition-colors {{ request()->routeIs('admin.categories.*') ? 'text-white font-bold' : 'text-purple-200 hover:text-white' }}">
                            <i class="fas fa-tags text-lg mb-0.5"></i>
                            <span class="text-[10px] font-medium leading-none">Categs</span>
                        </a>

                        {{-- 5. ASSETS (BARU) --}}
                        <a href="{{ route('assets.index') }}"
                            class="flex flex-col items-center justify-center w-full h-full hover:bg-purple-700 dark:hover:bg-purple-900 transition-colors {{ request()->routeIs('assets.*') ? 'text-white font-bold' : 'text-purple-200 hover:text-white' }}">
                            <i class="fas fa-gem text-lg mb-0.5"></i>
                            <span class="text-[10px] font-medium leading-none">Assets</span>
                        </a>

                    </div>
                </div>
            </div>
        @else
            <div
                class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 md:hidden z-50 pb-safe">
                {{-- Grid 7 Kolom: Pembagian Ruang Sempurna --}}
                <div class="grid grid-cols-6 h-16 relative">

                    {{-- 1. HOME --}}
                    <a href="{{ route('dashboard') }}"
                        class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i class="fas fa-home text-lg mb-0.5"></i>
                        <span class="text-[9px] font-medium leading-none">Home</span>
                    </a>

                    {{-- 2. ACCTS (Accounts) --}}
                    <a href="{{ route('accounts.index') }}"
                        class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('accounts.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i class="fas fa-wallet text-lg mb-0.5"></i>
                        <span class="text-[9px] font-medium leading-none">Accts</span>
                    </a>

                    {{-- 3. HISTORY (Transactions) --}}
                    <a href="{{ route('transactions.index') }}"
                        class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('transactions.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i class="fas fa-exchange-alt text-lg mb-0.5"></i>
                        <span class="text-[9px] font-medium leading-none">Trx</span>
                    </a>

                    {{-- 4. ADD BUTTON (FLOATING CENTER) --}}
                    {{-- <div class="relative flex justify-center items-center">
                        <a href="{{ route('transactions.create') }}"
                            class="absolute -top-3 w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg border-[2px] border-gray-50 dark:border-gray-900 flex items-center justify-center hover:bg-blue-700 transition-transform active:scale-95">
                            <i class="fas fa-plus text-xl"></i>
                        </a>
                    </div> --}}

                    {{-- 5. DEBT (Liabilities) --}}
                    <a href="{{ route('liabilities.index') }}"
                        class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('liabilities.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i class="fas fa-file-invoice-dollar text-lg mb-0.5"></i>
                        <span class="text-[9px] font-medium leading-none">Debt</span>
                    </a>

                    {{-- 6. INVEST (Portfolios) --}}
                    <a href="{{ route('portfolios.index') }}"
                        class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('portfolios.*') || request()->routeIs('investment-transactions.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i class="fas fa-chart-line text-lg mb-0.5"></i>
                        <span class="text-[9px] font-medium leading-none">Invest</span>
                    </a>

                    {{-- 7. AI (Insight) --}}
                    <a href="{{ route('ml.index') }}"
                        class="flex flex-col items-center justify-center w-full h-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ request()->routeIs('ml.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i class="fas fa-brain text-lg mb-0.5"></i>
                        <span class="text-[9px] font-medium leading-none">AI</span>
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
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Cek Sukses
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: {!! json_encode(session('success')) !!}, // <-- PERUBAHAN DI SINI
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            // 2. Cek Error (INI PERBAIKANNYA)
            @if (session('error')) Swal.fire({
        icon: 'error',
        title: 'Action Failed',
        text: {!! json_encode(session('error')) !!} || 'Terjadi kesalahan yang tidak diketahui.',
        confirmButtonText: 'Oke',
        confirmButtonColor: '#d33'
    }); @endif

            // 3. Cek Validasi Error
            @if ($errors->any()) let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '<li>{{ $error }}</li>';
                @endforeach

                Swal.fire({
                    icon: 'warning',
                    title: 'Input Error',
                    html: `<ul style="text-align: left;">${errorMessages}</ul>`,
                }); @endif
        });
    </script> --}}
    <!-- Custom Scripts Stack -->
    @stack('scripts')
</body>

</html>
