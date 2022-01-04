<?php

namespace Uteq\Move\Concerns;

trait WithSortable
{
    protected array $sortableCallbacks = [];

    public function sort($values)
    {
        $callbacks = $this->fields()
            ->flatMap(fn ($field) => $field->getSortableCallbacks());

        foreach ($callbacks as $callback) {
            $callback($values, $this);
        }
    }
}
