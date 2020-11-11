<?php

namespace Uteq\Move\Tests\Feature;

use Uteq\Move\ResourceFinder;
use Uteq\Move\Tests\TestCase;

class ResourceFinderTest extends TestCase
{
    /** @test */
    public function can_find_classes()
    {
        $resources = app(ResourceFinder::class)->getClassNames(DIRECTORY_SEPARATOR . 'Fixtures');

        $this->assertStringContainsString($resources->first(), '\\Uteq\\Move\\Tests\\Fixtures\\OtherNamespace\\UserResource');
    }
}
