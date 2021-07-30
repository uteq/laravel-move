<?php

namespace Uteq\Move\Fields;

class Row extends Panel
{
    public string $component = 'form.row';

    public function __construct(array $fields)
    {
        parent::__construct(null, $fields);
    }

    public function init()
    {
        /** @var \Support\Fields\Field $field */
        foreach ($this->fields as &$field) {
            $field->stacked();
            $field->flex = false;
            $field->withMeta([
                'stacked_classes' => 'bg-white w-full last:border-b-0 border-gray-100',
            ]);
        }
    }
}
