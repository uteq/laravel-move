<?php

namespace Uteq\Move\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Uteq\Move\Actions\UnsetField;

class Password extends Field
{
    public string $component = 'password-field';

    public function __construct(
        string $name,
        string $attribute = null,
        Closure $valueCallback = null
    ) {
        parent::__construct($name, $attribute, $valueCallback);

        $this->resourceDataCallback = fn () => null;

        $this->beforeStore(function ($value, $_field, $model): string {
            if (! $model->id) {
                return $value;
            }

            return $value
                ? bcrypt($value)
                : UnsetField::class;
        });
    }

    public function cleanModel(Model $model): Model
    {
        if (isset($model->{$this->attribute})) {
            $model->{$this->attribute} = null;
        }

        return $model;
    }
}
