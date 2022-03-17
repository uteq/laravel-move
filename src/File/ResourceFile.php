<?php

namespace Uteq\Move\File;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\File;

class ResourceFile extends File implements ResourceFileContract
{
    use HasResourceFile;

    protected Media $media;

    protected $version = null;

    public function __construct(Media $media)
    {
        $this->media = $media;

        parent::__construct($this->getPath());
    }

    public function getPath(): string
    {
        if (! in_array($this->media->disk, ['local', 'public'])) {
            $path = str_replace(
                DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR,
                sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->media->uuid
            );

            if (! file_exists($path)) {
                $dir = dirname($path);
                if (! file_exists($dir)) {
                    mkdir($dir, null, true);
                }

                if (! $this->media->getFullUrl()) {
                    return null;
                }

                file_put_contents($path, file_get_contents($this->media->getFullUrl()));
            }

            return $path;
        } else {
            return $this->media->getPath();
        }
    }

    public function exists()
    {
        return file_exists($this->getPath());
    }

    public function id()
    {
        return $this->media->getKey();
    }

    public function getClientOriginalName()
    {
        return $this->media->file_name;
    }

    public function withVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->media->getFullUrl() . ($this->version !== null ? '?v=' . $this->version : '');
    }
}
