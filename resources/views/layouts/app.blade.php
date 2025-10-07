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
            <div class="flex justify-around items-center py-2">
                <a href="{{ route('dashboard') }}"
                    class="flex flex-col items-center p-2 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}">
                    <i class="fas fa-home text-xl"></i>
                    <span class="text-xs mt-1">Dashboard</span>
                </a>
                <a href="{{ route('transactions.index') }}"
                    class="flex flex-col items-center p-2 {{ request()->routeIs('transactions.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}">
                    <i class="fas fa-exchange-alt text-xl"></i>
                    <span class="text-xs mt-1">Transactions</span>
                </a>
                <a href="{{ route('transactions.create') }}"
                    class="flex flex-col items-center p-2 bg-blue-600 text-white rounded-full -mt-4 shadow-lg">
                    <i class="fas fa-plus text-xl"></i>
                    <span class="text-xs mt-1">Add</span>
                </a>
                <a href="{{ route('budgets.index') }}"
                    class="flex flex-col items-center p-2 {{ request()->routeIs('budgets.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}">
                    <i class="fas fa-chart-pie text-xl"></i>
                    <span class="text-xs mt-1">Budget</span>
                </a>
                <a href="{{ route('ml.index') }}"
                    class="flex flex-col items-center p-2 {{ request()->routeIs('ml.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}">
                    <i class="fas fa-brain text-xl"></i>
                    <span class="text-xs mt-1">AI</span>
                </a>
            </div>
        </div>

        <!-- Add bottom padding on mobile to prevent content being hidden by bottom nav -->
        <div class="h-16 md:hidden"></div>
    @else
        {{-- Admin Mobile Bottom Navigation --}}
        <div class="fixed bottom-0 left-0 right-0 bg-purple-600 border-t border-purple-700 md:hidden z-50">
            <div class="flex justify-around items-center py-2">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex flex-col items-center p-2 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-purple-200' }}">
                    <i class="fas fa-tachometer-alt text-xl"></i>
                    <span class="text-xs mt-1">Dashboard</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="flex flex-col items-center p-2 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-purple-200' }}">
                    <i class="fas fa-users text-xl"></i>
                    <span class="text-xs mt-1">Users</span>
                </a>
                <a href="{{ route('admin.categories.create') }}"
                    class="flex flex-col items-center p-2 bg-white text-purple-600 rounded-full -mt-4 shadow-lg">
                    <i class="fas fa-plus text-xl"></i>
                    <span class="text-xs mt-1">Add</span>
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="flex flex-col items-center p-2 {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-purple-200' }}">
                    <i class="fas fa-tags text-xl"></i>
                    <span class="text-xs mt-1">Categories</span>
                </a>
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center p-2 text-purple-200">
                    <i class="fas fa-eye text-xl"></i>
                    <span class="text-xs mt-1">User View</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <!-- Custom Scripts Stack -->
    @stack('scripts')
</body>

</html>
