<?php

namespace Uteq\Move;

use Illuminate\Support\Str;

class Move
{
    public array $customResources = [];
    public array $customResourceNamespaces = [];
    public string $prefix = 'move';
    public bool $useSidebarGroups = true;

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

    public function groupedResources()
    {
        return collect($this->resources())
            ->filter(fn ($item) => $item::$group)
            ->mapToGroups(function ($item) {
                return [$item::$group => $item];
            });
    }

    public function resources()
    {
        $resources = [];
        foreach ($this->all() as $resource) {
            $resources[] = $this->resolveResource($resource);
        }

        return $resources;
    }

    public function resourceRoute(string $alias)
    {
        $resourceName = $this->fullResourceName($this->getByClass($alias));

        return str_replace('.', '/', $resourceName);
    }

    public function resourceNamespace(string $namespace, string $prefix = '')
    {
        $this->customResourceNamespaces[$prefix] = $namespace;

        return $this;
    }

    public function activeResource()
    {
        return Move::resolveResource(request()->route()->parameter('resource'));
    }

    public function resolveResource(string $resource)
    {
        if ($this->getByClass($resource)) {
            $resource = $this->getByClass($resource);
        }

        $resource = $this->fullResourceName($resource);

        if (! app()->has($resource)) {
            throw new \Exception(sprintf(
                '%s: The requested resource %s does not exist or has not been added',
                __METHOD__,
                str_replace('.', '/', $resource),
            ));
        }

        return app()->get($resource);
    }

    public function fullResourceName(string $resource)
    {
        return str_replace('/', '.', Str::start($resource, $this->prefix .'.'));
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
                        $prefix = empty($prefix) ? null : $prefix . '.';

                        return [$prefix . Str::lower(Str::afterLast(rtrim($class, '\\'), '\\')) => $class];
                    })
                    ->toArray()
            );
        }

        return array_replace($resources, $this->customResources);
    }

    public function useSidebarGroups(bool $bool = true)
    {
        $this->useSidebarGroups = $bool;

        return $this;
    }

    public function hasSidebarGroups()
    {
        return $this->useSidebarGroups;
    }

    public function getClassNames($path)
    {
        return app(ResourceFinder::class)->getClassNames($path);
    }

    public static function generatePathFromNamespace($namespace)
    {
        $name = Str::replaceFirst(app()->getNamespace(), '', $namespace);

        return app('path') . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $name);
    }

    public function routeMiddlewares()
    {
        $middlewares = config('move.middlewares');

        if (config('move.auth.enabled')) {
            $middlewares = array_merge($middlewares, config('move.auth.middlewares'));
        }

        return $middlewares;
    }

    public function headerSearch()
    {
        return 'move::livewire.header-search';
    }

    public function layout()
    {
        return config('move.layout');
    }

    public function styles()
    {
        return <<<HTML
<link rel="stylesheet" type="text/css" href="{$this->cssAssets()}" />
HTML;
    }

    public function scripts()
    {
        return <<<HTML
<script src="{$this->jsAssets()}"></script>
HTML;
    }

    public function cssAssets()
    {
        return asset('/move/move.css');
    }

    public function jsAssets()
    {
        return '/move/move.js';
    }
}
