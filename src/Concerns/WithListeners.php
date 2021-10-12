<?php

namespace Uteq\Move\Concerns;

trait WithListeners
{
    public function addListener($key, $method = null)
    {
        if (is_array($key)) {
            $this->listeners = array_replace($key, $this->listeners);
        } else {

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
}
