<?php

namespace Uteq\Move\Concerns;

trait IsStacked
{
    /** Indicates if the field label and form element should sit on top of each other. */
    public bool $stacked = false;

    /** Stack the label above the field. */
    public function stacked(string|bool $stacked = true): static
    {
        if (is_string($stacked)) {
            $this->meta['stacked_classes'] = $stacked;

            $stacked = true;
        }

        $this->stacked = $stacked;
        $this->meta['display'] = $stacked ? 'stacked' : $this->meta['display'];

        return $this;
    }
}
