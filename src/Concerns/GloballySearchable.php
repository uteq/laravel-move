<?php

namespace Uteq\Move\Concerns;

trait GloballySearchable
{
    public static bool $globallySearchable = true;

    public static int $globalSearchResults = 5;

    public static string $globalSearchLink = 'detail';

}
