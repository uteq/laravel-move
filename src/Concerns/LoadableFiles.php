<?php

namespace Uteq\Move\Concerns;

use Livewire\TemporaryUploadedFile;
use Uteq\Move\Fields\Field;
use Uteq\Move\File\ResourceFile;
use Uteq\Move\File\TemporaryUploadedResourceFile;

trait LoadableFiles
{
    public $files = [];

    public function loadFiles(Field $field, $withDeleted = false)
    {
        $field->resolveForDisplay($this->model);

        $existingFiles = collect($field->media())
            ->map(fn ($file) => new ResourceFile($file));

        $newFiles = isset($this->tempUploadedFiles)
            ? collect($this->tempUploadedFiles[$field->attribute] ?? [])
                ->filter(fn ($value) => $value instanceof TemporaryUploadedFile)
                ->map(fn (TemporaryUploadedFile $file) => new TemporaryUploadedResourceFile($file))
            : [];

        $this->files = $existingFiles
            ->merge($newFiles)
            ->filter(fn ($file) => file_exists($file->getPath()))
            ->when($withDeleted === false && isset($this->deletedFiles), function ($collection) use ($field) {
                return $collection
                    ->filter(fn ($file, $key) => ! isset($this->deletedFiles[$field->attribute][$key]));
            })
            ->count();

        return $existingFiles
            ->merge($newFiles)
            ->when($withDeleted === false && isset($this->deletedFiles), function ($collection) use ($field) {
                return $collection->filter(
                    fn ($file, $key) =>
                ! isset($this->deletedFiles[$field->attribute][$key])
                );
            });
    }
}
