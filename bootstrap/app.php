<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Schedule;

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
    ->withSchedule(function (Schedule $schedule) { // <-- 2. TAMBAHKAN BLOK INI
        $schedule->command('app:update-asset-prices')->dailyAt('05:00');
        // * * * * * cd /path-ke-proyek-laravel-anda && php artisan schedule:run >> /dev/null 2>&1
    })
    ->create();
