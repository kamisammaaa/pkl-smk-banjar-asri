<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // ← ✅ WAJIB: Import URL Facade

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
        // ✅ Force HTTPS jika FORCE_HTTPS=true di .env
        // Berguna untuk Cloudflare Tunnel yang forward request via HTTP
        if (env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}
