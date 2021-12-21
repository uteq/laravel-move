<?php

namespace Uteq\Move\Status;

class TrashedStatus
{
    const DEFAULT = '';
    const WITH = 'with';
    const ONLY = 'only';

    public static function fromBoolean($status): string
    {
        return $status ? self::WITH : self::DEFAULT;
    }
}
