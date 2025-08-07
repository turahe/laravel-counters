<?php

declare(strict_types=1);

namespace Turahe\Counters\Facades;

use Illuminate\Support\Facades\Facade;
use Turahe\Counters\Classes\Counters as CountersClass;

/**
 * Optimized Counters facade for PHP 8.4 and Laravel 11/12.
 *
 * @method static \Turahe\Counters\Models\Counter create(string $key, string $name, int $initialValue = 0, int $step = 1, ?string $notes = null)
 * @method static \Turahe\Counters\Models\Counter get(string|int $key)
 * @method static int getValue(string $key, ?int $default = null)
 * @method static bool setValue(string $key, int $value)
 * @method static bool setStep(string $key, int $step)
 * @method static bool increment(string $key, ?int $step = null)
 * @method static bool decrement(string $key, ?int $step = null)
 * @method static bool reset(string $key)
 * @method static bool incrementIfNotHasCookies(string $key, ?int $step = null)
 * @method static bool decrementIfNotHasCookies(string $key, ?int $step = null)
 * @method static \Illuminate\Database\Eloquent\Collection getAll(?string $search = null, int $limit = 50)
 * @method static bool delete(string $key)
 * @method static array bulkIncrement(array $keys, ?int $step = null)
 * @method static array bulkDecrement(array $keys, ?int $step = null)
 * @method static array getStats()
 */
class Counters extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return CountersClass::class;
    }
}
