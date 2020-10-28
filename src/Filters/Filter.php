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

    abstract public function apply($query, $value): Builder;

    public function __invoke($request, Builder $query): Builder
    {
        $value = Arr::get($request, Str::slug(static::class));

        return $this->apply($query, $value);
    }

    public function name()
    {
        if (! isset($this->name)) {
            throw new \Exception(sprintf(
                '%s: The filter %s should have the property `public string $name defined`',
                __METHOD__,
                static::class
            ));
        }

        return $this->name;
    }

    public function component()
    {
        if (! isset($this->component)) {
            throw new \Exception(sprintf(
                '%s: The filter %s should have the property `public string $component defined`',
                __METHOD__,
                static::class
            ));
        }

        return $this->component;
    }

    public function default()
    {
        return;
    }
}
