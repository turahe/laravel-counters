<?php

namespace Turahe\Counters\Tests;

use Turahe\Counters\Classes\Counters;
use Turahe\Counters\Exceptions\CounterAlreadyExists;
use Turahe\Counters\Exceptions\CounterDoesNotExist;
use Turahe\Counters\Models\Counter as CounterModel;

class CounterServiceTest extends TestCase
{
    public function test_create_counter()
    {
        $counterModel = new Counters;
        $counter = $counterModel->create('counter', 'counter', 0, 1);

        $this->assertInstanceOf(CounterModel::class, $counter);
        $this->assertEquals('counter', $counter->key);
        $this->assertEquals('counter', $counter->name);
        $this->assertEquals(0, $counter->value);
        $this->assertEquals(1, $counter->step);

    }

    public function test_create_cannot_create_counter_with_same_key()
    {
        $this->expectException(CounterAlreadyExists::class);

        $counterModel = new Counters;
        $counterModel->create('counter', 'counter', 0, 1);
        $counterModel->create('counter', 'counter', 0, 1);
    }

    public function test_can_get_key_counter()
    {
        $counterModel = new Counters;
        $counterModel->create('counter', 'counter', 0, 1);
        $counter = $counterModel->get('counter');
        $this->assertInstanceOf(CounterModel::class, $counter);
    }

    public function test_cannot_get_key_counter()
    {
        $this->expectException(CounterDoesNotExist::class);
        $counterModel = new Counters;
        $counterModel->get('counter');
    }

    public function test_can_get_value_counter()
    {
        $counterModel = new Counters;
        $counterModel->create('counter', 'counter', 0, 1);

        $counterValue1 = $counterModel->getValue('counter');
        $this->assertEquals(0, $counterValue1);

        $counterValue2 = $counterModel->getValue('counter-not-found', 1);
        $this->assertEquals(1, $counterValue2);
    }

    public function test_can_set_value_counter()
    {
        $counterModel = new Counters;
        $counterModel->create('counter', 'counter', 0, 1);
        $counter = $counterModel->setValue('counter', 1);

        $this->assertTrue($counter);
        $this->assertDatabaseHas('counters', [
            'key' => 'counter',
            'name' => 'counter',
            'value' => 1,
            'step' => 1,
        ]);

    }

    public function test_can_set_step_counter()
    {
        $counterModel = new Counters;
        $counterModel->create('counter', 'counter', 0, 1);
        $counter = $counterModel->setStep('counter', 1);

        $this->assertTrue($counter);
        $this->assertDatabaseHas('counters', [
            'key' => 'counter',
            'name' => 'counter',
            'value' => 0,
            'step' => 1,
        ]);

    }

    public function test_can_increment_counter()
    {
        $counterModel = new Counters;
        $counterModel->create('counter', 'counter', 1, 1);
        $counter = $counterModel->increment('counter', 1);

        $this->assertTrue($counter);
        $this->assertDatabaseHas('counters', [
            'key' => 'counter',
            'name' => 'counter',
            'value' => 2,
            'step' => 1,
        ]);

    }

    public function test_can_decrement_counter()
    {
        $counterModel = new Counters;
        $counterModel->create('counter', 'counter', 3, 1);
        $counter = $counterModel->decrement('counter', 1);

        $this->assertTrue($counter);
        $this->assertDatabaseHas('counters', [
            'key' => 'counter',
            'name' => 'counter',
            'value' => 2,
            'step' => 1,
        ]);

    }

    public function test_can_reset_counter()
    {
        $counterModel = new Counters;
        $counterModel->create('counter', 'counter', 3, 1);
        $counter = $counterModel->decrement('counter', 1);

        $this->assertTrue($counter);
        $this->assertDatabaseHas('counters', [
            'key' => 'counter',
            'name' => 'counter',
            'value' => 2,
            'step' => 1,
        ]);

    }
}
