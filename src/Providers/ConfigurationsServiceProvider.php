<?php

namespace Credpal\Configurations\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ConfigurationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/configurations.php' => config_path('configurations.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        Route::group(
            $this->getRouteGroupAttributes(),
            fn () => $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php')
        );
    }

    protected function getRouteGroupAttributes(): array
    {
        return config('configurations.route', []);
    }
}
