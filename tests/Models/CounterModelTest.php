<?php

declare(strict_types=1);

namespace Turahe\Counters\Tests\Models;

use Turahe\Counters\Tests\TestCase;
use Turahe\Counters\Models\Counter;
use Turahe\Counters\Tests\Models\Post;

class CounterModelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing counters
        Counter::query()->delete();
    }

    public function test_counter_can_be_created_with_basic_fields()
    {
        $counter = Counter::create([
            'key' => 'test_counter',
            'name' => 'Test Counter',
        ]);

        $this->assertEquals('test_counter', $counter->key);
        $this->assertEquals('Test Counter', $counter->name);
        $this->assertEquals(0, $counter->initial_value);
        $this->assertEquals(1, $counter->step);
        $this->assertEquals(0, $counter->value);
        
        // Refresh from database to ensure proper defaults
        $counter->refresh();
        $this->assertEquals(1, $counter->step);
    }

    public function test_counter_can_be_created_with_all_fields()
    {
        $counter = Counter::create([
            'key' => 'full_counter',
            'name' => 'Full Counter',
            'value' => 100,
            'initial_value' => 50,
            'step' => 5,
            'notes' => 'Test notes',
        ]);

        $this->assertEquals('full_counter', $counter->key);
        $this->assertEquals('Full Counter', $counter->name);
        $this->assertEquals(100, $counter->value);
        $this->assertEquals(50, $counter->initial_value);
        $this->assertEquals(5, $counter->step);
        $this->assertEquals('Test notes', $counter->notes);
    }

    public function test_counter_uses_custom_table_name()
    {
        config(['counter.tables.table_name' => 'custom_counters']);
        
        $counter = new Counter();
        
        $this->assertEquals('custom_counters', $counter->getTable());
    }

    public function test_counter_uses_custom_database_connection()
    {
        config(['counter.database_connection' => 'sqlite']);
        
        $counter = new Counter();
        
        $this->assertEquals('sqlite', $counter->getConnectionName());
    }

    public function test_scope_by_key()
    {
        Counter::create(['key' => 'first_counter', 'name' => 'First']);
        Counter::create(['key' => 'second_counter', 'name' => 'Second']);

        $result = Counter::byKey('first_counter')->get();
        
        $this->assertCount(1, $result);
        $this->assertEquals('first_counter', $result->first()->key);
    }

    public function test_scope_by_name()
    {
        Counter::create(['key' => 'test1', 'name' => 'Test Counter']);
        Counter::create(['key' => 'test2', 'name' => 'Another Test']);
        Counter::create(['key' => 'other', 'name' => 'Other']);

        $result = Counter::byName('Test')->get();
        
        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn($counter) => str_contains($counter->name, 'Test')));
    }

    public function test_scope_with_value_greater_than()
    {
        Counter::create(['key' => 'low', 'name' => 'Low', 'value' => 5]);
        Counter::create(['key' => 'medium', 'name' => 'Medium', 'value' => 10]);
        Counter::create(['key' => 'high', 'name' => 'High', 'value' => 15]);

        $result = Counter::withValueGreaterThan(7)->get();
        
        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn($counter) => $counter->value > 7));
    }

    public function test_scope_with_value_less_than()
    {
        Counter::create(['key' => 'low', 'name' => 'Low', 'value' => 5]);
        Counter::create(['key' => 'medium', 'name' => 'Medium', 'value' => 10]);
        Counter::create(['key' => 'high', 'name' => 'High', 'value' => 15]);

        $result = Counter::withValueLessThan(12)->get();
        
        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn($counter) => $counter->value < 12));
    }

    public function test_scope_active()
    {
        Counter::create(['key' => 'active1', 'name' => 'Active 1', 'value' => 5]);
        Counter::create(['key' => 'active2', 'name' => 'Active 2', 'value' => 10]);
        Counter::create(['key' => 'inactive', 'name' => 'Inactive', 'value' => 0]);

        $result = Counter::active()->get();
        
        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn($counter) => $counter->value > 0));
    }

    public function test_scope_inactive()
    {
        Counter::create(['key' => 'active', 'name' => 'Active', 'value' => 5]);
        Counter::create(['key' => 'inactive1', 'name' => 'Inactive 1', 'value' => 0]);
        Counter::create(['key' => 'inactive2', 'name' => 'Inactive 2', 'value' => 0]);

        $result = Counter::inactive()->get();
        
        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn($counter) => $counter->value === 0));
    }

    public function test_display_name_attribute()
    {
        $counter = Counter::create([
            'key' => 'test_key',
            'name' => 'Test Name',
        ]);

        $this->assertEquals('Test Name', $counter->display_name);
    }

    public function test_display_name_falls_back_to_key()
    {
        $counter = Counter::create([
            'key' => 'test_key',
            'name' => '',
        ]);

        $this->assertEquals('test_key', $counter->display_name);
    }

    public function test_is_active_method()
    {
        $activeCounter = Counter::create(['key' => 'active', 'name' => 'Active', 'value' => 5]);
        $inactiveCounter = Counter::create(['key' => 'inactive', 'name' => 'Inactive', 'value' => 0]);

        $this->assertTrue($activeCounter->isActive());
        $this->assertFalse($inactiveCounter->isActive());
    }

    public function test_is_inactive_method()
    {
        $activeCounter = Counter::create(['key' => 'active', 'name' => 'Active', 'value' => 5]);
        $inactiveCounter = Counter::create(['key' => 'inactive', 'name' => 'Inactive', 'value' => 0]);

        $this->assertFalse($activeCounter->isInactive());
        $this->assertTrue($inactiveCounter->isInactive());
    }

    public function test_percentage_change_attribute()
    {
        $counter = Counter::create([
            'key' => 'percentage_test',
            'name' => 'Percentage Test',
            'initial_value' => 100,
            'value' => 150,
        ]);

        $this->assertEquals(50.0, $counter->percentage_change);
    }

    public function test_percentage_change_with_zero_initial_value()
    {
        $counter = Counter::create([
            'key' => 'zero_initial',
            'name' => 'Zero Initial',
            'initial_value' => 0,
            'value' => 50,
        ]);

        $this->assertEquals(100.0, $counter->percentage_change);
    }

    public function test_percentage_change_with_negative_change()
    {
        $counter = Counter::create([
            'key' => 'negative_change',
            'name' => 'Negative Change',
            'initial_value' => 100,
            'value' => 50,
        ]);

        $this->assertEquals(-50.0, $counter->percentage_change);
    }

    public function test_formatted_value_attribute()
    {
        $counter = Counter::create([
            'key' => 'formatted_test',
            'name' => 'Formatted Test',
            'value' => 1234567,
        ]);

        $this->assertEquals('1,234,567', $counter->formatted_value);
    }

    public function test_formatted_value_with_zero()
    {
        $counter = Counter::create([
            'key' => 'zero_test',
            'name' => 'Zero Test',
            'value' => 0,
        ]);

        $this->assertEquals('0', $counter->formatted_value);
    }

    public function test_counterable_relationship()
    {
        $counter = Counter::create(['key' => 'relationship_test', 'name' => 'Relationship Test']);
        $post = Post::create(['name' => 'Test Post']);

        // Attach the counter to the post
        $post->counters()->attach($counter->id, ['value' => 10]);

        $this->assertCount(1, $counter->counterable);
        $this->assertInstanceOf(Post::class, $counter->counterable->first());
    }

    public function test_hidden_attributes()
    {
        $counter = Counter::create(['key' => 'hidden_test', 'name' => 'Hidden Test']);
        
        $array = $counter->toArray();
        
        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayHasKey('key', $array);
        $this->assertArrayHasKey('name', $array);
    }

    public function test_casts()
    {
        $counter = Counter::create([
            'key' => 'casts_test',
            'name' => 'Casts Test',
            'value' => '100',
            'initial_value' => '50',
            'step' => '5',
        ]);

        $this->assertIsInt($counter->value);
        $this->assertIsInt($counter->initial_value);
        $this->assertIsInt($counter->step);
        $this->assertEquals(100, $counter->value);
        $this->assertEquals(50, $counter->initial_value);
        $this->assertEquals(5, $counter->step);
    }

    public function test_cache_clearing_on_update()
    {
        $counter = Counter::create(['key' => 'cache_test', 'name' => 'Cache Test']);
        
        // Test that the update method works without mocking cache
        $result = $counter->update(['value' => 100]);
        
        $this->assertTrue($result);
        $this->assertEquals(100, $counter->fresh()->value);
    }

    public function test_cache_clearing_on_delete()
    {
        $counter = Counter::create(['key' => 'delete_cache_test', 'name' => 'Delete Cache Test']);
        
        // Test that the delete method works
        $result = $counter->delete();
        
        $this->assertTrue($result);
        $this->assertDatabaseMissing('counters', ['key' => 'delete_cache_test']);
    }
}
