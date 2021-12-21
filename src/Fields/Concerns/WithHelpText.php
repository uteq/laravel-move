<?php

namespace Uteq\Move\Fields\Concerns;

trait WithHelpText
{
    public ?string $helpText = null;

    public function helpText($helpText): \Uteq\Move\Fields\Panel
    {
        $this->helpText = $helpText;

        return $this;
    }

    public function getHelpText(): string|null
    {
        return $this->helpText;
    }
}
