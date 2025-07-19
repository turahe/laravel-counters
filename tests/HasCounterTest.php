<?php

namespace Turahe\Counters\Tests;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Turahe\Counters\Models\Counter;
use Turahe\Counters\Tests\Models\Post;

class HasCounterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->counter = Counter::create([
            'key' => 'number_of_downloads',
            'name' => 'Downloads',
            'initial_value' => 0,
            'step' => 1,
        ]);

        $this->testModel = Post::create(['name' => 'Test Post']);
    }

    public function test_provides_a_counters_relation(): void
    {
        $this->assertInstanceOf(MorphToMany::class, $this->testModel->counters());
        $this->assertInstanceOf(Collection::class, $this->testModel->counters);
    }

    public function test_can_model_get_counters(): void
    {
        $this->testModel->addCounter('number_of_downloads');

        $this->assertDatabaseHas(config('counters.tables.table_pivot_name'), [
            'counterable_id' => $this->testModel->getKey(),
            'counterable_type' => $this->testModel->getMorphClass(),
            'counter_id' => $this->counter->getKey(),
            'value' => 0,
        ]);
    }

    public function test_can_model_add_counters(): void
    {
        $this->testModel->addCounter('number_of_downloads');

        $this->assertDatabaseHas(config('counters.tables.table_pivot_name'), [
            'counterable_id' => $this->testModel->getKey(),
            'counterable_type' => $this->testModel->getMorphClass(),
            'counter_id' => $this->counter->getKey(),
            'value' => 0,
        ]);
    }

    public function test_can_model_remove_counters(): void
    {
        $this->testModel->addCounter('number_of_downloads');
        $counter = $this->testModel->removeCounter('number_of_downloads');

        $this->assertTrue($counter);

    }

    public function test_can_model_increment_counters(): void
    {
        $this->testModel->addCounter('number_of_downloads');
        $this->testModel->incrementCounter('number_of_downloads', 1);
        $this->testModel->incrementCounter('number_of_downloads', 1);
        $this->assertEquals(2, $this->testModel->getCounterValue('number_of_downloads'));
    }
}
