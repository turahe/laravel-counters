<?php
namespace Turahe\Counters;

use Turahe\Counters\Facades\Counters;
use Illuminate\Support\ServiceProvider;

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
            __DIR__.'/../database/migrations/0000_00_00_000000_create_counters_tables.php' => $this->app->databasePath().'/migrations/0000_00_00_000000_create_counters_tables.php',
        ], 'migrations');

        if ($this->app instanceof \Illuminate\Foundation\Application) {
            $databasePath = __DIR__ . '/../database/migrations';
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
            return new Counters();
        });
    }
}
