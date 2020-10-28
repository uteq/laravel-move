<?php

namespace Uteq\Move\Tests\Fixtures;

use Uteq\Move\Fields\Id;
use Uteq\Move\Fields\Password;
use Uteq\Move\Fields\Status;
use Uteq\Move\Fields\Text;
use Uteq\Move\Resource;

class UserResource extends Resource
{
    public static $model = User::class;

    public function fields()
    {
        return [
            Id::make(),

            Text::make('Name', 'name')
                ->rules(['required', 'string', 'max:255'])
                ->required(),

            Text::make('Email', 'email')
                ->hideWhenUpdating()
                ->requiredOnCreateOnly()
                ->creationRules(['required', 'string', 'email', 'max:255', 'unique:users']),

            Password::make('Password', 'password')
                ->required(function ($request, $model) {
                    return ! ($model->id ?? false);
                }),

            Password::make('Confirm password', 'password_confirmation')
                ->hideFromIndex()
                ->hideFromDetail()
                ->required(function ($request, $model) {
                    return ! ($model->id ?? false);
                }),

            Status::make('Email verified', 'email_verified_at', function ($value) {
                return $value !== null;
            })->hideFromForm(),
        ];
    }

    public function filters()
    {
        // TODO: Implement filters() method.
    }

    public function actions()
    {
        // TODO: Implement actions() method.
    }
}
