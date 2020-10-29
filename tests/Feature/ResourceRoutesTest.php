<?php

namespace Uteq\Move\Tests\Feature;

use Uteq\Move\Tests\TestCase;

class ResourceRoutesTest extends TestCase
{
    /** @test */
    public function the_resource_route_works()
    {
        $this
            ->withoutMiddleware()
            ->get('move/fixtures/user-resource')
            ->assertOk()
            ->assertSeeText('User');
    }
}
