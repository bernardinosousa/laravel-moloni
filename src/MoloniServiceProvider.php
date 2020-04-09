<?php

namespace Tiagosimoesdev\Moloni;

use Illuminate\Support\ServiceProvider;

class MoloniServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/../config/moloni.php' => config_path('moloni.php'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/moloni.php', 'moloni');

        $this->app->bind(Moloni::class, function () {
            $moloniConfig = config('moloni');

            $this->guardAgainstInvalidConfiguration($moloniConfig);

            $client = app(MoloniClient::class);

            return new Moloni();
        });

        $this->app->alias(Moloni::class, 'laravel-moloni');
    }
}
