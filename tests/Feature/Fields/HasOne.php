<?php

namespace Uteq\Move\Tests\Feature\Fields;

use Uteq\Move\Exceptions\FindResourceException;
use Uteq\Move\Tests\Fixtures\UserResource;
use Uteq\Move\Tests\TestCase;

class HasOne extends TestCase
{
    /** @test */
    public function can_find_valid_resource_from_name()
    {
        $hasOne = new \Uteq\Move\Fields\HasOne('fixtures.user-resource');

        $this->assertTrue(class_exists($hasOne->resourceName));
        $this->assertTrue($hasOne->resourceName === UserResource::class);
    }

    /** @test */
    public function can_find_valid_resource_name()
    {
        $resourceClass = \Uteq\Move\Tests\Fixtures\UserResource::class;
        $hasOne = new \Uteq\Move\Fields\HasOne('User', 'user', $resourceClass);

        $this->assertTrue(class_exists($hasOne->resourceName));
        $this->assertTrue($hasOne->resourceName === $resourceClass);
    }

    /** @test */
    public function detects_resource_has_multiple_implementations()
    {
        $this->expectException(FindResourceException::class);

        new \Uteq\Move\Fields\HasOne('user-resource');
    }
}
