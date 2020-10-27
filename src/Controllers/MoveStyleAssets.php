<?php

namespace Uteq\Move\Controllers;

class MoveStyleAssets
{
    use CanPretendToBeAFile;

    public function source()
    {
        return $this->pretendResponseIsFile(__DIR__.'/../../dist/move.css', 'text/css');
    }
}
