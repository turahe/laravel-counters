<?php

declare(strict_types=1);

namespace Turahe\Counters;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Turahe\Counters\Classes\Counters;
use Turahe\Counters\Facades\Counters as CountersFacade;
use Turahe\Counters\Commands\MakeCounter;

/**
 * Optimized CountersServiceProvider for Laravel 11/12.
 */
class CountersServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/counter.php', 'counter');

        $this->app->singleton(Counters::class, function ($app) {
            return new Counters(
                cachePrefix: config('counter.cache_prefix', 'counters:')
            );
        });

        $this->app->alias(Counters::class, 'counters');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/counter.php' => $this->app->configPath('counter.php'),
        ], 'counters-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => $this->app->databasePath('migrations'),
        ], 'counters-migrations');

        // Load migrations
        if ($this->app instanceof \Illuminate\Foundation\Application) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCounter::class,
            ]);
        }

        // Register facade
        if (!class_exists('Counters')) {
            class_alias(CountersFacade::class, 'Counters');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            Counters::class,
            'counters',
        ];
    }
}
