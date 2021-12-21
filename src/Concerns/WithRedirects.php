<?php

namespace Uteq\Move\Concerns;

trait WithRedirects
{
    protected $redirects;

    public function redirects($endpoints): static
    {
        $this->redirects = $endpoints;

        return $this;
    }

    public function getRedirects()
    {
        return $this->redirects;
    }
}
