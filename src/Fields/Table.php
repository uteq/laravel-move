<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Facades\Move;

class Table extends Panel
{
    public string $component = 'form.table';

    public string $tableResource;

    public function resource(string $resource)
    {
        if (class_exists($resource)) {
            $resource = Move::resourceKey($resource);
        }

        $this->tableResource = $resource;

        return $this;
    }
}
