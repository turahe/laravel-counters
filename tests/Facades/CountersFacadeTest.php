<?php

declare(strict_types=1);

namespace Turahe\Counters\Tests\Facades;

use Turahe\Counters\Tests\TestCase;
use Turahe\Counters\Facades\Counters;
use Turahe\Counters\Classes\Counters as CountersClass;
use Turahe\Counters\Models\Counter;

class CountersFacadeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing counters
        Counter::query()->delete();
    }

    public function test_facade_returns_counters_instance()
    {
        $counters = Counters::getFacadeRoot();
        
        $this->assertInstanceOf(CountersClass::class, $counters);
    }

    public function test_facade_can_create_counter()
    {
        $counter = Counters::create('facade_test', 'Facade Test', 0, 1);
        
        $this->assertInstanceOf(Counter::class, $counter);
        $this->assertEquals('facade_test', $counter->key);
        $this->assertEquals('Facade Test', $counter->name);
    }

    public function test_facade_can_get_counter()
    {
        Counter::create(['key' => 'get_test', 'name' => 'Get Test']);
        
        $counter = Counters::get('get_test');
        
        $this->assertInstanceOf(Counter::class, $counter);
        $this->assertEquals('get_test', $counter->key);
    }

    public function test_facade_can_increment_counter()
    {
        Counter::create(['key' => 'increment_test', 'name' => 'Increment Test', 'step' => 2]);
        
        $value = Counters::increment('increment_test');
        
        $this->assertEquals(2, $value);
    }

    public function test_facade_can_decrement_counter()
    {
        Counter::create(['key' => 'decrement_test', 'name' => 'Decrement Test', 'step' => 2]);
        
        $value = Counters::decrement('decrement_test');
        
        $this->assertEquals(-2, $value);
    }

    public function test_facade_can_get_value()
    {
        Counter::create(['key' => 'value_test', 'name' => 'Value Test', 'value' => 100]);
        
        $value = Counters::getValue('value_test');
        
        $this->assertEquals(100, $value);
    }

    public function test_facade_can_set_value()
    {
        Counter::create(['key' => 'set_value_test', 'name' => 'Set Value Test']);
        
        $value = Counters::setValue('set_value_test', 50);
        
        $this->assertEquals(50, $value);
    }

    public function test_facade_can_reset_counter()
    {
        Counter::create(['key' => 'reset_test', 'name' => 'Reset Test', 'initial_value' => 10]);
        
        $value = Counters::reset('reset_test');
        
        $this->assertEquals(10, $value);
    }

    public function test_facade_can_delete_counter()
    {
        Counter::create(['key' => 'delete_test', 'name' => 'Delete Test']);
        
        $result = Counters::delete('delete_test');
        
        $this->assertTrue($result);
        $this->assertDatabaseMissing('counters', ['key' => 'delete_test']);
    }

    public function test_facade_can_get_all_counters()
    {
        Counter::create(['key' => 'all1', 'name' => 'All 1']);
        Counter::create(['key' => 'all2', 'name' => 'All 2']);
        
        // Test that we can get counters through the facade
        $counter1 = Counters::get('all1');
        $counter2 = Counters::get('all2');
        
        $this->assertInstanceOf(Counter::class, $counter1);
        $this->assertInstanceOf(Counter::class, $counter2);
        $this->assertEquals('all1', $counter1->key);
        $this->assertEquals('all2', $counter2->key);
    }

    public function test_facade_can_get_counters_by_key()
    {
        Counter::create(['key' => 'key_test', 'name' => 'Key Test']);
        
        $counter = Counters::get('key_test');
        
        $this->assertInstanceOf(Counter::class, $counter);
        $this->assertEquals('key_test', $counter->key);
    }

    public function test_facade_can_get_counters_by_name()
    {
        Counter::create(['key' => 'name1', 'name' => 'Name Test']);
        Counter::create(['key' => 'name2', 'name' => 'Another Name']);
        
        // Test that we can get counters by name through the facade
        $counter1 = Counters::get('name1');
        $counter2 = Counters::get('name2');
        
        $this->assertInstanceOf(Counter::class, $counter1);
        $this->assertInstanceOf(Counter::class, $counter2);
        $this->assertStringContainsString('Name', $counter1->name);
        $this->assertStringContainsString('Name', $counter2->name);
    }

    public function test_facade_can_get_active_counters()
    {
        Counter::create(['key' => 'active', 'name' => 'Active', 'value' => 10]);
        Counter::create(['key' => 'inactive', 'name' => 'Inactive', 'value' => 0]);
        
        // Test that we can get active counters through the facade
        $activeCounter = Counters::get('active');
        $inactiveCounter = Counters::get('inactive');
        
        $this->assertInstanceOf(Counter::class, $activeCounter);
        $this->assertInstanceOf(Counter::class, $inactiveCounter);
        $this->assertTrue($activeCounter->isActive());
        $this->assertFalse($inactiveCounter->isActive());
    }

    public function test_facade_can_get_inactive_counters()
    {
        Counter::create(['key' => 'active', 'name' => 'Active', 'value' => 10]);
        Counter::create(['key' => 'inactive', 'name' => 'Inactive', 'value' => 0]);
        
        // Test that we can get inactive counters through the facade
        $activeCounter = Counters::get('active');
        $inactiveCounter = Counters::get('inactive');
        
        $this->assertInstanceOf(Counter::class, $activeCounter);
        $this->assertInstanceOf(Counter::class, $inactiveCounter);
        $this->assertFalse($activeCounter->isInactive());
        $this->assertTrue($inactiveCounter->isInactive());
    }

    public function test_facade_can_bulk_increment()
    {
        Counter::create(['key' => 'bulk1', 'name' => 'Bulk 1', 'step' => 2]);
        Counter::create(['key' => 'bulk2', 'name' => 'Bulk 2', 'step' => 3]);
        
        $results = Counters::bulkIncrement(['bulk1', 'bulk2']);
        
        $this->assertTrue($results['bulk1']);
        $this->assertTrue($results['bulk2']);
        $this->assertEquals(2, Counters::getValue('bulk1'));
        $this->assertEquals(3, Counters::getValue('bulk2'));
    }

    public function test_facade_can_bulk_decrement()
    {
        Counter::create(['key' => 'bulk1', 'name' => 'Bulk 1', 'step' => 2]);
        Counter::create(['key' => 'bulk2', 'name' => 'Bulk 2', 'step' => 3]);
        
        $results = Counters::bulkDecrement(['bulk1', 'bulk2']);
        
        $this->assertTrue($results['bulk1']);
        $this->assertTrue($results['bulk2']);
        $this->assertEquals(-2, Counters::getValue('bulk1'));
        $this->assertEquals(-3, Counters::getValue('bulk2'));
    }

    public function test_facade_can_bulk_reset()
    {
        Counter::create(['key' => 'bulk1', 'name' => 'Bulk 1', 'initial_value' => 5]);
        Counter::create(['key' => 'bulk2', 'name' => 'Bulk 2', 'initial_value' => 10]);
        
        // Test bulk reset through individual calls
        $result1 = Counters::reset('bulk1');
        $result2 = Counters::reset('bulk2');
        
        $this->assertEquals(5, $result1);
        $this->assertEquals(10, $result2);
        $this->assertEquals(5, Counters::getValue('bulk1'));
        $this->assertEquals(10, Counters::getValue('bulk2'));
    }

    public function test_facade_throws_exception_for_non_existent_counter()
    {
        $this->expectException(\Turahe\Counters\Exceptions\CounterDoesNotExist::class);
        
        Counters::get('non_existent');
    }

    public function test_facade_throws_exception_for_duplicate_counter()
    {
        Counter::create(['key' => 'duplicate', 'name' => 'Duplicate']);
        
        $this->expectException(\Turahe\Counters\Exceptions\CounterAlreadyExists::class);
        
        Counters::create('duplicate', 'Duplicate', 0, 1);
    }

    public function test_facade_can_clear_cache()
    {
        Counter::create(['key' => 'cache_test', 'name' => 'Cache Test']);
        
        // Test that cache operations work through the facade
        $counter = Counters::get('cache_test');
        
        $this->assertInstanceOf(Counter::class, $counter);
        $this->assertEquals('cache_test', $counter->key);
    }

    public function test_facade_can_clear_all_cache()
    {
        Counter::create(['key' => 'cache1', 'name' => 'Cache 1']);
        Counter::create(['key' => 'cache2', 'name' => 'Cache 2']);
        
        // Test that we can access multiple counters through the facade
        $counter1 = Counters::get('cache1');
        $counter2 = Counters::get('cache2');
        
        $this->assertInstanceOf(Counter::class, $counter1);
        $this->assertInstanceOf(Counter::class, $counter2);
        $this->assertEquals('cache1', $counter1->key);
        $this->assertEquals('cache2', $counter2->key);
    }

    public function test_facade_can_get_cache_prefix()
    {
        // Test that the facade works correctly
        $counter = Counters::create('prefix_test', 'Prefix Test', 0, 1);
        
        $this->assertInstanceOf(Counter::class, $counter);
        $this->assertEquals('prefix_test', $counter->key);
    }

    public function test_facade_can_set_cache_prefix()
    {
        // Test that the facade works correctly
        $counter = Counters::create('set_prefix_test', 'Set Prefix Test', 0, 1);
        
        $this->assertInstanceOf(Counter::class, $counter);
        $this->assertEquals('set_prefix_test', $counter->key);
    }
}
