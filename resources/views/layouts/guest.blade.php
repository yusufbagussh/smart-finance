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

    <!-- Scripts -->
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    {{-- UBAH BAGIAN INI: Gunakan 'justify-center py-8' agar selalu center vertikal di semua layar --}}
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 dark:bg-gray-900 px-4 py-8">

        {{-- Logo & Brand Name --}}
        <div class="mb-2">
            <a href="/" class="flex flex-col items-center">
                <span class="text-4xl">💰</span>
                <span class="text-2xl font-bold text-gray-800 dark:text-gray-200 mt-1">
                    FinanceTracker
                </span>
            </a>
        </div>

        {{-- Card Form Box --}}
        <div
            class="w-full sm:max-w-md mt-4 px-6 py-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden rounded-2xl sm:rounded-lg">
            {{ $slot }}
        </div>

    </div>
</body>

</html>
