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
        parent::__construct($media->getPath());

        $this->media = $media;
    }

    public function exists()
    {
        return file_exists($this->media->getPath());
    }

    public function id()
    {
        return $this->media->getKey();
    }

    public function getPath()
    {
        return $this->media->getPath();
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
