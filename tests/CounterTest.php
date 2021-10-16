<?php

namespace Turahe\Counters\Tests;

use Turahe\Counters\Models\Counter;

class CounterTest extends TestCase
{
    /** @test */
    public function a_counter_can_seed_simple_test()
    {
        // create Counters
        //This will create a counter with inital value as 3, and every increment 5 will be added.
        $counter = Counter::create([
            'key' => 'number_of_downloads',
            'name' => 'Visitors',
            'initial_value' => 3,
            'step' => 5
        ]);

        $this->assertEquals('number_of_downloads', $counter->key);
        $this->assertEquals('Visitors', $counter->name);
        $this->assertEquals(3, $counter->initial_value);
        $this->assertEquals(5, $counter->step);
    }
    /** @test */
    public function a_create_name_key_only()
    {
        $counter = Counter::create([
            'key' => 'number_of_downloads',
            'name' => 'Visitors',
        ]);

        $this->assertEquals('number_of_downloads', $counter->key);
        $this->assertEquals('Visitors', $counter->name);
    }

}