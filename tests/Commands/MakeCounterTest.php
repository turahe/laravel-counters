<?php

declare(strict_types=1);

namespace Turahe\Counters\Tests\Commands;

use Turahe\Counters\Tests\TestCase;
use Turahe\Counters\Commands\MakeCounter;
use Turahe\Counters\Classes\Counters;
use Turahe\Counters\Exceptions\CounterAlreadyExists;
use Turahe\Counters\Models\Counter;

class MakeCounterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing counters
        Counter::query()->delete();
    }

    public function test_make_counter_command_creates_counter_successfully()
    {
        $this->artisan(MakeCounter::class, [
            'key' => 'test_counter',
            'name' => 'Test Counter',
        ])->assertSuccessful();

        $this->assertDatabaseHas('counters', [
            'key' => 'test_counter',
            'name' => 'Test Counter',
            'initial_value' => 0,
            'step' => 1,
        ]);
    }

    public function test_make_counter_command_with_all_options()
    {
        $this->artisan(MakeCounter::class, [
            'key' => 'advanced_counter',
            'name' => 'Advanced Counter',
            '--initial-value' => 10,
            '--step' => 5,
            '--notes' => 'Test notes',
        ])->assertSuccessful();

        $this->assertDatabaseHas('counters', [
            'key' => 'advanced_counter',
            'name' => 'Advanced Counter',
            'initial_value' => 10,
            'step' => 5,
            'notes' => 'Test notes',
        ]);
    }

    public function test_make_counter_command_fails_without_key()
    {
        $this->expectException(\Symfony\Component\Console\Exception\RuntimeException::class);
        
        $this->artisan(MakeCounter::class, [
            'name' => 'Test Counter',
        ]);
    }

    public function test_make_counter_command_fails_without_name()
    {
        $this->expectException(\Symfony\Component\Console\Exception\RuntimeException::class);
        
        $this->artisan(MakeCounter::class, [
            'key' => 'test_counter',
        ]);
    }

    public function test_make_counter_command_fails_when_counter_already_exists()
    {
        // Create a counter first
        Counter::create([
            'key' => 'existing_counter',
            'name' => 'Existing Counter',
        ]);

        $this->artisan(MakeCounter::class, [
            'key' => 'existing_counter',
            'name' => 'Another Counter',
        ])->assertFailed();
    }

    public function test_make_counter_command_handles_exception_gracefully()
    {
        // Test that the command handles exceptions gracefully
        // We'll test this by creating a counter that already exists
        Counter::create(['key' => 'error_counter', 'name' => 'Error Counter']);
        
        $this->artisan(MakeCounter::class, [
            'key' => 'error_counter',
            'name' => 'Error Counter',
        ])->assertFailed();
    }

    public function test_make_counter_command_output_format()
    {
        $output = $this->artisan(MakeCounter::class, [
            'key' => 'output_test',
            'name' => 'Output Test',
            '--initial-value' => 25,
            '--step' => 3,
            '--notes' => 'Test output',
        ]);

        $output->expectsOutput("✅ Counter 'output_test' created successfully!");
    }

    public function test_make_counter_command_table_output()
    {
        $this->artisan(MakeCounter::class, [
            'key' => 'table_test',
            'name' => 'Table Test',
            '--initial-value' => 100,
            '--step' => 10,
        ])->assertSuccessful();

        $counter = Counter::where('key', 'table_test')->first();
        $this->assertNotNull($counter);
        $this->assertEquals(100, $counter->initial_value);
        $this->assertEquals(10, $counter->step);
    }

    public function test_make_counter_command_with_empty_notes()
    {
        $this->artisan(MakeCounter::class, [
            'key' => 'empty_notes',
            'name' => 'Empty Notes',
            '--notes' => '',
        ])->assertSuccessful();

        $counter = Counter::where('key', 'empty_notes')->first();
        $this->assertNotNull($counter);
        $this->assertEquals('', $counter->notes);
    }

    public function test_make_counter_command_with_negative_values()
    {
        $this->artisan(MakeCounter::class, [
            'key' => 'negative_test',
            'name' => 'Negative Test',
            '--initial-value' => -5,
            '--step' => -2,
        ])->assertSuccessful();

        $counter = Counter::where('key', 'negative_test')->first();
        $this->assertNotNull($counter);
        $this->assertEquals(-5, $counter->initial_value);
        $this->assertEquals(-2, $counter->step);
    }

    public function test_make_counter_command_with_large_values()
    {
        $this->artisan(MakeCounter::class, [
            'key' => 'large_test',
            'name' => 'Large Test',
            '--initial-value' => 999999,
            '--step' => 1000,
        ])->assertSuccessful();

        $counter = Counter::where('key', 'large_test')->first();
        $this->assertNotNull($counter);
        $this->assertEquals(999999, $counter->initial_value);
        $this->assertEquals(1000, $counter->step);
    }
}
