<?php

namespace Uteq\Move\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class MediaCollection extends DataTransferObjectCollection implements MediaCollectable
{
    public function current(): MediaData
    {
        return parent::current();
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
