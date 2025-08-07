<?php

declare(strict_types=1);

namespace Turahe\Counters\Exceptions;

use InvalidArgumentException;

/**
 * Exception thrown when attempting to access a counter that doesn't exist.
 */
class CounterDoesNotExist extends InvalidArgumentException
{
    public static function create(string $key): self
    {
        return new self("Counter '{$key}' does not exist.");
    }
}
