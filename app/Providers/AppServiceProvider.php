<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // !! TAMBAHKAN KODE INI !!
        // Jika aplikasi berjalan di environment produksi (seperti Heroku)
        if ($this->app->environment('production')) {
            // Paksa semua URL yang dihasilkan (termasuk aset) menggunakan HTTPS
            URL::forceScheme('https');
        }
        // !! AKHIR PENAMBAHAN !!
    }
}
