<?php

namespace Uteq\Move\Concerns;

trait WithActionableFields
{
    public function action($store, $method, ...$args)
    {
        $result = $this->fields
            ->filter(fn ($field) => $field->store === $store)
            ->map(fn ($field) => $field->{$method}($this, $field, ...$args));

        if ($result->count() == 1) {
            return $result->first();
        }

        return $this;
    }
}
