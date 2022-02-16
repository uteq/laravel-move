<?php

namespace Uteq\Move\Fields\Concerns;

use Uteq\Move\Fields\Field;

trait WithStackableFields
{
    public function stackFields()
    {
        foreach ($this->getFields() as &$field) {
            if (! $field instanceof Field) {
                continue;
            }

            $field->stacked();
            $field->flex = false;
            $field->withMeta([
                'stacked_classes' => 'bg-white w-full last:border-b-0 border-gray-100',
            ]);
        }
    }
}
