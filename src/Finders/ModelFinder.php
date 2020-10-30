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

    public function find(string $path)
    {
        if (! $this->isNamespaced($path)) {
            return $this->app->getNamespace() . 'Models\\'.  str_replace('/', '\\', $path);
        }

        return $path;
    }

    public function isNamespaced($path)
    {
        return Str::startsWith($path, [$this->app->getNamespace(), '\\']);
    }
}
