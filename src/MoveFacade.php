<?php

namespace Uteq\Move;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Uteq\Move\Move
 */
class MoveFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-move';
    }
}
