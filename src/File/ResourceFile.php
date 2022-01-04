<?php

namespace Uteq\Move\File;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\File;
use Uteq\Move\Concerns\CanPretendToBeAFile;

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

    public function getPath()
    {
        $path = $this->media->getPath();

        if (file_exists($path)) {
            return $path;
        }

        $path = str_replace(
            DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->media->uuid
        );

        if (file_exists($path)) {
            return $path;
        }

        $dir = dirname($path);

        if (! file_exists($dir)) {
            if (! mkdir($dir, recursive: true) && ! is_dir($dir)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
        }

        $url = $this->media->getDiskDriverName() === 's3'
            ? $this->media->getTemporaryUrl(now()->addMinute())
            : $this->media->getFullUrl();

        file_put_contents($path, file_get_contents($url));

        return $path;
    }

    public function getMimeType()
    {
        return $this->media->mime_type ?: parent::getMimeType();
    }

    public function exists(): bool
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

    public function withVersion($version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getUrl(): string
    {
        $file = $this->media->getFullUrl() . ($this->version !== null ? '?v=' . $this->version : '');

        if (file_exists($file)) {
            return $file;
        }

        /** @psalm-suppress UndefinedInterfaceMethod */
        return url()->temporarySignedRoute(
            name: move()::getPrefix() . '.preview-file',
            expiration: now()->addMinutes(60),
            parameters: $this->media->id,
        );
    }

    public function get()
    {
        return $this->media->get();
    }

    public function rotate(int $degrees): void
    {
        $image = Image::make($this->getPath());
        $image->setFileInfoFromPath($this->getPath());
        $image->rotate($degrees);
        $image->save();

        Storage::disk($this->media->disk)
            ->put($this->media->id . '/' . $this->media->file_name, (string) $image);
    }
}
