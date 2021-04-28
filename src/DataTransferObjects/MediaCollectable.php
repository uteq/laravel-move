<?php

namespace Uteq\Move\DataTransferObjects;

interface MediaCollectable
{
    public function onlyDelete();

    public function withoutDelete();
}
