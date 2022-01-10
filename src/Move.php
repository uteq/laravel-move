<?php

namespace Uteq\Move;

use Illuminate\Support\Str;
use Uteq\Move\Collections\ResourceCollection;
use Uteq\Move\Exceptions\UnknownResourceException;

class Move
{
    public array $customResources = [];
    public array $customResourceNamespaces = [];
    public string $prefix = 'move';
    public bool $useSidebarGroups = true;
    public ?array $resources = null;
    public ?bool $loadResourceRoutes = null;
    public ?bool $useTestStore = false;
    public ?bool $wrapTableContent = false;
    public string $themeColor = 'green';
    protected $prefixes = [];
    public $registeredTable;

    public function registerTable($table): void
    {
        $this->registeredTable = $table;
    }

    public function getRegisteredTable()
    {
        return $this->registeredTable;
    }

    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getPrefix($class = null)
    {
        return $class
            ? $this->prefixes[$this->getClassNamespace($class)] ?? $this->prefix
            : $this->prefix;
    }

    public function getClassNamespace($class): ?string
    {
        foreach ($this->getPrefixes() as $namespace => $_prefix) {
            if (Str::startsWith($class, $namespace)) {
                return $namespace;
            }
        }

        return $class;
    }

    public function getPrefixes()
    {
        return $this->prefixes;
    }

    public function wrapTableContent($wrapTableContent = true): static
    {
        $this->wrapTableContent = $wrapTableContent;

        return $this;
    }

    public function getWrapTableContent($wrapTableContent = true): bool|null
    {
        return $this->wrapTableContent;
    }

    public function useTestStore(bool $useTestStore = true): static
    {
        $this->useTestStore = $useTestStore;

        return $this;
    }

    public function usesTestStore(): bool|null
    {
        return $this->useTestStore;
    }

    public function themeColor(string $color): static
    {
        $this->themeColor = $color;

        return $this;
    }

    public function getThemeColor(): string
    {
        return $this->themeColor;
    }

    public function resource(string $alias, $class): static
    {
        $this->customResources[$alias] = $class;

        return $this;
    }

    public function resources(): ResourceCollection
    {
        return new ResourceCollection(
            collect($this->all())
                ->map(fn ($resource) => $this->resolveResource($resource))
        );
    }

    public function resourceRoute(string $alias): string
    {
        $resourceName = $this->fullResourceName($this->getByClass($alias));

        return str_replace('.', '/', $resourceName);
    }

    public function resourceKey(string $alias)
    {
        return $this->fullResourceName($this->getByClass($alias));
    }

    public function resourceNamespace(string $namespace, string $prefix = '', $movePrefix = null): static
    {
        $this->customResourceNamespaces[$prefix] = $namespace;

        if ($movePrefix) {
            $this->prefixes[$namespace] = $movePrefix;
        }

        return $this;
    }

    public function activeResource()
    {
        return Move::resolveResource(request()->route()->parameter('resource'));
    }

    public function activeResourceGroup()
    {
        if (! $activeResource = $this->activeResource()) {
            return null;
        }

        return $activeResource::$group;
    }

    public function resolveResource(string $resource = null, mixed $component = null)
    {
        if (! $resource) {
            return null;
        }

        if ($this->getByClass($resource)) {
            $resource = $this->getByClass($resource);
        }

        $resource = $this->fullResourceName($resource);

        if (! app()->has($resource)) {
            throw new UnknownResourceException(sprintf(
                '%s: The requested resource %s does not exist or has not been added',
                __METHOD__,
                str_replace('.', '/', $resource),
            ));
        }

        $resource = app()->get($resource);
        $resource->component = $component;

        return $resource;
    }

    public function fullResourceName(string $resource): string
    {
        return str_replace('/', '.', Str::start($resource, $this->prefix .'.'));
    }

    public function getCustomResources(): array
    {
        return $this->customResources;
    }

    public function getCustomResourceNamespace(): array
    {
        return $this->customResourceNamespaces;
    }

    public function get(string $alias)
    {
        return $this->customResources[$alias] ?? null;
    }

    /**
     * @return (int|string)|null
     *
     * @psalm-return array-key|null
     */
    public function getByClass(string $class)
    {
        return array_flip($this->all())[$class] ?? null;
    }

    public function all(): array
    {
        if (! $this->resources) {
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

            $this->resources = array_replace($resources, $this->customResources);
        }

        return $this->resources;
    }

    public function find($resource): array
    {
        if (collect($this->all())->first(fn ($value) => $value === $resource)) {
            return [$resource];
        }

        return collect($this->all())
            ->filter(fn ($_class, $name) => Str::contains($name, $resource))
            ->toArray();
    }

    public function useSidebarGroups(bool $bool = true): static
    {
        $this->useSidebarGroups = $bool;

        return $this;
    }

    public function hasSidebarGroups(): bool
    {
        return $this->useSidebarGroups;
    }

    public function loadResourceRoutes(bool $value = true): static
    {
        $this->loadResourceRoutes = $value;

        return $this;
    }

    public function shouldLoadResourceRoutes()
    {
        return $this->loadResourceRoutes !== null
            ? $this->loadResourceRoutes
            : config('move.load_resource_routes');
    }

    public function getClassNames($path)
    {
        return app(ResourceFinder::class)->getClassNames($path);
    }

    public static function generatePathFromNamespace($namespace): string
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

    public function headerSearch(): string
    {
        return 'move::livewire.header-search';
    }

    public function layout()
    {
        return config('move.layout');
    }

    public function styles(): string
    {
        return <<<HTML
<link rel="stylesheet" type="text/css" href="{$this->cssAssets()}" />
HTML;
    }

    public function scripts(): string
    {
        return <<<HTML
<script src="{$this->jsAssets()}" defer></script>
HTML;
    }

    public function cssAssets(): string
    {
        return asset('/move/move.css');
    }

    public function jsAssets(): string
    {
        return '/move/move.js';
    }
}
