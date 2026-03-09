<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Observers\ClienteObserver;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        // Registrar observers
        Cliente::observe(ClienteObserver::class);
    }
}
