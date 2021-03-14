<?php

namespace Turahe\Counters;

use Illuminate\Foundation\AliasLoader;
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

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Counters', Counters::class);
        });


        $this->publishes([
            __DIR__.'/../database/migrations/0000_00_00_000000_create_counters_tables.php' => $this->app->databasePath().'/migrations/0000_00_00_000000_create_counters_tables.php',
        ], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([\Turahe\Counters\Commands\MakeCounter::class]);
        }
    }

}
