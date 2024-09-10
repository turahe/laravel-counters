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
     * @param  int  $step
     *                     Creating a record in counters table with $key, $name, $inital_value, $step
     */
    public function create(string $key, $name, int $initial_value = 0, int $step = 1)
    {
        $value = $initial_value;

        try {
            Counter::query()->create(
                compact('key', 'name', 'initial_value', 'step', 'value')
            );
        } catch (\Exception $e) {
            throw CounterAlreadyExists::create($key);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Counter
     *                                                                                           Get a counter object for the given $key
     */
    public function get($key)
    {
        $counter = Counter::query()->where('key', $key)->first();

        if (is_null($counter)) {
            throw CounterDoesNotExist::create($key);
        }

        return $counter;
    }

    /**
     * @param  null  $default
     * @return mixed|null|string
     *                           get the counter value for the given $key,
     *                           $default will be returned in case the key is not found
     */
    public function getValue(string $key, $default = null)
    {
        $counter = Counter::query()->where('key', $key)->first();

        if ($counter) {
            return $counter->value;
        } elseif (! is_null($default)) {
            return $default;
        }

        throw CounterDoesNotExist::create($key);
    }

    /**
     * @param  $value
     *                set the value of the given counter's key
     */
    public function setValue(string $key, string|array $value)
    {
        Counter::query()->where('key', $key)->update(['value' => $value]);
    }

    /**
     * @param  $step
     *               set the step value for a given counter's
     */
    public function setStep(string $key, int $step)
    {
        Counter::query()->where('key', $key)->update(['step' => $step]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|Counters|null
     *                                                           increment the counter with the step
     */
    public function increment(string $key, ?int $step = null)
    {
        $counter = $this->get($key);

        if ($counter) {
            $counter->update(['value' => $counter->value + ($step ?? $counter->step)]);
        }

        return $counter;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|Counters|null
     *                                                           decrement the counter with the step
     */
    public function decrement(string $key, ?int $step = null)
    {
        $counter = $this->get($key);

        if ($counter) {
            $counter->update(['value' => $counter->value - ($step ?? $counter->step)]);
        }

        return $counter;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|Counters|null
     */
    public function reset(string $key)
    {
        $counter = $this->get($key);

        if ($counter) {
            $counter->update(['value' => $counter->initial_value]);
        }

        return $counter;
    }

    /**
     * @param  string  $key
     *                       This function will store a cookie for the counter key
     *                       If the cookie already exist, the counter will not incremented again
     */
    public function incrementIfNotHasCookies(string $key, ?int $step = null)
    {
        $cookieName = $this->getCookieName($key);

        if (! array_key_exists($cookieName, $_COOKIE)) {
            $this->increment($key, $step);
            setcookie($cookieName, 1);
        }
    }

    /**
     * @param  string|$key
     *              This function will store a cookie for the counter key
     *              If the cookie already exist, the counter will not decremented again
     */
    public function decrementIfNotHasCookies(string $key, ?int $step = null)
    {
        $cookieName = $this->getCookieName($key);

        if (! array_key_exists($cookieName, $_COOKIE)) {
            $this->decrement($key, $step);
            setcookie($cookieName, 1);
        }
    }

    /**
     * @return string
     */
    private function getCookieName(string $key)
    {
        return 'counters-cookie-'.$key;
    }
}
