<?php

namespace Uteq\Move\Concerns;

trait WithActionableFields
{
    public function action($store, $method, ...$args)
    {
        $this->fields
            ->filter(fn ($field) => $field->store === $store)
            ->each(fn ($field) => $field->{$method}($this, $field, ...$args));

        return $this;
    }
}
