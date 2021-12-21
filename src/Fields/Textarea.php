<?php

namespace Uteq\Move\Fields;

use Closure;

class Textarea extends Field
{
    public string $component = 'textarea-field';

    /** The number of rows used for the textarea. */
    public int $rows = 5;

    public function __construct(
        string $name,
        string $attribute = null,
        Closure $valueCallback = null
    ) {
        parent::__construct($name, $attribute, $valueCallback);

        $this->hideFromIndex();
    }

    /** Set the number of rows used for the textarea. */
    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }
}
