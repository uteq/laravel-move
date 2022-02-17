<?php

namespace Uteq\Move\DomainActions;

use Illuminate\Database\Eloquent\Model;
use Uteq\Move\DataTransferObjects\MediaCollection;
use Uteq\Move\DomainActions\Concerns\WithAfterStore;
use Uteq\Move\DomainActions\Concerns\WithFill;
use Uteq\Move\DomainActions\Concerns\WithMedia;
use Uteq\Move\Resource;

class StoreResource
{
    use WithMedia, WithFill, WithAfterStore;

    public function __invoke(Model $model, array $data, Resource $resource)
    {
        dd('sfd');
        $model = $this->fill($model, $data, $resource, $this->withoutMedia($model, $data));
        $model->save();

        [$model, $data] = $this->afterStore($model, $data, $resource);

        /** @psalm-suppress InvalidArgument */
        app()->call([$this, 'syncMedia'], ['model' => $model, 'data' => $data]);

        return $model;
    }
}
