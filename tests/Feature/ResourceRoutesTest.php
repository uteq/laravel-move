<?php

namespace Uteq\Move\Tests\Feature;

use Uteq\Move\Tests\TestCase;

class ResourceRoutesTest extends TestCase
{
    /** @test */
    public function the_resource_form_route_returns_ok()
    {
        $this->withoutExceptionHandling();

        $this
            ->withoutMiddleware()
            ->get('move/fixtures/user-resource')
            ->assertOk();
    }
}
