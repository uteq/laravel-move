<?php

namespace Uteq\Move;

class MoveBladeDirectives
{
    public static function moveStyles($expression)
    {
        return '{!! \Uteq\Move\Facades\Move::styles(' . $expression . ') !!}';
    }

    public static function moveScripts($expression)
    {
        return '{!! \Uteq\Move\Facades\Move::scripts(' . $expression . ') !!}';
    }
}
