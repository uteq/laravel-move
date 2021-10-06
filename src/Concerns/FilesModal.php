<?php

namespace Uteq\Move\Concerns;

trait FilesModal
{
    public $showFile = null;

    public function showFile($id)
    {
        $this->showFile = $id;
    }
}
