<?php

namespace Uteq\Move\Concerns;

trait WithListeners
{
    public function addListener($key, $method = null)
    {
        if (isset($this->listeners[$key])) {
            throw new \Exception(sprintf(
                '%s: The given listener `%s` already exists',
                __METHOD__,
                $key,
            ));
        }

        $this->listeners = array_replace([$key => $method ?: $key], $this->listeners ?? []);
    }
}
