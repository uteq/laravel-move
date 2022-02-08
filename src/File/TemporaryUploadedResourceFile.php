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

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch
     */
    public function getPath(): bool|string
    {
        /** @psalm-suppress FalsableReturnStatement */
        return $this->media->getRealPath();
    }

    public function guessExtension(): string
    {
        return $this->media->guessExtension();
    }

    public function getClientOriginalName(): string
    {
        return $this->media->getClientOriginalName();
    }

    public function withVersion($version): static
    {
        return $this;
    }

    public function getUrl(): string
    {
        return URL::temporarySignedRoute(move()::getPrefix() . '.preview-file', now()->addMinutes(30), [
            'filename' => $this->media->getFilename(),
        ]);
    }
}
