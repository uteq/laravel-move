<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Fields\Concerns\HasResource;

class HasOne extends Select
{
    use HasResource;

    public function __construct(string $name, string $attribute = null, string $resource = null)
    {
        $this->resourceName = $this->findResourceName($name, $resource);

        parent::__construct($name, $attribute);
    }
}
