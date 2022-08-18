<?php

namespace Uteq\Move\DomainActions\Concerns;

trait WithFill
{
    public function fill($model, $input, $resource = null, $options = [])
    {
        unset($input['store'], $model->store);

        $options['store'] = $this;

        if (! $resource) {
            return $model;
        }

        return $resource->fill(
            // All media should be stripped from the model data
            //  because this action will store the media separate in the after store.
            ...$options,
        );
    }
}
