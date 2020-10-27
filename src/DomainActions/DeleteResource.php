<?php

namespace Uteq\Move\DomainActions;

use Illuminate\Database\Eloquent\Model;

class DeleteResource
{
    public function __invoke(Model $model)
    {
        return $model->delete();
    }
}
