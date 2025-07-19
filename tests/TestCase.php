<?php

namespace Turahe\Counters\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->setUpDatabase();
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [\Turahe\Counters\CountersServiceProvider::class];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('app.key', 'base64:MFOsOH9RomiI2LRdgP4hIeoQJ5nyBhdABdH77UY2zi8=');
        
        // Set counters configuration for tests
        $app['config']->set('counters.tables.table_name', 'counters');
        $app['config']->set('counters.tables.table_pivot_name', 'counterables');
    }

    protected function setUpDatabase()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }
}
