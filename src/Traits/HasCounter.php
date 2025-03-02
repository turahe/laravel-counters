<?php

namespace Turahe\Counters\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Turahe\Counters\Facades\Counters;
use Turahe\Counters\Models\Counter;
use Turahe\Counters\Models\Counterable;

/**
 * Trait HasCounter.
 */
trait HasCounter
{
    /**
     * The morph relation between any model and counters
     *
     * @return mixed
     */
    public function counters(): MorphToMany
    {
        return $this->morphToMany(
            related: config('counter.models.counter', Counter::class),
            name: 'counterable',
            table: config('counter.models.table_pivot_name')
        )->withPivot('value')
            ->withTimestamps();
    }

    /**
     * Get counter related to the relation with the given $key
     *
     * @return mixed
     */
    public function getCounter(string $key)
    {
        $counter = $this->counters->where('key', $key)->first();

        // connect the counter to the object if it's not exist
        if (! $counter) {
            $this->addCounter($key);
            $counter = $this->counters->where('key', $key)->first();
        }

        return $counter;
    }

    /**
     * check if the related model has counter with the given key
     */
    public function hasCounter(string $key): bool
    {
        return ! is_null($this->counters()->where(config('counter.models.table_name').'.key', $key)->first());
    }

    /**
     * Get the related model value of the counter for the given $key
     */
    public function getCounterValue(string $key): int
    {
        $counter = $this->getCounter($key);
        $value = 0;

        if ($pivot = $counter->pivot) {
            $value = $pivot?->value;
        }

        return $value;
    }

    /**
     * Add a record to counterable table (make relation with the given $key)
     */
    public function addCounter(string $key, ?int $initialValue = null): void
    {
        $counter = Counters::get($key);

        $this->counters()->attach($this->getKey(), [
            'value' => ! is_null($initialValue) ? $initialValue : $counter->initial_value,
        ]);

    }

    /**
     * @param  $key
     *              Remove the relation in counterable table
     */
    public function removeCounter(string $key): bool
    {
        $counter = Counters::get($key);

        if ($counter) {
            return $this->counters()->detach($counter->getKey());
        }

        return false;
    }

    /**
     * Increment the counterable in the relation table for the given $key
     */
    public function incrementCounter(string $key, ?int $step = null): void
    {
        $counter = $this->getCounter($key);

        $this->counters()->updateExistingPivot($this->getKey(), [
            'value' => $counter->pivot->value + ($step ?? $counter->step),
        ]);
    }

    /**
     * Decrement the counterable in the relation table for the given $key
     *
     * @return mixed
     */
    public function decrementCounter(string $key, ?int $step = null): bool
    {
        $counter = $this->getCounter($key);

        return $this->counters()->updateExistingPivot($counter->getKey(), ['value' => $counter->pivot->value - ($step ?? $counter->step)]);
    }

    /**
     * Reset the counterable in the relation table to the initial value for the given $key
     *
     * @return mixed
     */
    public function resetCounter(string $key, ?int $initialValue = null): bool
    {
        $counter = $this->getCounter($key);

        return $this->counters()->updateExistingPivot($counter->id, ['value' => $initialValue ?? $counter->initial_value]);
    }
}
