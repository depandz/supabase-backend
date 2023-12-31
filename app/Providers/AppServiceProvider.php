<?php

namespace App\Providers;

use App\Contracts\ClientContract;
use App\Contracts\DriverContract;
use App\Contracts\ProvinceContract;
use App\Helpers\ApiResponser;
use App\Services\SupaBase\Client;
use App\Services\SupaBase\Driver;
use App\Services\SupaBase\Province;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProvinceContract::class, Province::class);
        $this->app->bind(ClientContract::class, Client::class);
        $this->app->bind(DriverContract::class, Driver::class);
        // $this->app->singleton(ApiResponser::class, function () {
        //     return new ApiResponser();
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        if (! session()->get('supabase_token')) {
            setup_supabase();
        }

    }
}
