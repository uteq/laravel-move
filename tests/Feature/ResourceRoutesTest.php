<?php

namespace Uteq\Move\Tests\Feature;

use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use Uteq\Move\Facades\Move;
use Uteq\Move\Tests\Fixtures\UserResource;
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
