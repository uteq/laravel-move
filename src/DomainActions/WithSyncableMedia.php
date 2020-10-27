<?php

namespace Uteq\Move\DomainActions;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Uteq\Move\DataTransferObjects\MediaCollection;

trait WithSyncableMedia
{
    public function syncMedia(HasMedia $model, MediaCollection $paths, $collection)
    {
        foreach ($paths->onlyDelete() as $path) {
            $model->deleteMedia($path->id);
        }

        foreach ($paths->withoutDelete() as $path) {
            if ($path->id && Media::query()->find($path->id)) {
                continue;
            }

            if (!file_exists($path->path)) {
                continue;
            }

            $model->addMediaFromString(file_get_contents($path->path))
                ->usingName(pathinfo($path->name, PATHINFO_FILENAME))
                ->usingFileName($path->name)
                ->toMediaCollection($collection);
        }
    }
}

