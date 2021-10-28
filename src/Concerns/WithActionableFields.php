<?php

namespace Uteq\Move\Concerns;

trait WithActionableFields
{
    public function action($storeKey, $method, ...$args)
    {
        $store = $this->store;

        $result = $this->fields()
            ->filter(fn ($field) => $field->store === $storeKey)
            ->map(function ($field) use ($method, $args, $store) {
                $this->store = $store;

                return $field->{$method}($this, $field, ...$args);
            });

        if ($result->count() == 1) {
            return $result->first();
        }

        return $this;
    }
}
