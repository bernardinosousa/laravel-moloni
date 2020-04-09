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

        $this->app->bind(MoloniClient::class, function () {
            $moloniConfig = config('moloni');

            return MoloniClientFactory::createForConfig($moloniConfig);
        });

        $this->app->bind(Moloni::class, function () {
            $moloniConfig = config('moloni');

            $this->guardAgainstInvalidConfiguration($moloniConfig);

            $client = app(MoloniClient::class);

            return new Moloni($client, $moloniConfig['view_id']);
        });

        $this->app->alias(Moloni::class, 'laravel-moloni');
    }

    protected function guardAgainstInvalidConfiguration(array $moloniConfig = null)
    {
        if (empty($moloniConfig['view_id'])) {
            throw InvalidConfiguration::viewIdNotSpecified();
        }

        if (is_array($moloniConfig['service_account_credentials_json'])) {
            return;
        }

        if (!file_exists($moloniConfig['service_account_credentials_json'])) {
            throw InvalidConfiguration::credentialsJsonDoesNotExist($moloniConfig['service_account_credentials_json']);
        }
    }
}
