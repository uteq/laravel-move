<?php

namespace Uteq\Move\Fields;

class Id extends Field
{
    public string $component = 'text-field';

    /**
     * Id constructor.
     * @param string $name
     * @param string $attribute
     * @param callable|null $valueCallback
     */
    public function __construct(
        string $name = 'Id',
        string $attribute = 'id',
        callable $valueCallback = null
    ) {
        parent::__construct($name, $attribute, $valueCallback);

        $this->onlyOnIndex();
        $this->sortable();
    }

    /**
     * @return $this
     */
    public function asBigInt()
    {
        $this->valueCallback = fn ($id) => (string) $id;

        return $this;
    }
}
