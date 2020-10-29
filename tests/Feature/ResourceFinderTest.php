<?php

namespace Uteq\Move\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Uteq\Move\Facades\Move;
use Uteq\Move\ResourceFinder;
use Uteq\Move\Tests\TestCase;

class ResourceFinderTest extends TestCase
{
    /** @test */
    public function can_find_classes()
    {
        $finder = new ResourceFinder(new Filesystem(), dirname(dirname(__FILE__)));
        $finder->setNamespace('\\Uteq\\Move\\Tests\\');
        $finder->setAppPath(dirname(dirname(__FILE__)));

        $resources = $finder->getClassNames(DIRECTORY_SEPARATOR . 'Fixtures');

        $this->assertStringContainsString($resources->first(), '\\Uteq\\Move\\Tests\\Fixtures\\UserResource');
    }
}
