<?php

namespace Turahe\Counters;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Turahe\Counters\Facades\Counters;

class CountersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/0000_00_00_000000_create_counters_tables.php' => $this->getMigrationFileName('create_permission_tables.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../config/counter.php' => $this->app->configPath('counter.php'),
        ], 'config');

        if ($this->app instanceof \Illuminate\Foundation\Application) {
            $databasePath = __DIR__.'/../database/migrations';
            $this->loadMigrationsFrom($databasePath);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([Commands\MakeCounter::class]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/counter.php', 'counter');
        $this->app->singleton('Counter', function ($app) {
            return new Counters;
        });
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR])
            ->flatMap(fn ($path) => $filesystem->glob($path.'*_'.$migrationFileName))
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
