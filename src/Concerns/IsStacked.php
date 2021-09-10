<?php

namespace Uteq\Move\Concerns;

trait IsStacked
{
    /**
     * Indicates if the field label and form element should sit on top of each other.
     *
     * @var bool
     */
    public bool $stacked = false;

    /**
     * Stacks the label above the field.
     */
    public function stacked($stacked = true): static
    {
        if (is_string($stacked)) {
            $this->meta['stacked_classes'] = $stacked;

            $stacked = true;
        }

        $this->stacked = $stacked;
        $this->meta['stacked'] = $stacked;

        return $this;
    }
}
