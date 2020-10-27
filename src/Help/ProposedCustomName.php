<?php

namespace Uteq\Move\Help;

use Illuminate\Database\Eloquent\Model;

class ProposedCustomName
{
    public static function asHtml(string $field, Model $resource): string
    {
        return static::make($field, $resource)();
    }

    public static function make(string $field, Model $resource): \Closure
    {
        return function () use ($field, $resource) {
            if ($resource->{$field}) {
                return '';
            }

            if (! isset($resource->custom[$field])) {
                return '';
            }

            return 'Voorstel van invoerder: ' . $resource->custom[$field];
        };
    }
}
