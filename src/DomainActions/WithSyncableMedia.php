<?php

namespace Uteq\Move\DomainActions;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Uteq\Move\DataTransferObjects\MediaCollection;

trait WithSyncableMedia
{
    protected $mediaPrefix = null;

    public function mediaPrefix($mediaPrefix)
    {
        $this->mediaPrefix = $mediaPrefix;

        return $this;
    }

    public function syncMedia(Model $model, MediaCollection $paths, $collection, $diskName = 'public')
    {
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
            if ($path->id && Media::query()->find($path->id)) {
                continue;
            }

            if (! file_exists($path->path)) {
                continue;
            }

            $result[] = $model->addMediaFromString(file_get_contents($path->path))
                ->usingName(pathinfo($path->name, PATHINFO_FILENAME))
                ->usingFileName($path->name)
                ->toMediaCollection($collection, $diskName);

            $model->save();
        }

        return $result;
    }
}
