<?php

namespace Uteq\Move\Fields;

use Closure;

class Text extends Field
{
    public string $component = 'text-field';

    protected ?Closure $externalLinkCallback = null;

    public function externalLink(Closure $closure): static
    {
        $this->externalLinkCallback = $closure;

        return $this;
    }

    public function getExternalLink(array $args = []): ?string
    {
        return $this->externalLinkCallback
            ? ($this->externalLinkCallback)($this, $args)
            : null;
    }
}
