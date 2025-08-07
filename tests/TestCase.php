<?php

declare(strict_types=1);

namespace Turahe\Counters\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Turahe\Counters\CountersServiceProvider;

/**
 * Optimized TestCase for PHP 8.4 and Laravel 11/12.
 */
abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    /**
     * Get package providers.
     */
    protected function getPackageProviders($app): array
    {
        return [
            CountersServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     */
    protected function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Setup counter configuration
        $app['config']->set('counter.cache.enabled', false);
        $app['config']->set('counter.tables.table_name', 'counters');
        $app['config']->set('counter.tables.table_pivot_name', 'counterables');
    }

    /**
     * Get package aliases.
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Counters' => \Turahe\Counters\Facades\Counters::class,
        ];
    }
}
