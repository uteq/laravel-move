<?php

namespace Uteq\Move\Controllers;

class MoveJavaScriptAssets
{
    use CanPretendToBeAFile;

    public function source()
    {
        return $this->pretendResponseIsFile(__DIR__.'/../../dist/move.js');
    }

    public function maps()
    {
        return $this->pretendResponseIsFile(__DIR__.'/../../dist/move.js.map');
    }
}
