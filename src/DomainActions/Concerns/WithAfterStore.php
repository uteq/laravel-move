<?php

namespace Uteq\Move\DomainActions\Concerns;

use Illuminate\Database\Eloquent\Model;
use Uteq\Move\Resource;

trait WithAfterStore
{
    public function afterStore(Model $model, array $data, Resource $resource)
    {
        $afterStoreActions = method_exists($resource, 'afterStore') ? $resource->afterStore() : [];

        collect($afterStoreActions)->each->__invoke($this, $model, $data);

        return [$model, $data];
    }
}
