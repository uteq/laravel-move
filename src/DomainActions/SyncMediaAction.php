<?php

namespace Uteq\Move\DomainActions;

use Illuminate\Database\Eloquent\Model;
use Uteq\Move\DataTransferObjects\MediaCollection;

class SyncMediaAction
{
    use WithSyncableMedia;

    public function __invoke(Model $model, MediaCollection $paths, $collection, $disk = null)
    {
        $disk ??= config('filesystems.default');

        $this->syncMedia($model, $paths, $collection, $disk);
    }
}
