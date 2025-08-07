<?php

declare(strict_types=1);

namespace Turahe\Counters\Tests\Traits;

use Turahe\Counters\Tests\TestCase;
use Turahe\Counters\Models\Counter;
use Turahe\Counters\Tests\Models\Post;
use Turahe\Counters\Facades\Counters;

class HasCounterTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing counters
        Counter::query()->delete();
    }

    public function test_counters_relationship()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'test_counter', 'name' => 'Test Counter']);

        $post->counters()->attach($counter->id, ['value' => 10]);

        $this->assertCount(1, $post->counters);
        $this->assertEquals('test_counter', $post->counters->first()->key);
    }

    public function test_get_counter_returns_existing_counter()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'existing_counter', 'name' => 'Existing Counter']);
        
        $post->counters()->attach($counter->id, ['value' => 15]);

        $result = $post->getCounter('existing_counter');
        
        $this->assertNotNull($result);
        $this->assertEquals('existing_counter', $result->key);
    }

    public function test_get_counter_creates_new_relationship_when_not_exists()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'new_counter', 'name' => 'New Counter']);

        $result = $post->getCounter('new_counter');
        
        $this->assertNotNull($result);
        $this->assertEquals('new_counter', $result->key);
        $this->assertDatabaseHas(config('counter.tables.table_pivot_name'), [
            'counterable_id' => $post->getKey(),
            'counterable_type' => $post->getMorphClass(),
            'counter_id' => $counter->getKey(),
        ]);
    }

    public function test_has_counter_returns_true_when_exists()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'has_test', 'name' => 'Has Test']);
        
        $post->counters()->attach($counter->id, ['value' => 5]);

        $this->assertTrue($post->hasCounter('has_test'));
    }

    public function test_has_counter_returns_false_when_not_exists()
    {
        $post = Post::create(['name' => 'Test Post']);

        $this->assertFalse($post->hasCounter('non_existent'));
    }

    public function test_get_counter_value_returns_correct_value()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'value_test', 'name' => 'Value Test']);
        
        $post->counters()->attach($counter->id, ['value' => 25]);

        $this->assertEquals(25, $post->getCounterValue('value_test'));
    }

    public function test_get_counter_value_returns_zero_when_not_exists()
    {
        $post = Post::create(['name' => 'Test Post']);
        
        // Create the counter but don't attach it to the post
        Counter::create(['key' => 'non_existent', 'name' => 'Non Existent']);

        $this->assertEquals(0, $post->getCounterValue('non_existent'));
    }

    public function test_add_counter_creates_relationship()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'add_test', 'name' => 'Add Test', 'initial_value' => 10]);

        $post->addCounter('add_test');

        $this->assertDatabaseHas(config('counter.tables.table_pivot_name'), [
            'counterable_id' => $post->getKey(),
            'counterable_type' => $post->getMorphClass(),
            'counter_id' => $counter->getKey(),
            'value' => 10,
        ]);
    }

    public function test_add_counter_with_custom_initial_value()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'custom_value', 'name' => 'Custom Value', 'initial_value' => 5]);

        $post->addCounter('custom_value', 20);

        $this->assertDatabaseHas(config('counter.tables.table_pivot_name'), [
            'counterable_id' => $post->getKey(),
            'counterable_type' => $post->getMorphClass(),
            'counter_id' => $counter->getKey(),
            'value' => 20,
        ]);
    }

    public function test_remove_counter_removes_relationship()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'remove_test', 'name' => 'Remove Test']);
        
        $post->counters()->attach($counter->id, ['value' => 10]);

        $result = $post->removeCounter('remove_test');
        
        $this->assertTrue($result);
        $this->assertDatabaseMissing(config('counter.tables.table_pivot_name'), [
            'counterable_id' => $post->getKey(),
            'counterable_type' => $post->getMorphClass(),
            'counter_id' => $counter->getKey(),
        ]);
    }

    public function test_remove_counter_returns_false_when_not_exists()
    {
        $post = Post::create(['name' => 'Test Post']);

        $result = $post->removeCounter('non_existent');
        
        $this->assertFalse($result);
    }

    public function test_increment_counter()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'increment_test', 'name' => 'Increment Test', 'step' => 3]);
        
        $post->counters()->attach($counter->id, ['value' => 10]);

        $result = $post->incrementCounter('increment_test');
        
        $this->assertTrue($result);
        $this->assertEquals(13, $post->getCounterValue('increment_test'));
    }

    public function test_increment_counter_with_custom_step()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'custom_increment', 'name' => 'Custom Increment', 'step' => 2]);
        
        $post->counters()->attach($counter->id, ['value' => 10]);

        $result = $post->incrementCounter('custom_increment', 5);
        
        $this->assertTrue($result);
        $this->assertEquals(15, $post->getCounterValue('custom_increment'));
    }

    public function test_increment_counter_returns_false_when_not_exists()
    {
        $post = Post::create(['name' => 'Test Post']);

        $result = $post->incrementCounter('non_existent');
        
        $this->assertFalse($result);
    }

    public function test_decrement_counter()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'decrement_test', 'name' => 'Decrement Test', 'step' => 2]);
        
        $post->counters()->attach($counter->id, ['value' => 10]);

        $result = $post->decrementCounter('decrement_test');
        
        $this->assertTrue($result);
        $this->assertEquals(8, $post->getCounterValue('decrement_test'));
    }

    public function test_decrement_counter_with_custom_step()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'custom_decrement', 'name' => 'Custom Decrement', 'step' => 1]);
        
        $post->counters()->attach($counter->id, ['value' => 10]);

        $result = $post->decrementCounter('custom_decrement', 3);
        
        $this->assertTrue($result);
        $this->assertEquals(7, $post->getCounterValue('custom_decrement'));
    }

    public function test_reset_counter()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'reset_test', 'name' => 'Reset Test', 'initial_value' => 5]);
        
        $post->counters()->attach($counter->id, ['value' => 20]);

        $result = $post->resetCounter('reset_test');
        
        $this->assertTrue($result);
        $this->assertEquals(5, $post->getCounterValue('reset_test'));
    }

    public function test_reset_counter_with_custom_value()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'custom_reset', 'name' => 'Custom Reset', 'initial_value' => 10]);
        
        $post->counters()->attach($counter->id, ['value' => 30]);

        $result = $post->resetCounter('custom_reset', 15);
        
        $this->assertTrue($result);
        $this->assertEquals(15, $post->getCounterValue('custom_reset'));
    }

    public function test_set_counter_value()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter = Counter::create(['key' => 'set_value_test', 'name' => 'Set Value Test']);
        
        $post->counters()->attach($counter->id, ['value' => 10]);

        $result = $post->setCounterValue('set_value_test', 50);
        
        $this->assertTrue($result);
        $this->assertEquals(50, $post->getCounterValue('set_value_test'));
    }

    public function test_get_all_counters()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'counter1', 'name' => 'Counter 1']);
        $counter2 = Counter::create(['key' => 'counter2', 'name' => 'Counter 2']);
        
        $post->counters()->attach($counter1->id, ['value' => 10]);
        $post->counters()->attach($counter2->id, ['value' => 20]);

        $counters = $post->getAllCounters();
        
        $this->assertCount(2, $counters);
        $this->assertTrue($counters->contains('key', 'counter1'));
        $this->assertTrue($counters->contains('key', 'counter2'));
    }

    public function test_get_counters_with_value_greater_than()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'low', 'name' => 'Low']);
        $counter2 = Counter::create(['key' => 'high', 'name' => 'High']);
        
        $post->counters()->attach($counter1->id, ['value' => 5]);
        $post->counters()->attach($counter2->id, ['value' => 15]);

        $counters = $post->getCountersWithValueGreaterThan(10);
        
        $this->assertCount(1, $counters);
        $this->assertEquals('high', $counters->first()->key);
    }

    public function test_get_counters_with_value_less_than()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'low', 'name' => 'Low']);
        $counter2 = Counter::create(['key' => 'high', 'name' => 'High']);
        
        $post->counters()->attach($counter1->id, ['value' => 5]);
        $post->counters()->attach($counter2->id, ['value' => 15]);

        $counters = $post->getCountersWithValueLessThan(10);
        
        $this->assertCount(1, $counters);
        $this->assertEquals('low', $counters->first()->key);
    }

    public function test_get_active_counters()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'active', 'name' => 'Active']);
        $counter2 = Counter::create(['key' => 'inactive', 'name' => 'Inactive']);
        
        $post->counters()->attach($counter1->id, ['value' => 10]);
        $post->counters()->attach($counter2->id, ['value' => 0]);

        $counters = $post->getActiveCounters();
        
        $this->assertCount(1, $counters);
        $this->assertEquals('active', $counters->first()->key);
    }

    public function test_get_inactive_counters()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'active', 'name' => 'Active']);
        $counter2 = Counter::create(['key' => 'inactive', 'name' => 'Inactive']);
        
        $post->counters()->attach($counter1->id, ['value' => 10]);
        $post->counters()->attach($counter2->id, ['value' => 0]);

        $counters = $post->getInactiveCounters();
        
        $this->assertCount(1, $counters);
        $this->assertEquals('inactive', $counters->first()->key);
    }

    public function test_get_total_counter_value()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'counter1', 'name' => 'Counter 1']);
        $counter2 = Counter::create(['key' => 'counter2', 'name' => 'Counter 2']);
        
        $post->counters()->attach($counter1->id, ['value' => 10]);
        $post->counters()->attach($counter2->id, ['value' => 20]);

        $total = $post->getTotalCounterValue();
        
        $this->assertEquals(30, $total);
    }

    public function test_get_average_counter_value()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'counter1', 'name' => 'Counter 1']);
        $counter2 = Counter::create(['key' => 'counter2', 'name' => 'Counter 2']);
        
        $post->counters()->attach($counter1->id, ['value' => 10]);
        $post->counters()->attach($counter2->id, ['value' => 20]);

        $average = $post->getAverageCounterValue();
        
        $this->assertEquals(15.0, $average);
    }

    public function test_bulk_increment_counters()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'bulk1', 'name' => 'Bulk 1', 'step' => 2]);
        $counter2 = Counter::create(['key' => 'bulk2', 'name' => 'Bulk 2', 'step' => 3]);
        
        $post->counters()->attach($counter1->id, ['value' => 10]);
        $post->counters()->attach($counter2->id, ['value' => 15]);

        $results = $post->bulkIncrementCounters(['bulk1', 'bulk2']);
        
        $this->assertTrue($results['bulk1']);
        $this->assertTrue($results['bulk2']);
        $this->assertEquals(12, $post->getCounterValue('bulk1'));
        $this->assertEquals(18, $post->getCounterValue('bulk2'));
    }

    public function test_bulk_decrement_counters()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'bulk1', 'name' => 'Bulk 1', 'step' => 2]);
        $counter2 = Counter::create(['key' => 'bulk2', 'name' => 'Bulk 2', 'step' => 3]);
        
        $post->counters()->attach($counter1->id, ['value' => 10]);
        $post->counters()->attach($counter2->id, ['value' => 15]);

        $results = $post->bulkDecrementCounters(['bulk1', 'bulk2']);
        
        $this->assertTrue($results['bulk1']);
        $this->assertTrue($results['bulk2']);
        $this->assertEquals(8, $post->getCounterValue('bulk1'));
        $this->assertEquals(12, $post->getCounterValue('bulk2'));
    }

    public function test_bulk_reset_counters()
    {
        $post = Post::create(['name' => 'Test Post']);
        $counter1 = Counter::create(['key' => 'bulk1', 'name' => 'Bulk 1', 'initial_value' => 5]);
        $counter2 = Counter::create(['key' => 'bulk2', 'name' => 'Bulk 2', 'initial_value' => 10]);
        
        $post->counters()->attach($counter1->id, ['value' => 20]);
        $post->counters()->attach($counter2->id, ['value' => 30]);

        $results = $post->bulkResetCounters(['bulk1', 'bulk2']);
        
        $this->assertTrue($results['bulk1']);
        $this->assertTrue($results['bulk2']);
        $this->assertEquals(5, $post->getCounterValue('bulk1'));
        $this->assertEquals(10, $post->getCounterValue('bulk2'));
    }

    public function test_add_counter_throws_exception_when_counter_not_found()
    {
        $post = Post::create(['name' => 'Test Post']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Failed to add counter 'non_existent' to model");

        $post->addCounter('non_existent');
    }
}
