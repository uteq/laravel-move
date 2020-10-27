<?php

namespace Uteq\Move\Concerns;

trait WithHeadings
{
    /**
     * @var array
     */
    protected $headings = [];

    /**
     * @param array|mixed $headings
     * @param array       $only
     *
     * @return $this
     */
    public function withHeadings($headings = null)
    {
        $this->headings = $headings;

        return $this;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->headings;
    }
}
