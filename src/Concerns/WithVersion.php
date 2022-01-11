<?php

namespace Uteq\Move\Concerns;

trait WithVersion
{
    protected $version = 1;

    public function version($version)
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion()
    {
        $version = $this->version;

        return is_callable($version)
            ? $version($this)
            : $version;
    }
}
