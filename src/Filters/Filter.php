<?php

namespace Uteq\Move\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Uteq\Move\Concerns\Makeable;
use Uteq\Move\Concerns\Metable;

abstract class Filter
{
    use Makeable;
    use Metable;

    public function __invoke($request, Builder $query)
    {
        $value = Arr::get($request, Str::slug(static::class));

        return $this->apply($query, $value);
    }

    public function name()
    {
        return $this->name;
    }

    public function component()
    {
        return $this->component;
    }

    public function default()
    {
        return;
    }
}
