<?php

namespace Uteq\Move\File;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Livewire\TemporaryUploadedFile;

class TemporaryUploadedResourceFile extends UploadedFile implements ResourceFileContract
{
    use HasResourceFile;

    protected TemporaryUploadedFile $media;

    public function __construct(TemporaryUploadedFile $media)
    {
        $this->media = $media;
    }

    public function exists()
    {
        return $this->media->exists();
    }

    public function getPath()
    {
        return $this->media->getRealPath();
    }

    public function guessExtension()
    {
        return $this->media->guessExtension();
    }

    public function getClientOriginalName()
    {
        return $this->media->getClientOriginalName();
    }

    public function withVersion($version)
    {
        return $this;
    }

    public function getUrl(): string
    {
        return URL::temporarySignedRoute('move.preview-file', now()->addMinutes(30), [
            'filename' => $this->media->getFilename()
        ]);
    }
}
