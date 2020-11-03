<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Fields\Select;

class Role extends Select
{
    public string $component = 'role';

    public function __construct(string $name, string $attribute = null, callable $callableValue = null)
    {
        parent::__construct($name, $attribute, $callableValue);

        $this->placeholder = (string) __('Select a role');

        $this->beforeStore(function ($model, $data, $value) {
            unset($model->role);

            $model->syncRoles($data['role']);

            return $data;
        });

        $this->callableValue = function($value, $user, $field) {
            return optional($user->roles()->first())->name;
        };
    }
}
