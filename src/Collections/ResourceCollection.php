<?php

namespace Uteq\Move\Collections;

use Illuminate\Support\Collection;
use Uteq\Move\Resource;

class ResourceCollection extends Collection
{
    public function authorized(): static
    {
        return new static($this->filter(fn (Resource $item) => $item->can('viewAny')));
    }

    public function grouped(): array
    {
        $resources = $this->filter(fn ($item) => $item::$group)
            ->mapToGroups(function ($item) {
                return [$item::$group => $item];
            });

        return $resources->toArray();
    }
}
