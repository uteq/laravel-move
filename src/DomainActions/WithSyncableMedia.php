<?php

namespace Uteq\Move\DomainActions;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Uteq\Move\DataTransferObjects\MediaCollection;

trait WithSyncableMedia
{
    protected $mediaPrefix = null;

    public function mediaPrefix($mediaPrefix)
    {
        $this->mediaPrefix = $mediaPrefix;

        return $this;
    }

    public function syncMedia(
        Model $model,
        MediaCollection $paths,
        $collection,
        $diskName = null,
        $manipulations = [],
    )
    {
        $diskName ??= config('filesystems.default');

        if (! $model instanceof HasMedia) {
            throw new \Exception(sprintf(
                '%s: The given model `%s` should implement the %s interface',
                __METHOD__,
                get_class($model),
                HasMedia::class,
            ));
        }

        foreach ($paths->onlyDelete() as $path) {
            $model->deleteMedia($path->id);
        }

        $result = [];

        foreach ($paths->withoutDelete() as $path) {
            if ($path->id && config('media-library.media_model')::query()->find($path->id)) {
                continue;
            }

            if (! file_exists($path->path)) {
                continue;
            }

            if (method_exists($this, 'beforeSyncMedia')) {
                $pathPath = $this->beforeSyncMedia($path->path);
            }

            $result[] = $model->addMediaFromString(file_get_contents($pathPath ?? $path->path))
                ->withManipulations($manipulations)
                ->usingName(pathinfo($path->name, PATHINFO_FILENAME))
                ->usingFileName($path->name)
                ->toMediaCollection($collection, $diskName);

            $model->save();
        }

        return $result;
    }
}
