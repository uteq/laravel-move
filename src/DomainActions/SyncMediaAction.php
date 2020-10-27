<?php

namespace Uteq\Move\DomainActions;

use Spatie\MediaLibrary\HasMedia;
use Uteq\Move\DataTransferObjects\MediaCollection;

class SyncMediaAction
{
    use WithSyncableMedia;

    public function __invoke(HasMedia $model, MediaCollection $paths, $collection)
    {
        $this->syncMedia($model, $paths, $collection);
    }
}
