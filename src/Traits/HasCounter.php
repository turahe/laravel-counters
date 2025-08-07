<?php

declare(strict_types=1);

namespace Turahe\Counters\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Collection;
use Turahe\Counters\Facades\Counters;
use Turahe\Counters\Models\Counter;
use Turahe\Counters\Models\Counterable;

/**
 * Optimized HasCounter trait for PHP 8.4 and Laravel 11/12.
 */
trait HasCounter
{
    /**
     * The morph relation between any model and counters.
     */
    public function counters(): MorphToMany
    {
        return $this->morphToMany(
            related: config('counter.models.counter', Counter::class),
            name: 'counterable',
            table: config('counter.tables.table_pivot_name', 'counterables')
        )->withPivot('value')
          ->withTimestamps();
    }

    /**
     * Get counter related to the relation with the given key.
     */
    public function getCounter(string $key): ?Counter
    {
        $counter = $this->counters->where('key', $key)->first();

        // Connect the counter to the object if it doesn't exist
        if (!$counter) {
            $this->addCounter($key);
            $counter = $this->counters->where('key', $key)->first();
        }

        return $counter;
    }

    /**
     * Check if the related model has counter with the given key.
     */
    public function hasCounter(string $key): bool
    {
        return $this->counters()
            ->where(config('counter.tables.table_name', 'counters') . '.key', $key)
            ->exists();
    }

    /**
     * Get the related model value of the counter for the given key.
     */
    public function getCounterValue(string $key): int
    {
        $counter = $this->getCounter($key);
        
        if (!$counter || !$counter->pivot) {
            return 0;
        }

        return (int) $counter->pivot->value;
    }

    /**
     * Add a record to counterable table (make relation with the given key).
     */
    public function addCounter(string $key, ?int $initialValue = null): void
    {
        try {
            $counter = Counters::get($key);
            $value = $initialValue ?? $counter->initial_value;

            $this->counters()->attach($counter->getKey(), [
                'value' => $value,
            ]);

            // Refresh the relationship to get the latest data
            $this->load('counters');
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            throw new \RuntimeException("Failed to add counter '{$key}' to model", 0, $e);
        }
    }

    /**
     * Remove the relation in counterable table.
     */
    public function removeCounter(string $key): bool
    {
        try {
            $counter = Counters::get($key);
            $result = $this->counters()->detach($counter->getKey());
            
            // Refresh the relationship
            $this->load('counters');
            
            return $result > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Increment the counterable in the relation table for the given key.
     */
    public function incrementCounter(string $key, ?int $step = null): bool
    {
        try {
            $counter = $this->getCounter($key);
            
            if (!$counter || !$counter->pivot) {
                return false;
            }

            $newValue = $counter->pivot->value + ($step ?? $counter->step);
            
            $result = $this->counters()->updateExistingPivot($counter->getKey(), [
                'value' => $newValue,
            ]);

            // Refresh the relationship to get the latest pivot value
            $this->load('counters');
            
            return $result > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Decrement the counterable in the relation table for the given key.
     */
    public function decrementCounter(string $key, ?int $step = null): bool
    {
        try {
            $counter = $this->getCounter($key);
            
            if (!$counter || !$counter->pivot) {
                return false;
            }

            $newValue = $counter->pivot->value - ($step ?? $counter->step);
            
            $result = $this->counters()->updateExistingPivot($counter->getKey(), [
                'value' => $newValue,
            ]);

            // Refresh the relationship to get the latest pivot value
            $this->load('counters');
            
            return $result > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reset the counterable in the relation table to the initial value for the given key.
     */
    public function resetCounter(string $key, ?int $initialValue = null): bool
    {
        try {
            $counter = $this->getCounter($key);
            
            if (!$counter) {
                return false;
            }

            $value = $initialValue ?? $counter->initial_value;
            
            $result = $this->counters()->updateExistingPivot($counter->getKey(), [
                'value' => $value,
            ]);

            // Refresh the relationship to get the latest pivot value
            $this->load('counters');
            
            return $result > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set the counter value for the given key.
     */
    public function setCounterValue(string $key, int $value): bool
    {
        try {
            $counter = $this->getCounter($key);
            
            if (!$counter) {
                return false;
            }

            $result = $this->counters()->updateExistingPivot($counter->getKey(), [
                'value' => $value,
            ]);

            // Refresh the relationship to get the latest pivot value
            $this->load('counters');
            
            return $result > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all counters for this model.
     */
    public function getAllCounters(): Collection
    {
        return $this->counters;
    }

    /**
     * Get counters with values greater than the given value.
     */
    public function getCountersWithValueGreaterThan(int $value): Collection
    {
        return $this->counters()->wherePivot('value', '>', $value)->get();
    }

    /**
     * Get counters with values less than the given value.
     */
    public function getCountersWithValueLessThan(int $value): Collection
    {
        return $this->counters()->wherePivot('value', '<', $value)->get();
    }

    /**
     * Get active counters (value > 0).
     */
    public function getActiveCounters(): Collection
    {
        return $this->counters()->wherePivot('value', '>', 0)->get();
    }

    /**
     * Get inactive counters (value = 0).
     */
    public function getInactiveCounters(): Collection
    {
        return $this->counters()->wherePivot('value', '=', 0)->get();
    }

    /**
     * Get the total value of all counters for this model.
     */
    public function getTotalCounterValue(): int
    {
        return $this->counters()->sum('value');
    }

    /**
     * Get the average value of all counters for this model.
     */
    public function getAverageCounterValue(): float
    {
        return $this->counters()->avg('value') ?? 0.0;
    }

    /**
     * Bulk increment multiple counters.
     */
    public function bulkIncrementCounters(array $keys, ?int $step = null): array
    {
        $results = [];
        
        foreach ($keys as $key) {
            $results[$key] = $this->incrementCounter($key, $step);
        }
        
        return $results;
    }

    /**
     * Bulk decrement multiple counters.
     */
    public function bulkDecrementCounters(array $keys, ?int $step = null): array
    {
        $results = [];
        
        foreach ($keys as $key) {
            $results[$key] = $this->decrementCounter($key, $step);
        }
        
        return $results;
    }

    /**
     * Bulk reset multiple counters.
     */
    public function bulkResetCounters(array $keys, ?int $initialValue = null): array
    {
        $results = [];
        
        foreach ($keys as $key) {
            $results[$key] = $this->resetCounter($key, $initialValue);
        }
        
        return $results;
    }
}
