<?php

namespace Uteq\Move\Concerns;

trait Makeable
{
    /**
     * Create a new element.
     *
     * @return static
     */
    public static function make(...$arguments)
    {
        /** @psalm-suppress TooManyArguments */
        return new static(...$arguments);
    }
}
