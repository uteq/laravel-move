<?php

namespace Uteq\Move\Controllers;

use Illuminate\Routing\Controller;
use Livewire\FileUploadConfiguration;
use Uteq\Move\Concerns\CanPretendToBeAFile;

class PreviewFileController extends Controller
{
    use CanPretendToBeAFile;

    public function __invoke($filename)
    {
        abort_unless(request()->hasValidSignature(), 401);

        return $this->pretendResponseIsFile(
            FileUploadConfiguration::storage()->path(
                FileUploadConfiguration::path($filename)
            )
        );
    }
}
