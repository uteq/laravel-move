<?php

namespace Uteq\Move\Concerns;

trait FilesModal
{
    public $showFile = null;

    public function showFile($id): void
    {
        $this->showFile = $id;
    }
}
