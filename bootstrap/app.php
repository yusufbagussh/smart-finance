<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withSchedule(function (Illuminate\Console\Scheduling\Schedule $schedule) { // <-- UBAH BARIS INI
        // **Run 1: Morning Check (09:30 WIB)**
        $schedule->command('app:update-asset-prices')
            ->dailyAt('09:30');

        // **Run 2: Midday Check (14:00 WIB)**
        $schedule->command('app:update-asset-prices')
            ->dailyAt('14:00');

        // **Run 3: FINAL CLOSING PRICE (17:00 WIB - Paling Penting)**
        $schedule->command('app:update-asset-prices')
            ->dailyAt('17:00');

        // // 1. Update Per Jam Selama Jam Bursa (09:00 hingga 17:00 WIB)
        // $schedule->command('app:update-asset-prices')
        //     ->hourly() // Jalankan setiap jam
        //     // Batasi waktu dijalankan dari jam 9 pagi hingga jam 5 sore (WIB)
        //     ->between('09:00', '17:00')
        //     ->timezone('Asia/Jakarta');

        // // 2. Run FINAL CLOSING PRICE (17:01 WIB - Pastikan setelah bursa tutup)
        // // Ini adalah harga yang paling penting dan final.
        // $schedule->command('app:update-asset-prices')
        //     ->dailyAt('17:01', 'Asia/Jakarta');

        // // 3. Run Pagi Hari (Opsional, untuk harga Emas/Crypto di luar jam kerja)
        // $schedule->command('app:update-asset-prices')
        //     ->dailyAt('06:00', 'Asia/Jakarta'); // Sebelum pengguna bangun
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
