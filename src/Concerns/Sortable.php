<?php

namespace Uteq\Move\Concerns;

trait Sortable
{
    /**
     * Indicates if the element should be sortable.
     *
     * @var bool
     */
    public bool $sortable = false;

    /**
     * Specify that this element should be sortable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function sortable($value = true)
    {
        $this->sortable = $value;

        return $this;
    }
}
