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
        // Si la petición llega por HTTPS (incluido un proxy con TrustProxies),
        // todas las URLs absolutas generadas deben seguir siendo https para no
        // provocar envíos mixtos http desde páginas https.
        if (request()->isSecure()) {
            URL::forceScheme('https');
        }
    }
}
