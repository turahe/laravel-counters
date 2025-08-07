<?php

declare(strict_types=1);

namespace Turahe\Counters\Tests;

use Turahe\Counters\Classes\Counters;
use Turahe\Counters\Exceptions\CounterAlreadyExists;
use Turahe\Counters\Exceptions\CounterDoesNotExist;
use Turahe\Counters\Models\Counter as CounterModel;

/**
 * Optimized CounterServiceTest for PHP 8.4 and Laravel 11/12.
 */
class CounterServiceTest extends TestCase
{
    private Counters $counters;

    protected function setUp(): void
    {
        parent::setUp();
        $this->counters = new Counters();
    }

    public function test_create_counter(): void
    {
        $counter = $this->counters->create('test_counter', 'Test Counter', 0, 1);

        $this->assertInstanceOf(CounterModel::class, $counter);
        $this->assertEquals('test_counter', $counter->key);
        $this->assertEquals('Test Counter', $counter->name);
        $this->assertEquals(0, $counter->value);
        $this->assertEquals(1, $counter->step);
    }

    public function test_create_counter_with_notes(): void
    {
        $counter = $this->counters->create(
            key: 'test_counter_with_notes',
            name: 'Test Counter with Notes',
            initialValue: 10,
            step: 2,
            notes: 'This is a test counter with notes'
        );

        $this->assertInstanceOf(CounterModel::class, $counter);
        $this->assertEquals('test_counter_with_notes', $counter->key);
        $this->assertEquals('This is a test counter with notes', $counter->notes);
        $this->assertEquals(10, $counter->value);
        $this->assertEquals(2, $counter->step);
    }

    public function test_cannot_create_counter_with_same_key(): void
    {
        $this->expectException(CounterAlreadyExists::class);

        $this->counters->create('duplicate_counter', 'Duplicate Counter');
        $this->counters->create('duplicate_counter', 'Another Counter');
    }

    public function test_can_get_counter_by_key(): void
    {
        $this->counters->create('test_get_counter', 'Test Get Counter');
        $counter = $this->counters->get('test_get_counter');
        
        $this->assertInstanceOf(CounterModel::class, $counter);
        $this->assertEquals('test_get_counter', $counter->key);
    }

    public function test_cannot_get_nonexistent_counter(): void
    {
        $this->expectException(CounterDoesNotExist::class);
        $this->counters->get('nonexistent_counter');
    }

    public function test_can_get_counter_value(): void
    {
        $this->counters->create('test_value_counter', 'Test Value Counter', 42);
        $value = $this->counters->getValue('test_value_counter');
        
        $this->assertEquals(42, $value);
    }

    public function test_get_value_with_default(): void
    {
        $value = $this->counters->getValue('nonexistent_counter', 100);
        $this->assertEquals(100, $value);
    }

    public function test_get_value_throws_exception_when_no_default(): void
    {
        $this->expectException(CounterDoesNotExist::class);
        $this->counters->getValue('nonexistent_counter');
    }

    public function test_can_set_counter_value(): void
    {
        $this->counters->create('test_set_value', 'Test Set Value');
        $result = $this->counters->setValue('test_set_value', 50);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('counters', [
            'key' => 'test_set_value',
            'value' => 50,
        ]);
    }

    public function test_can_set_counter_step(): void
    {
        $this->counters->create('test_set_step', 'Test Set Step');
        $result = $this->counters->setStep('test_set_step', 5);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('counters', [
            'key' => 'test_set_step',
            'step' => 5,
        ]);
    }

    public function test_can_increment_counter(): void
    {
        $this->counters->create('test_increment', 'Test Increment', 1);
        $result = $this->counters->increment('test_increment', 2);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('counters', [
            'key' => 'test_increment',
            'value' => 3,
        ]);
    }

    public function test_can_increment_counter_with_default_step(): void
    {
        $this->counters->create('test_increment_default', 'Test Increment Default', 1, 3);
        $result = $this->counters->increment('test_increment_default');
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('counters', [
            'key' => 'test_increment_default',
            'value' => 4,
        ]);
    }

    public function test_can_decrement_counter(): void
    {
        $this->counters->create('test_decrement', 'Test Decrement', 5);
        $result = $this->counters->decrement('test_decrement', 2);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('counters', [
            'key' => 'test_decrement',
            'value' => 3,
        ]);
    }

    public function test_can_decrement_counter_with_default_step(): void
    {
        $this->counters->create('test_decrement_default', 'Test Decrement Default', 5, 2);
        $result = $this->counters->decrement('test_decrement_default');
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('counters', [
            'key' => 'test_decrement_default',
            'value' => 3,
        ]);
    }

    public function test_can_reset_counter(): void
    {
        $this->counters->create('test_reset', 'Test Reset', 10);
        $this->counters->increment('test_reset', 5); // Value becomes 15
        
        $result = $this->counters->reset('test_reset');
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('counters', [
            'key' => 'test_reset',
            'value' => 10, // Back to initial value
        ]);
    }

    public function test_can_delete_counter(): void
    {
        $this->counters->create('test_delete', 'Test Delete');
        $result = $this->counters->delete('test_delete');
        
        $this->assertTrue($result);
        $this->assertDatabaseMissing('counters', [
            'key' => 'test_delete',
        ]);
    }

    public function test_can_get_all_counters(): void
    {
        $this->counters->create('counter1', 'Counter 1');
        $this->counters->create('counter2', 'Counter 2');
        $this->counters->create('counter3', 'Counter 3');
        
        $counters = $this->counters->getAll();
        
        $this->assertCount(3, $counters);
        $this->assertContains('counter1', $counters->pluck('key'));
        $this->assertContains('counter2', $counters->pluck('key'));
        $this->assertContains('counter3', $counters->pluck('key'));
    }

    public function test_can_search_counters(): void
    {
        $this->counters->create('test_search_1', 'Test Search Counter 1');
        $this->counters->create('test_search_2', 'Test Search Counter 2');
        $this->counters->create('other_counter', 'Other Counter');
        
        $counters = $this->counters->getAll('test_search');
        
        $this->assertCount(2, $counters);
        $this->assertContains('test_search_1', $counters->pluck('key'));
        $this->assertContains('test_search_2', $counters->pluck('key'));
    }

    public function test_can_bulk_increment_counters(): void
    {
        $this->counters->create('bulk1', 'Bulk Counter 1', 0);
        $this->counters->create('bulk2', 'Bulk Counter 2', 0);
        $this->counters->create('bulk3', 'Bulk Counter 3', 0);
        
        $results = $this->counters->bulkIncrement(['bulk1', 'bulk2', 'bulk3'], 2);
        
        $this->assertCount(3, $results);
        $this->assertTrue($results['bulk1']);
        $this->assertTrue($results['bulk2']);
        $this->assertTrue($results['bulk3']);
        
        $this->assertDatabaseHas('counters', ['key' => 'bulk1', 'value' => 2]);
        $this->assertDatabaseHas('counters', ['key' => 'bulk2', 'value' => 2]);
        $this->assertDatabaseHas('counters', ['key' => 'bulk3', 'value' => 2]);
    }

    public function test_can_bulk_decrement_counters(): void
    {
        $this->counters->create('bulk_dec1', 'Bulk Decrement 1', 10);
        $this->counters->create('bulk_dec2', 'Bulk Decrement 2', 10);
        
        $results = $this->counters->bulkDecrement(['bulk_dec1', 'bulk_dec2'], 3);
        
        $this->assertCount(2, $results);
        $this->assertTrue($results['bulk_dec1']);
        $this->assertTrue($results['bulk_dec2']);
        
        $this->assertDatabaseHas('counters', ['key' => 'bulk_dec1', 'value' => 7]);
        $this->assertDatabaseHas('counters', ['key' => 'bulk_dec2', 'value' => 7]);
    }

    public function test_can_get_stats(): void
    {
        $this->counters->create('stats1', 'Stats 1', 10);
        $this->counters->create('stats2', 'Stats 2', 20);
        $this->counters->create('stats3', 'Stats 3', 30);
        
        $stats = $this->counters->getStats();
        
        $this->assertArrayHasKey('total_counters', $stats);
        $this->assertArrayHasKey('total_value', $stats);
        $this->assertArrayHasKey('average_value', $stats);
        $this->assertArrayHasKey('max_value', $stats);
        $this->assertArrayHasKey('min_value', $stats);
        
        $this->assertEquals(3, $stats['total_counters']);
        $this->assertEquals(60, $stats['total_value']);
        $this->assertEquals(20.0, $stats['average_value']);
        $this->assertEquals(30, $stats['max_value']);
        $this->assertEquals(10, $stats['min_value']);
    }

    public function test_increment_if_not_has_cookies_increments_and_sets_cookie(): void
    {
        $this->counters->create('cookie_counter', 'Cookie Counter', 0, 1);
        
        // Simulate no cookie present
        $this->app['request']->cookies->remove('counters-cookie-cookie_counter');
        
        $result = $this->counters->incrementIfNotHasCookies('cookie_counter');
        $this->assertTrue($result);
        $this->assertDatabaseHas('counters', [
            'key' => 'cookie_counter',
            'value' => 1,
        ]);
        
        // Simulate cookie present
        $this->app['request']->cookies->set('counters-cookie-cookie_counter', '1');
        $result = $this->counters->incrementIfNotHasCookies('cookie_counter');
        $this->assertFalse($result);
    }

    public function test_decrement_if_not_has_cookies_decrements_and_sets_cookie(): void
    {
        $this->counters->create('cookie_counter_dec', 'Cookie Counter Dec', 2, 1);
        
        // Simulate no cookie present
        $this->app['request']->cookies->remove('counters-cookie-cookie_counter_dec');
        
        $result = $this->counters->decrementIfNotHasCookies('cookie_counter_dec');
        $this->assertTrue($result);
        $this->assertDatabaseHas('counters', [
            'key' => 'cookie_counter_dec',
            'value' => 1,
        ]);
        
        // Simulate cookie present
        $this->app['request']->cookies->set('counters-cookie-cookie_counter_dec', '1');
        $result = $this->counters->decrementIfNotHasCookies('cookie_counter_dec');
        $this->assertFalse($result);
    }
}
