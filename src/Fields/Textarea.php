<?php

namespace Uteq\Move\Fields;

class Textarea extends Field
{
    public string $component = 'textarea-field';

    /** The number of rows used for the textarea. */
    public int $rows = 5;

    public function __construct(string $name, string $attribute = null, callable $callableValue = null)
    {
        parent::__construct($name, $attribute, $callableValue);

        $this->hideFromIndex();
    }

    /** Set the number of rows used for the textarea. */
    public function rows(int $rows): self
    {
        $this->rows = $rows;

        return $this;
    }
}
