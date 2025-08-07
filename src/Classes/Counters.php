<?php

declare(strict_types=1);

namespace Turahe\Counters\Classes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Turahe\Counters\Exceptions\CounterAlreadyExists;
use Turahe\Counters\Exceptions\CounterDoesNotExist;
use Turahe\Counters\Models\Counter;

/**
 * Optimized Counters class for PHP 8.4 and Laravel 11/12.
 */
final class Counters
{
    private const CACHE_TTL = 3600; // 1 hour
    private const COOKIE_PREFIX = 'counters-cookie-';

    public function __construct(
        private ?string $cachePrefix = null
    ) {
        $this->cachePrefix ??= 'counters:';
    }

    /**
     * Create a new counter record.
     */
    public function create(
        string $key, 
        string $name, 
        int $initialValue = 0, 
        int $step = 1,
        ?string $notes = null
    ): Counter {
        $value = $initialValue;

        try {
            $counter = Counter::query()->create([
                'key' => $key,
                'name' => $name,
                'initial_value' => $initialValue,
                'step' => $step,
                'value' => $value,
                'notes' => $notes,
            ]);

            $this->clearCache($key);

            return $counter;
        } catch (\Exception $e) {
            throw CounterAlreadyExists::create($key);
        }
    }

    /**
     * Get a counter by key with caching.
     */
    public function get(string|int $key): Counter
    {
        $cacheKey = $this->getCacheKey($key);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key) {
            $counter = Counter::query()->where('key', $key)->first();
            
            if (!$counter) {
                throw CounterDoesNotExist::create($key);
            }
            
            return $counter;
        });
    }

    /**
     * Get counter value with optional default.
     */
    public function getValue(string $key, ?int $default = null): int
    {
        try {
            return $this->get($key)->value;
        } catch (CounterDoesNotExist $e) {
            if ($default !== null) {
                return $default;
            }
            throw $e;
        }
    }

    /**
     * Set counter value.
     */
    public function setValue(string $key, int $value): bool
    {
        $counter = $this->get($key);
        $result = $counter->update(['value' => $value]);
        
        if ($result) {
            $this->clearCache($key);
        }
        
        return $result;
    }

    /**
     * Set counter step value.
     */
    public function setStep(string $key, int $step): bool
    {
        $counter = $this->get($key);
        $result = $counter->update(['step' => $step]);
        
        if ($result) {
            $this->clearCache($key);
        }
        
        return $result;
    }

    /**
     * Increment counter value.
     */
    public function increment(string $key, ?int $step = null): bool
    {
        $counter = $this->get($key);
        $incrementStep = $step ?? $counter->step;
        
        $result = $counter->update(['value' => $counter->value + $incrementStep]);
        
        if ($result) {
            $this->clearCache($key);
        }
        
        return $result;
    }

    /**
     * Decrement counter value.
     */
    public function decrement(string $key, ?int $step = null): bool
    {
        $counter = $this->get($key);
        $decrementStep = $step ?? $counter->step;
        
        $result = $counter->update(['value' => $counter->value - $decrementStep]);
        
        if ($result) {
            $this->clearCache($key);
        }
        
        return $result;
    }

    /**
     * Reset counter to initial value.
     */
    public function reset(string $key): bool
    {
        $counter = $this->get($key);
        $result = $counter->update(['value' => $counter->initial_value]);
        
        if ($result) {
            $this->clearCache($key);
        }
        
        return $result;
    }

    /**
     * Increment counter only if cookie doesn't exist.
     */
    public function incrementIfNotHasCookies(string $key, ?int $step = null): bool
    {
        $cookieName = $this->getCookieName($key);

        if (!Cookie::has($cookieName)) {
            $result = $this->increment($key, $step);
            
            if ($result) {
                Cookie::queue($cookieName, '1', 60 * 24 * 365); // 1 year
            }
            
            return $result;
        }

        return false;
    }

    /**
     * Decrement counter only if cookie doesn't exist.
     */
    public function decrementIfNotHasCookies(string $key, ?int $step = null): bool
    {
        $cookieName = $this->getCookieName($key);

        if (!Cookie::has($cookieName)) {
            $result = $this->decrement($key, $step);
            
            if ($result) {
                Cookie::queue($cookieName, '1', 60 * 24 * 365); // 1 year
            }
            
            return $result;
        }

        return false;
    }

    /**
     * Get all counters with optional filtering.
     */
    public function getAll(?string $search = null, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        $query = Counter::query();
        
        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Delete a counter.
     */
    public function delete(string $key): bool
    {
        $counter = $this->get($key);
        $result = $counter->delete();
        
        if ($result) {
            $this->clearCache($key);
        }
        
        return $result;
    }

    /**
     * Bulk increment multiple counters.
     */
    public function bulkIncrement(array $keys, ?int $step = null): array
    {
        $results = [];
        
        foreach ($keys as $key) {
            try {
                $results[$key] = $this->increment($key, $step);
            } catch (CounterDoesNotExist $e) {
                $results[$key] = false;
            }
        }
        
        return $results;
    }

    /**
     * Bulk decrement multiple counters.
     */
    public function bulkDecrement(array $keys, ?int $step = null): array
    {
        $results = [];
        
        foreach ($keys as $key) {
            try {
                $results[$key] = $this->decrement($key, $step);
            } catch (CounterDoesNotExist $e) {
                $results[$key] = false;
            }
        }
        
        return $results;
    }

    /**
     * Get counter statistics.
     */
    public function getStats(): array
    {
        return [
            'total_counters' => Counter::count(),
            'total_value' => Counter::sum('value'),
            'average_value' => Counter::avg('value'),
            'max_value' => Counter::max('value'),
            'min_value' => Counter::min('value'),
        ];
    }

    /**
     * Clear cache for a specific key.
     */
    private function clearCache(string $key): void
    {
        Cache::forget($this->getCacheKey($key));
    }

    /**
     * Get cache key for counter.
     */
    private function getCacheKey(string|int $key): string
    {
        return $this->cachePrefix . (string) $key;
    }

    /**
     * Get cookie name for counter.
     */
    private function getCookieName(string $key): string
    {
        return self::COOKIE_PREFIX . $key;
    }
}
