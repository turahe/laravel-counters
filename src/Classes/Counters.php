<?php

namespace Turahe\Counters\Classes;

use Turahe\Counters\Exceptions\CounterAlreadyExists;
use Turahe\Counters\Exceptions\CounterDoesNotExist;
use Turahe\Counters\Models\Counter;

/**
 * Class Counters.
 * @package Turahe\Counters\Classes
 */
class Counters
{
    /**
     * @param $key
     * @param $name
     * @param int $initial_value
     * @param int $step
     * Creating a record in counters table with $key, $name, $inital_value, $step
     */
    public function create($key, $name, $initial_value = 0, $step = 1)
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
     * @param $key
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Counter
     * Get a counter object for the given $key
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
     * @param $key
     * @param null $default
     * @return mixed|null|string
     * get the counter value for the given $key,
     * $default will be returned in case the key is not found
     */
    public function getValue($key, $default = null)
    {
        $counter = Counter::query()->where('key', $key)->first();
        if ($counter) {
            return $counter->value;
        } elseif (! is_null($default)) {
            return $default;
        } else {
            throw CounterDoesNotExist::create($key);
        }
    }

    /**
     * @param $key
     * @param $value
     * set the value of the given counter's key
     */
    public function setValue($key, $value)
    {
        Counter::query()->where('key', $key)->update(['value' => $value]);
    }

    /**
     * @param $key
     * @param $step
     * set the step value for a given counter's
     */
    public function setStep($key, $step)
    {
        Counter::query()->where('key', $key)->update(['step' => $step]);
    }

    /**
     * @param $key
     * @param null $step
     * @return \Illuminate\Database\Eloquent\Model|Counters|null
     * increment the counter with the step
     */
    public function increment($key, $step = null)
    {
        $counter = $this->get($key);
        if ($counter) {
            $counter->update(['value' => $counter->value + ($step ?? $counter->step)]);
        }

        return $counter;
    }

    /**
     * @param $key
     * @param null $step
     * @return \Illuminate\Database\Eloquent\Model|Counters|null
     * decrement the counter with the step
     */
    public function decrement($key, $step = null)
    {
        $counter = $this->get($key);
        if ($counter) {
            $counter->update(['value' => $counter->value - ($step ?? $counter->step)]);
        }

        return $counter;
    }

    /**
     * @param $key
     * @return \Illuminate\Database\Eloquent\Model|Counters|null
     */
    public function reset($key)
    {
        $counter = $this->get($key);
        if ($counter) {
            $counter->update(['value' => $counter->initial_value]);
        }

        return $counter;
    }

    /**
     * @param $key
     * This function will store a cookie for the counter key
     * If the cookie already exist, the counter will not incremented again
     * @param null $step
     */
    public function incrementIfNotHasCookies($key, $step = null)
    {
        $cookieName = $this->getCookieName($key);
        if (! array_key_exists($cookieName, $_COOKIE)) {
            $this->increment($key, $step);
            setcookie($cookieName, 1);
        }
    }

    /**
     * @param $key
     * This function will store a cookie for the counter key
     * If the cookie already exist, the counter will not decremented again
     * @param null $step
     */
    public function decrementIfNotHasCookies($key, $step = null)
    {
        $cookieName = $this->getCookieName($key);
        if (! array_key_exists($cookieName, $_COOKIE)) {
            $this->decrement($key, $step);
            setcookie($cookieName, 1);
        }
    }

    /**
     * @param $key
     * @return string
     */
    private function getCookieName($key)
    {
        return 'counters-cookie-'.$key;
    }
}
