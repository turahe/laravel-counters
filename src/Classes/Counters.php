<?php

namespace Turahe\Counters\Classes;

use Turahe\Counters\Exceptions\CounterAlreadyExists;
use Turahe\Counters\Exceptions\CounterDoesNotExist;
use Turahe\Counters\Models\Counter;

/**
 * Class Counters.
 */
class Counters
{
    /**
     * Creating a record in counters table with $key, $name, $initial_value, $step
     */
    public function create(string $key, $name, int $initial_value = 0, int $step = 1): Counter
    {
        $value = $initial_value;

        try {
            return Counter::query()->create(
                compact('key', 'name', 'initial_value', 'step', 'value')
            );
        } catch (\Exception $e) {
            throw CounterAlreadyExists::create($key);
        }
    }

    /**
     * Get a counter object for the given $key
     */
    public function get(string|int $key): ?Counter
    {
        $counter = Counter::query()->where('key', $key)->first();

        if (is_null($counter)) {
            throw CounterDoesNotExist::create($key);
        }

        return $counter;
    }

    /**
     * get the counter value for the given $key,
     * $default will be returned in case the key is not found
     */
    public function getValue(string $key, $default = null): ?int
    {
        $counter = $this->get($key);

        if ($counter) {
            return $counter->value;
        } elseif (! is_null($default)) {
            return $default;
        }

        throw CounterDoesNotExist::create($key);
    }

    /**
     * set the value of the given counter's key
     */
    public function setValue(string $key, string|array $value): bool
    {
        $counter = $this->get($key);
        if ($counter) {
            return $counter->update(['value' => $value]);
        }
        throw CounterDoesNotExist::create($key);
    }

    /**
     * set the step value for a given counter's
     */
    public function setStep(string $key, int $step): bool
    {
        $counter = $this->get($key);
        if ($counter) {
            return $counter->update(['step' => $step]);
        }
        throw CounterDoesNotExist::create($key);
    }

    /**
     * increment the counter with the step
     */
    public function increment(string $key, ?int $step = null): bool
    {
        $counter = $this->get($key);

        if ($counter) {
            return $counter->update(['value' => $counter->value + ($step ?? $counter->step)]);
        }
        throw CounterDoesNotExist::create($key);
    }

    /**
     * decrement the counter with the step
     */
    public function decrement(string $key, ?int $step = null): bool
    {
        $counter = $this->get($key);

        if ($counter) {
            return $counter->update(['value' => $counter->value - ($step ?? $counter->step)]);
        }
        throw CounterDoesNotExist::create($key);
    }

    /**
     * reset the counter with the default value of counter
     */
    public function reset(string $key): bool
    {
        $counter = $this->get($key);

        if ($counter) {
            return $counter->update(['value' => $counter->initial_value]);
        }
        throw CounterDoesNotExist::create($key);
    }

    /**
     * This function will store a cookie for the counter key
     * If the cookie already exist, the counter will not incremented again
     */
    public function incrementIfNotHasCookies(string $key, ?int $step = null): bool
    {
        $cookieName = $this->getCookieName($key);

        if (! array_key_exists($cookieName, $_COOKIE)) {
            $this->increment($key, $step);

            return setcookie($cookieName, 1);
        }

        return false;
    }

    /**
     * This function will store a cookie for the counter key
     * If the cookie already exist, the counter will not decremented again
     */
    public function decrementIfNotHasCookies(string $key, ?int $step = null): bool
    {
        $cookieName = $this->getCookieName($key);

        if (! array_key_exists($cookieName, $_COOKIE)) {
            $this->decrement($key, $step);

            return setcookie($cookieName, 1);
        }

        return false;
    }

    /**
     * Get Cookie name
     */
    private function getCookieName(string $key): string
    {
        return 'counters-cookie-'.$key;
    }
}
