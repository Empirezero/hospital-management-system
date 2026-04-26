<?php

namespace App\Providers;

use App\Services\Billing\BillingService;
use App\Services\Billing\MpesaService;
use App\Services\Billing\NumberGeneratorService;
use Illuminate\Support\ServiceProvider;

class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NumberGeneratorService::class, function () {
            return new NumberGeneratorService();
        });

        $this->app->singleton(BillingService::class, function ($app) {
            return new BillingService(
                $app->make(NumberGeneratorService::class),
            );
        });

        $this->app->singleton(MpesaService::class, function ($app) {
            return new MpesaService(
                $app->make(NumberGeneratorService::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
