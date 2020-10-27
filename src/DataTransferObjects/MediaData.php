<?php

namespace Uteq\Move\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class MediaData extends DataTransferObject
{
    public ?int $id;
    public string $name;
    public string $path;
    public string $action = 'create';
}
