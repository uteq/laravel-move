<?php

namespace Uteq\Move\Actions;

use Uteq\Move\Concerns\Makeable;

class LivewireCloseModal
{
    use Makeable;

    public function __invoke($item)
    {
        $item->emit('closeModal');

        return null;
    }

    public static function asClosure()
    {
        return fn (...$args) => app(static::class)(...$args);
    }
}
