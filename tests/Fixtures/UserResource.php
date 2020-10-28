<?php

namespace Uteq\Move\Tests\Fixtures;

use Uteq\Move\Fields\Id;
use Uteq\Move\Resource;

class UserResource extends Resource
{
    public static $model = User::class;

    public function fields()
    {
        return [
            Id::make(),
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
