<?php

namespace Uteq\Move\Fields\Concerns;

trait WithHelpText
{
    public ?string $helpText = null;

    public function helpText($helpText)
    {
        $this->helpText = $helpText;

        return $this;
    }

    public function getHelpText()
    {
        return $this->helpText;
    }
}
