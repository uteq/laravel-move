<?php

namespace Uteq\Move\Tests\Feature;

use Uteq\Move\Facades\Move;
use Uteq\Move\Tests\TestCase;

class MoveTest extends TestCase
{
    /** @test */
    public function can_get_all_move_resources()
    {
        $this->assertArrayHasKey('fixtures.user-resource', Move::all());
    }
}
