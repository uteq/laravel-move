<?php

namespace Uteq\Move\Facades;

use Illuminate\Support\Facades\Facade;
use Uteq\Move\Resource;

/**
 * Class Move
 * @method static void resource(string $alias, $class)
 * @method static void resourceNamespace(string $namespace, string $prefix)
 * @method static Resource resolveResource(string $resource)
 * @method static getCustomResources()
 * @method static getCustomResourceNamespace()
 * @method static get(string $alias)
 * @method static getByClass($class)
 * @method static array all()
 * @method static getClassNames($path)
 * @method static prefix(string $prefix)
 * @method static useSidebarGroups(bool $bool = true)
 * @method static Move loadResourceRoutes(bool $value = true)
 * @package Uteq\Move\Facades
 * @see \Uteq\Move\Move
 */
class Move extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'move';
    }
}
