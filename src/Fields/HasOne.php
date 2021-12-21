<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Fields\Concerns\HasResource;

class HasOne extends Select
{
    use HasResource;

    public function __construct(string $name, string $attribute, string $resource, \Closure $valueCallback = null)
    {
        $this->resourceName = $this->findResourceName($name, $resource);

        /** @psalm-suppress UnusedClosureParam */
        $valueCallback ??= function ($value) use ($resource) {
            /** @psalm-suppress InvalidPropertyFetch */
            $model = $resource::$model;

            if (\request()->input('parent_model') !== $model) {
                return $value;
            }

            return \request()->input('parent_id', $value);
        };

        parent::__construct($name, $attribute, $valueCallback);
    }
}
