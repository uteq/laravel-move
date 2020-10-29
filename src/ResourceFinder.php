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

    public function __construct(Filesystem $files, string $basePath)
    {
        $this->files = $files;
        $this->basePath = $basePath;
    }

    public function getClassNames($path)
    {
        return collect($this->files->allFiles(Str::start($path, $this->basePath)))
            ->map(function (SplFileInfo $file) {
                return app()->getNamespace() . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($file->getPathname(), app_path() . DIRECTORY_SEPARATOR)
                );
            })
            ->filter(function (string $class) {
                return is_subclass_of($class, Resource::class) &&
                    ! (new ReflectionClass($class))->isAbstract();
            });
    }
}
