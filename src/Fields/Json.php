<?php

namespace Uteq\Move\Fields;

class Json extends Field
{
    public string $component = 'json';

    public function isPlaceholder(bool $value = true): self
    {
        $this->hide($value);

        $this->isPlaceholder = $value;

        return $this;
    }
}
