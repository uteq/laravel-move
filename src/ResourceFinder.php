<?php

namespace Uteq\Move;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class ResourceFinder
{
    /**
     * @var Filesystem
     */
    private Filesystem $files;
    private string $basePath;
    private string $namespace;
    private string $appPath;

    public function __construct(Filesystem $files, string $basePath)
    {
        $this->files = $files;
        $this->basePath = $basePath;
        $this->namespace = app()->getNamespace();
        $this->namespace = app_path();
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function setAppPath($appPath)
    {
        $this->appPath = $appPath;

        return $this;
    }

    public function getClassNames($path)
    {
        return collect($this->files->allFiles(Str::start($path, $this->basePath)))
            ->map(function (SplFileInfo $file) {
                return $this->namespace . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($file->getPathname(), $this->appPath . DIRECTORY_SEPARATOR)
                );
            })
            ->filter(function (string $class) {
                return is_subclass_of($class, Resource::class) &&
                    ! (new ReflectionClass($class))->isAbstract();
            })
            ->values();
    }
}
