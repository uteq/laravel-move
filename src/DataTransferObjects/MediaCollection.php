<?php

namespace Uteq\Move\DataTransferObjects;

use Illuminate\Support\Collection;

class MediaCollection extends Collection implements MediaCollectable
{
    public static function create(array $data)
    {
        return new self(collect($data)->map(fn ($item) => new MediaData($item))->toArray());
    }

    public function onlyDelete()
    {
        return collect($this->collection)
            ->filter(fn ($item) => $item)
            ->filter(fn (MediaData $item) => $item->action === 'delete');
    }

    public function withoutDelete()
    {
        return collect($this->collection)
            ->filter(fn ($item) => $item)
            ->filter(fn (MediaData $item) => $item->action !== 'delete');
    }
}
