<?php

namespace Uteq\Move\Fields;

use Closure;

class Id extends Field
{
    public string $component = 'text-field';

    public function __construct(
        string $name = 'Id',
        string $attribute = 'id',
        Closure $valueCallback = null
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
        $this->valueCallback = fn ($id): string => (string) $id;

        return $this;
    }
}
