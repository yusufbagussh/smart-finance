<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. Penyesuaian Title Brand --}}
    <title>{{ config('app.name', 'Artafis') }} | Smart Finance Tracker</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- FontAwesome & SweetAlert -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- 2. Anti-Flicker Dark Mode Script --}}
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Scripts -->
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8">

        {{-- 3. Logo & Brand Name Artafis --}}
        <div class="mb-3 text-center">
            <a href="/" class="flex flex-col items-center group">
                <span class="text-4xl transform group-hover:scale-110 transition-transform duration-200">💰</span>
                <span class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100 mt-1">
                    Artafis
                </span>
                <span
                    class="text-xs font-semibold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mt-0.5">
                    Smart Finance Tracker
                </span>
            </a>
        </div>

        {{-- Card Form Box --}}
        <div
            class="w-full sm:max-w-md mt-2 px-6 py-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden rounded-2xl sm:rounded-lg border border-gray-100 dark:border-gray-700">
            {{ $slot }}
        </div>

    </div>

    {{-- 4. SweetAlert Loader saat Submit Form Auth --}}
    <script>
        document.addEventListener('submit', function(e) {
            const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains(
                'dark');
            const bgColor = isDark ? '#111827' : '#FFFFFF';
            const titleColor = isDark ? '#F9FAFB' : '#111827';
            const subColor = isDark ? '#9CA3AF' : '#6B7280';
            const borderColor = isDark ? 'border-gray-800' : 'border-gray-100 shadow-xl';

            Swal.fire({
                html: `
                    <div class="flex flex-col items-center justify-center pt-2">
                        <div class="mb-4">
                            <i class="fas fa-user-lock text-4xl text-blue-500 animate-pulse"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-1" style="color: ${titleColor};">Memproses Autentikasi</h3>
                        <p class="text-xs font-medium" style="color: ${subColor};">Memverifikasi data akun Anda...</p>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                background: bgColor,
                customClass: {
                    popup: `rounded-2xl border ${borderColor}`
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    </script>
</body>

</html>
