<?php

namespace Uteq\Move\Concerns;

use Opis\Closure\SerializableClosure;

trait WithClosures
{
    protected $unserializedClosures = [];

    protected static $serializedClasses = [];

    public function initializeWithClosures()
    {
        $this->beforeMount(fn() => $this->serializeClosures());
    }

    protected function serializeClosures()
    {
        // No need to serialize again when already serialized
        //  This makes it possible to create a manual serialization point, even before mount
        if (isset(static::$serializedClasses[$this->unique()])) {
            return;
        }

        $this->doSerializeClosures(array_flip($this->closures ?? []), $this);

        static::$serializedClasses[$this->unique()] = true;
    }

    protected function doSerializeClosures(array $closures, object $model)
    {
        foreach ($closures ?? [] as $key => $closure) {
            if ($model->{$key} == null) {
                continue;
            }

            if (is_array($model->{$key})) {
                $model->{$key} = (array) $this->doSerializeClosures($model->{$key}, (object) $model->{$key});
            } elseif (is_callable($model->{$key})) {
                $model->{$key} = $this->serializeClosure($model->{$key});
            }
        }

        return $model;
    }

    protected function serializeClosure($closure): string
    {
        if (is_string($closure)) {
            return $closure;
        }

        if (is_callable($closure) && ! $closure instanceof \Closure) {
            $closure = fn (...$args) => $closure(...$args);
        }

        return \Opis\Closure\serialize(new SerializableClosure($closure));
    }

    protected function unserializeClosures()
    {
        $closures = [];

        foreach ($this->closures ?? [] as $key => $closure) {
            $closures[$key] = $this->unserializeClosure($closure);
        }

        return $closures;
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
                if ($value == null) {
                    continue;
                }

                try {
                    $this->unserializedClosures[$closure][$key] = \Opis\Closure\unserialize($value);
                } catch (\Throwable $t) {
                    dd($t, $value);
                }
            }

            return $this->unserializedClosures[$closure];
        }

        return null;
    }

    public function closure(string $closure, $default = null, ...$args): mixed
    {
        if (! $this->$closure instanceof \Closure) {
            return $this->$closure;
        }

        $closure = $this->unserializeClosure($closure);

        return $closure ? $closure(...$args) : $default;
    }

    private function unique()
    {
        return static::class . '.' . $this->resource;
    }

    public function addClosure($closure)
    {
        $this->closures[] = $closure;

        return $this;
    }
}
