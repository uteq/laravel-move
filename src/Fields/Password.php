<?php

namespace Uteq\Move\Fields;

use Illuminate\Database\Eloquent\Model;
use Uteq\Move\Actions\UnsetField;

class Password extends Field
{
    public string $component = 'password-field';

    public function __construct(string $name, string $attribute = null, callable $callableValue = null)
    {
        parent::__construct($name, $attribute, $callableValue);

        $this->callableValue = fn () => null;

        $this->beforeStore(function ($value, $field, $model, $data) {
            if ($value) {
                $model->password = bcrypt($value);
            } else {
                unset($model->password);
            }

            return UnsetField::class;
        });
    }

    public function cleanModel(Model $model)
    {
        if (isset($model->{$this->attribute})) {
            $model->{$this->attribute} = null;
        }

        return $model;
    }
}
