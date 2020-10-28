<?php

namespace Uteq\Move\Tests\Feature;

use Uteq\Move\Tests\Fixtures\User;
use Uteq\Move\Tests\Fixtures\UserResource;
use Uteq\Move\Tests\TestCase;

class ResourceFieldTest extends TestCase
{
    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed */
    private $user;

    private UserResource $userResource;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->make();
        $this->userResource = new UserResource($this->user);
    }

    /** @test */
    public function can_get_fields()
    {
        $this->assertIsArray($this->userResource->fields());
    }

    /** @test */
    public function can_resolve_fields()
    {
        $fields = $this->userResource->resolveFields($this->user);

        $this->assertNotEmpty($fields[0]->value);
    }
}
