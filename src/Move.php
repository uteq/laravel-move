<?php

namespace Uteq\Move;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class Move
{
    public array $customResources = [];
    public array $customResourceNamespaces = [];
    public string $prefix = 'move';

    public function prefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function resource(string $alias, $class)
    {
        $this->customResources[$alias] = $class;

        return $this;
    }

    public function resourceNamespace(string $namespace, string $prefix)
    {
        $this->customResourceNamespaces[$prefix] = $namespace;

        return $this;
    }

    public function resolveResource(string $resource)
    {
        $resource = str_replace('/', '.', $resource);

        if (! app()->has('resource.' . $resource)) {
            throw new \Exception(sprintf(
                '%s: The requested resource %s does not exist or has not been added',
                __METHOD__,
                str_replace('.', '/', $resource),
            ));
        }

        return app()->get('resource.' . $resource);
    }

    public function getCustomResources()
    {
        return $this->customResources;
    }

    public function getCustomResourceNamespace()
    {
        return $this->customResourceNamespaces;
    }

    public function get(string $alias)
    {
        return $this->customResources[$alias] ?? null;
    }

    public function getByClass($class)
    {
        return array_flip($this->all())[$class] ?? null;
    }

    public function all()
    {
        $resources = [];
        foreach ($this->customResourceNamespaces as $prefix => $namespace) {
            $resources = array_merge(
                $resources,
                $this->getClassNames($this->generatePathFromNamespace($namespace))
                ->mapWithKeys(function ($class) use ($prefix) {
                    return [$prefix . '.' . Str::lower(Str::afterLast(rtrim($class, '\\'), '\\')) => $class];
                })
                ->toArray()
            );
        }

        return array_replace($resources, $this->customResources);
    }

    public function getClassNames($path)
    {
        return collect(app(Filesystem::class)->allFiles(base_path() . '/' . $path))
            ->map(function (SplFileInfo $file) {
                return app()->getNamespace() . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($file->getPathname(), app_path() . '/')
                );
            })
            ->filter(function (string $class) {
                return is_subclass_of($class, Resource::class) &&
                    ! (new ReflectionClass($class))->isAbstract();
            });
    }

    public static function generatePathFromNamespace($namespace)
    {
        $name = Str::replaceFirst(app()->getNamespace(), '', $namespace);

        return app('path') . '/' . str_replace('\\', '/', $name);
    }
}
