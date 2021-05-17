<?php

namespace Uteq\Move\DomainActions\Concerns;

use Illuminate\Database\Eloquent\Model;
use Uteq\Move\DataTransferObjects\MediaCollection;
use Uteq\Move\DomainActions\SyncMediaAction;

trait WithMedia
{
    /**
     * Returns an array with data without media
     *
     * @param Model $model
     * @param array $data
     * @return array
     */
    public function withoutMedia(Model $model, array $data)
    {
        return [$this->modelWithoutMedia($model, $data), $this->dataWithoutMedia($data)];
    }

    /**
     * Returns the model without the MediaCollection
     *
     * @param Model $model
     * @return Model
     */
    public function modelWithoutMedia(Model $model, $data)
    {
        collect($data)
            ->filter(fn ($attribute) => $attribute instanceof MediaCollection)
            /** @psalm-suppress UnusedClosureParam */
            ->each(function ($attribute, $key) use (&$model) {
                unset($model[$key]);
            });

        return $model;
    }

    /**
     * Returns the data without media
     *
     * @param array $data
     * @return array
     */
    public function dataWithoutMedia(array $data)
    {
        return collect($data)
            ->filter(fn ($attribute) => ! $attribute instanceof MediaCollection)
            ->toArray();
    }

    public function syncMedia(Model $model, array $data, SyncMediaAction $syncer = null, $disk = 'public')
    {
        $syncer ??= new SyncMediaAction();

        return collect($data)
            ->filter(fn ($attribute) => $attribute instanceof MediaCollection)
            ->each(fn ($mediaCollection, $key) => $syncer($model, $mediaCollection, $key, $disk));
    }
}
