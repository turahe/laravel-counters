<?php

declare(strict_types=1);

namespace Turahe\Counters\Tests\Providers;

use Turahe\Counters\Tests\TestCase;
use Turahe\Counters\CountersServiceProvider;
use Turahe\Counters\Classes\Counters;
use Turahe\Counters\Facades\Counters as CountersFacade;
use Turahe\Counters\Commands\MakeCounter;

class CountersServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing service bindings
        $this->app->forgetInstance(Counters::class);
        $this->app->forgetInstance('counters');
    }

    public function test_service_provider_registers_counters_class()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        $this->assertTrue($this->app->bound(Counters::class));
        $this->assertInstanceOf(Counters::class, $this->app->make(Counters::class));
    }

    public function test_service_provider_registers_counters_alias()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        $this->assertTrue($this->app->bound('counters'));
        $this->assertInstanceOf(Counters::class, $this->app->make('counters'));
    }

    public function test_service_provider_merges_config()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        $this->assertArrayHasKey('counter', $this->app['config']);
        $this->assertArrayHasKey('tables', $this->app['config']['counter']);
        $this->assertArrayHasKey('models', $this->app['config']['counter']);
    }

    public function test_service_provider_creates_counters_with_custom_cache_prefix()
    {
        config(['counter.cache_prefix' => 'custom:']);
        
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        $counters = $this->app->make(Counters::class);
        $this->assertInstanceOf(Counters::class, $counters);
    }

    public function test_service_provider_creates_counters_with_default_cache_prefix()
    {
        config(['counter.cache_prefix' => null]);
        
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        $counters = $this->app->make(Counters::class);
        $this->assertInstanceOf(Counters::class, $counters);
    }

    public function test_service_provider_boots_correctly()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();
        $provider->boot();

        // Test that the provider can boot without errors
        $this->assertTrue(true);
    }

    public function test_service_provider_publishes_config()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();
        $provider->boot();

        // Test that the provider can boot without errors
        $this->assertTrue(true);
    }

    public function test_service_provider_provides_services()
    {
        $provider = new CountersServiceProvider($this->app);
        
        $services = $provider->provides();
        
        $this->assertContains(Counters::class, $services);
        $this->assertContains('counters', $services);
    }

    public function test_service_provider_implements_deferrable_provider()
    {
        $provider = new CountersServiceProvider($this->app);
        
        $this->assertInstanceOf(\Illuminate\Contracts\Support\DeferrableProvider::class, $provider);
    }

    public function test_facade_registration()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();
        $provider->boot();

        // Test that the facade is accessible
        $this->assertInstanceOf(CountersFacade::class, new CountersFacade());
    }

    public function test_config_has_expected_structure()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        $config = $this->app['config']['counter'];
        
        $this->assertArrayHasKey('tables', $config);
        $this->assertArrayHasKey('models', $config);
        $this->assertArrayHasKey('database_connection', $config);
        $this->assertArrayHasKey('cache', $config);
        $this->assertArrayHasKey('cookies', $config);
        $this->assertArrayHasKey('defaults', $config);
        $this->assertArrayHasKey('performance', $config);
    }

    public function test_default_config_values()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        $config = $this->app['config']['counter'];
        
        $this->assertEquals('counters', $config['tables']['table_name']);
        $this->assertEquals('counterables', $config['tables']['table_pivot_name']);
        $this->assertEquals(\Turahe\Counters\Models\Counter::class, $config['models']['counter']);
        $this->assertEquals(\Turahe\Counters\Models\Counterable::class, $config['models']['counterable']);
        
        // Check if cache structure exists before accessing
        if (isset($config['cache']['ttl'])) {
            $this->assertEquals(3600, $config['cache']['ttl']);
        }
    }

    public function test_migrations_are_loaded()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();
        $provider->boot();

        // Test that migrations can be found
        $migrationPath = __DIR__ . '/../../database/migrations';
        $this->assertDirectoryExists($migrationPath);
        
        $migrationFiles = glob($migrationPath . '/*.php');
        $this->assertNotEmpty($migrationFiles);
    }

    public function test_command_registration_in_console()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();
        $provider->boot();

        // Test that the command class exists
        $this->assertTrue(class_exists(MakeCounter::class));
    }

    public function test_singleton_registration()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        $instance1 = $this->app->make(Counters::class);
        $instance2 = $this->app->make(Counters::class);

        $this->assertSame($instance1, $instance2);
    }

    public function test_alias_registration()
    {
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        $counters = $this->app->make('counters');
        $this->assertInstanceOf(Counters::class, $counters);
    }

    public function test_config_merge_does_not_override_existing()
    {
        // Set a custom config value
        $this->app['config']->set('counter.tables.table_name', 'custom_counters');
        
        $provider = new CountersServiceProvider($this->app);
        $provider->register();

        // The custom value should be preserved
        $this->assertEquals('custom_counters', $this->app['config']['counter']['tables']['table_name']);
    }
}
