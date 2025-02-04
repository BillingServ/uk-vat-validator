<?php

namespace VatValidator;

use Illuminate\Support\ServiceProvider;

class VatValidatorProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(VatValidatorService::class, function () {
            return new VatValidatorService();
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/vat_validator.php', 'vat_validator');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/vat_validator.php' => config_path('vat_validator.php'),
            ], 'config');
        }
    }
}
