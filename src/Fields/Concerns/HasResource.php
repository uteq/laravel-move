<?php

namespace Uteq\Move\Fields\Concerns;

use Illuminate\Support\Str;
use Uteq\Move\Exceptions\FindResourceException;
use Uteq\Move\Facades\Move;

trait HasResource
{
    public function findResourceName(string $name, string $resource = null)
    {
        if (class_exists($resource)) {
            return $resource;
        }

        $resource = Str::kebab(Str::singular($resource ?? $name));
        $resources = collect(Move::find($resource));

        if ($resources->count() > 1) {
            throw FindResourceException::multipleImplementationsOfResource($resource, $resources->toArray());
        }

        return $resources->first();
    }
}
