<?php

namespace Uteq\Move\Finders;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

class ModelFinder
{
    /**
     * @var Application
     */
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function find(string $path): string
    {
        if (! $this->isNamespaced($path)) {
            return $this->app->getNamespace() . 'Models\\'.  str_replace('/', '\\', $path);
        }

        return $path;
    }

    public function isNamespaced(string $path): bool
    {
        return Str::startsWith($path, [$this->app->getNamespace(), '\\']);
    }
}
