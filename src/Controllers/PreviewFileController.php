<?php

namespace Uteq\Move\Controllers;

use Illuminate\Routing\Controller;
use Livewire\FileUploadConfiguration;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Uteq\Move\Concerns\CanPretendToBeAFile;

class PreviewFileController extends Controller
{
    use CanPretendToBeAFile;

    public function __invoke($filename)
    {
        abort_unless(request()->hasValidSignature(), 401);

        if (is_numeric($filename)) {
            $media = Media::find($filename);

            return $this->pretendResponseIsFile($media->getPath());
        }

        return $this->pretendResponseIsFile(
            FileUploadConfiguration::storage()->path(
                FileUploadConfiguration::path($filename)
            )
        );
    }
}
