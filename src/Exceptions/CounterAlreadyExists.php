<?php

namespace Turahe\Counters\Exceptions;

use InvalidArgumentException;

class CounterAlreadyExists extends InvalidArgumentException
{
    public static function create(string $key)
    {
        return new static("A `{$key}` counter already exists.");
    }
}
