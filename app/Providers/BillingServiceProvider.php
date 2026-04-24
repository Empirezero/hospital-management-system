<?php

namespace App\Providers;

use App\Services\Billing\BillingService;
use App\Services\Billing\MpesaService;
use App\Services\Billing\NumberGeneratorService;
use Illuminate\Support\ServiceProvider;

class BillingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BillingService::class, function ($app) {
            return new BillingService(
                $app->make(MpesaService::class),
                $app->make(NumberGeneratorService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}