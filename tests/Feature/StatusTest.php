<?php

namespace Uteq\Move\Tests\Feature;

use Uteq\Move\Status\TrashedStatus;
use Uteq\Move\Tests\TestCase;

class StatusTest extends TestCase
{
    /** @test */
    public function trashed_status_from_boolean_with_default()
    {
        $this->assertStringContainsString('', TrashedStatus::fromBoolean(false));
    }

    /** @test */
    public function trashed_status_from_boolean_with_trashed()
    {
        $this->assertStringContainsString('with', TrashedStatus::fromBoolean(true));
    }
}
