<?php

namespace Turahe\Counters;

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
            __DIR__.'/../config/counter.php' => $this->app->configPath('counter.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

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
}
