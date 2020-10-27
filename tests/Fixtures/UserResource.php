<?php

namespace Uteq\Move\Tests\Fixtures;

use Uteq\Move\Fields\Id;
use Uteq\Move\Resource;

class UserResource extends Resource
{
    public function fields()
    {
        return [
            Id::make(),
        ];
    }
}
