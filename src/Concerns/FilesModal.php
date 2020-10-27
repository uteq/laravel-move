<?php

namespace Uteq\Move\Concerns;

trait FilesModal
{
    public $showFile = false;

    public function showFile()
    {
        $this->showFile = true;
    }
}
