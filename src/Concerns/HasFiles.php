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
    public array $rotatedFiles = [];

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
        $field = $this->fields()
            ->firstWhere('attribute', $field);

        /** @var ResourceFile $file */
        $file = $this->loadFiles($field)->get($i);

        if (! $file) {
            session()->flash('error', __('Something went wrong rotating the image. No file loaded'));

            return null;
        }

//        if (! exif_imagetype($file->getPath())) {
//            session()->flash('error', __('Something went wrong rotating the image. Unable to retrieve correct data from file path'));
//            session()->flash('timout', 5000);
//
//            return null;
//        }

        if (method_exists($file, 'rotate')) {
            $file->rotate($degrees);
        } else {
            $image = Image::make($file->getPath());
            $image->setFileInfoFromPath($file->getPath());
            $image->rotate($degrees);
            $image->save();
        }

        $this->rotatedFiles[$i] ??= 0;
        $this->rotatedFiles[$i]++;

        $this->loadFiles($field);
    }

    public function beforeStore(array $store)
    {
        $this->fields()
            ->filter(fn ($field) => $field instanceof Files)
            ->each(function (Files $field) use (&$store) {
                $key = $field->attribute;

                $store[$key] = $this->getFilesPaths($field);
            });

        return $store;
    }

    public function getFilesPaths(Field $field)
    {
        $field->applyResourceData($this->model ?: static::newModel());

        $urls = $this->loadFiles($field, true)
            ->map(function (ResourceFileContract $file, $key) use ($field) {

                $inDelete = isset($this->deletedFiles[$field->attribute][$key]);

                // Only add Resource file to deleted if it already exists
                if ($inDelete && ! $file instanceof ResourceFile) {
                    return null;
                }

                return new MediaData([
                    'id' => $file instanceof ResourceFile ? $file->id() : null,
                    'path' => $file->getPath(),
                    'name' => $file->getClientOriginalName(),
                    'action' => $inDelete ? 'delete' : 'create',
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
