<?php

namespace Uteq\Move\TableActions;

use Uteq\Move\Actions\ExportToExcel;

class ExportToExcelAction extends ExportToExcel
{
    public string $name = 'Download for excel';

    public function map($row): array
    {
        $fields = collect($this->resource->visibleFields('index', $row))
            ->mapWithKeys(fn ($field) => [$field->name => $field->applyResourceData($row)->value])
            ->toArray();

        return $fields;
    }
}
