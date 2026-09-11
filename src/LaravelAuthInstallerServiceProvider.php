<?php

namespace LaravelAuth\Installer;

use Illuminate\Support\ServiceProvider;
use LaravelAuth\Installer\Commands\ImportAuthCommand;
use LaravelAuth\Installer\Commands\InstallAuthCommand;
use LaravelAuth\Installer\Services\AuthInstaller;
use LaravelAuth\Installer\Services\ConfigurationValidator;
use LaravelAuth\Installer\Services\InstallationClient;

class LaravelAuthInstallerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(InstallationClient::class);
        $this->app->singleton(ConfigurationValidator::class);
        $this->app->singleton(AuthInstaller::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportAuthCommand::class,
                InstallAuthCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../resources/stubs' => base_path('stubs/auth-installer'),
            ], 'auth-installer-stubs');
        }
    }
}
