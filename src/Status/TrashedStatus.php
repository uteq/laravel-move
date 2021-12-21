<?php

namespace Uteq\Move\Status;

class TrashedStatus
{
    public const DEFAULT = '';
    public const WITH = 'with';
    public const ONLY = 'only';

    public static function fromBoolean($status): string
    {
        return $status ? self::WITH : self::DEFAULT;
    }
}
