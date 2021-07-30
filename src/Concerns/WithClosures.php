<?php

namespace Uteq\Move\Concerns;

use Opis\Closure\SerializableClosure;

trait WithClosures
{
    protected $unserializedClosures = [];

    protected function serializeClosures()
    {
        foreach ($this->closures ?? [] as $closure) {
            if (is_array($this->{$closure})) {
                foreach ($this->{$closure} as $key => $value) {
                    $this->{$closure}[$key] = $this->serializeClosure($value);
                }
            } elseif ($this->{$closure} instanceof \Closure) {
                $this->{$closure} = $this->serializeClosure($this->{$closure});
            }
        }
    }

    protected function serializeClosure(\Closure $closure): string
    {
        return \Opis\Closure\serialize(new SerializableClosure($closure));
    }

    protected function unserializeClosure($closure)
    {
        if (isset($this->unserializedClosures[$closure])) {
            return ($this->unserializedClosures[$closure]);
        }

        if (is_string($this->{$closure} ?? null)) {
            return $this->unserializedClosures[$closure] = \Opis\Closure\unserialize($this->{$closure});
        }

        if (is_array($this->{$closure} ?? null)) {
            foreach ($this->{$closure} as $key => $value) {
                $this->unserializedClosures[$closure][$key] = \Opis\Closure\unserialize($value);
            }

            return $this->unserializedClosures[$closure];
        }

        return null;
    }

    public function closure(string $closure, $default, ...$args): mixed
    {
        $closure = $this->unserializeClosure($closure);

        return $closure ? $closure(...$args) : $default;
    }
}
