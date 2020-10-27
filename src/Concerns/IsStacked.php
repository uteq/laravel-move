<?php

namespace Uteq\Move\Concerns;

trait IsStacked
{
    /**
     * Indicates if the field label and form element should sit on top of each other.
     *
     * @var bool
     */
    public $stacked = false;

    /**
     * Stack the label above the field.
     *
     * @param bool $stack
     *
     * @return $this
     */
    public function stacked($stack = true)
    {
        $this->stacked = $stack;

        return $this;
    }
}
