<?php

namespace Uteq\Move\DomainActions;

use Illuminate\Database\Eloquent\Model;
use Uteq\Move\DataTransferObjects\MediaCollection;

class SyncMediaAction
{
    use WithSyncableMedia;

    public function __invoke(Model $model, MediaCollection $paths, $collection)
    {
        $this->syncMedia($model, $paths, $collection);
    }
}
