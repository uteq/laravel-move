<?php

namespace Uteq\Move\Fields;

use Illuminate\Database\Eloquent\Model;

class Password extends Field
{
    public string $component = 'password-field';

    public function cleanModel(Model $model)
    {
        if (isset($model->{$this->attribute})) {
            $model->{$this->attribute} = null;
        }
        return $model;
    }
}
