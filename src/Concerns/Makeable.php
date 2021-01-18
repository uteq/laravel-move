<?php

namespace Uteq\Move\Concerns;

trait Makeable
{
    public static function make(...$arguments)
    {
        /** @psalm-suppress TooManyArguments */
        return new static(...$arguments);
    }
}
