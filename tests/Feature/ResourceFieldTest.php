<?php

namespace Uteq\Move\Tests\Feature;

use Uteq\Move\Tests\Fixtures\User;
use Uteq\Move\Tests\Fixtures\UserResource;
use Uteq\Move\Tests\TestCase;

class ResourceFieldTest extends TestCase
{
    public function test_can_get_fields()
    {
        $user = User::factory()->make();
        $userResource = new UserResource($user);

        $this->assertIsArray($userResource->fields());
    }

//    public function test_can_resolve_fields()
//    {
//
//    }
}
