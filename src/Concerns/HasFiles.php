<?php

namespace Uteq\Move\Concerns;

use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\File\File;
use Uteq\Move\DataTransferObjects\MediaCollection;
use Uteq\Move\DataTransferObjects\MediaData;
use Uteq\Move\Fields\Field;
use Uteq\Move\Fields\Files;
use Uteq\Move\File\ResourceFile;
use Uteq\Move\File\ResourceFileContract;

trait HasFiles
{
    use WithFileUploads;
    use LoadableFiles;
    use FilesModal;

    public $file;
    public $tempUploadedFiles = [];
    public $deletedFiles = [];
    public int $rotatedFiles = 0;

    public function removeFile(string $field, int $i): void
    {
        if (! isset($this->deletedFiles[$field])) {
            $this->deletedFiles[$field] = [];
        }

        $this->deletedFiles[$field][$i] = true;
    }

    /**
     * Rotate is clockwise
     *
     * @param int $i
     * @param int $degrees
     */
    public function rotateFile(string $field, int $i, int $degrees = -90)
    {
        $field = $this->fields
            ->where('attribute', $field)
            ->first();

        $file = $this->loadFiles($field)->get($i);

        if (! $file) {
            // TODO add a message, unable to rotate.
            return null;
        }

        if (! exif_imagetype($file->getPath())) {
            // TODO add a message, unable to rotate.
            return null;
        }

        $image = Image::make($file->getPath());
        $image->setFileInfoFromPath($file->getPath());
        $image->rotate($degrees);
        $image->save();

        $this->rotatedFiles++;

        $this->loadFiles($field);
    }

    public function beforeStore(array $store)
    {
        $this->fields
            ->filter(fn ($field) => $field instanceof Files)
            ->each(function (Files $field) use (&$store) {
                $key = $field->attribute;

                $store[$key] = $this->getFilesPaths($field);
            });

        return $store;
    }

    public function getFilesPaths(Field $field)
    {
        $field->resolveForDisplay($this->model ?: static::newModel());

        $urls = $this->loadFiles($field, true)
            ->map(function (ResourceFileContract $file, $key) use ($field) {

                // Only add Resource file to be deleted
                if (isset($this->deletedFiles[$field->attribute][$key]) && ! $file instanceof ResourceFile) {
                    return null;
                }

                return new MediaData([
                    'id' => $file instanceof ResourceFile ? $file->id() : null,
                    'path' => $file->getPath(),
                    'name' => $file->getClientOriginalName(),
                    'action' => isset($this->deletedFiles[$field->attribute][$key]) ? 'delete' : 'create',
                ]);
            })
            ->toArray();

        return new MediaCollection($urls);
    }

    public function updatedFiles($data, string $key)
    {
        if (! isset($this->tempUploadedFiles[$key])) {
            $this->tempUploadedFiles[$key] = [];
        }

        $this->tempUploadedFiles[$key] = array_merge(
            array_values($this->tempUploadedFiles[$key]),
            array_values(is_array($data) ? $data : [$data])
        );
    }

    public function getTemporaryUrl(File $file)
    {
        return URL::temporarySignedRoute(move()::getPrefix() . '.preview-file', now()->addMinutes(30), [
            'filename' => $file->getFilename(),
        ]);
    }
}
