<?php

namespace Uteq\Move\Fields;

class Text extends Field
{
    public string $component = 'text-field';

    protected ?\Closure $externalLinkCallback = null;

    public function externalLink(\Closure $closure): static
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
