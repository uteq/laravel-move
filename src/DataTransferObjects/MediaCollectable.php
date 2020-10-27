<?php

namespace Uteq\Move\DataTransferObjects;

interface MediaCollectable
{
    public function current(): MediaData;

    public function onlyDelete();

    public function withoutDelete();
}
