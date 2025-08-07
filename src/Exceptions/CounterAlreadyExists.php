<?php

declare(strict_types=1);

namespace Turahe\Counters\Exceptions;

use InvalidArgumentException;

/**
 * Exception thrown when attempting to create a counter that already exists.
 */
class CounterAlreadyExists extends InvalidArgumentException
{
    public static function create(string $key): self
    {
        return new self("Counter '{$key}' already exists.");
    }
}
