<?php

namespace Uteq\Move\DataTransferObjects;

use Closure;
use Uteq\Move\Support\DataTransferObjectCollection;

class MediaCollection extends DataTransferObjectCollection implements MediaCollectable
{
    public function current(): MediaData
    {
        return parent::current();
    }

    public static function create($data)
    {
        if ($data instanceof MediaCollection) {
            return $data;
        }

        if (! is_array($data)) {
            throw new \Exception(sprintf(
                '%s: Unkown type `%s`',
                __METHOD__,
                gettype($data)
            ));
        }

        return new self(
            collect($data)
                ->map(fn ($item) => $item instanceof MediaData ? $item : new MediaData($item))
                ->toArray()
        );
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function onlyDelete()
    {
        return collect($this->collection)
            ->filter(fn ($item) => $item)
            ->filter(fn (MediaData $item) => $item->action === 'delete');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function withoutDelete()
    {
        return collect($this->collection)
            ->filter(fn ($item) => $item)
            ->filter(fn (MediaData $item) => $item->action !== 'delete');
    }

    public function each(Closure $closure): static
    {
        return static::create(
            collect($this->collection)
                ->each($closure)
                ->toArray()
        );
    }
}
