<?php

namespace Uteq\Move\Fields;

use Illuminate\Database\Eloquent\Model;
use Uteq\Move\Actions\UnsetField;

class Password extends Field
{
    public string $component = 'password-field';

    public function __construct(string $name, string $attribute = null, callable $valueCallback = null)
    {
        parent::__construct($name, $attribute, $valueCallback);

        $this->valueCallback = fn () => null;

        $this->beforeStore(function ($value, $field, $model, $data) {
            if (! $model->id) {
                return $value;
            }

            if ($value) {
                return bcrypt($value);
            } else {
                return UnsetField::class;
            }
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
