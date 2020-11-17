<?php

namespace Uteq\Move\Fields;

class Id extends Field
{
    public string $component = 'text-field';

    public function __construct(
        string $name = 'Id',
        string $attribute = 'id',
        callable $callableValue = null
    ) {
        parent::__construct($name, $attribute, $callableValue);

        $this->onlyOnIndex();
        $this->sortable();
    }

    /**
     * Resolve a BIGINT ID field as a string for compatibility with JavaScript.
     *
     * @return $this
     */
    public function asBigInt()
    {
        $this->callableValue = function ($id) {
            return (string) $id;
        };

        return $this;
    }
}
