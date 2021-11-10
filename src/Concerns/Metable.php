<?php

namespace Uteq\Move\Concerns;

use Illuminate\Support\Arr;

trait Metable
{
    /**
     * The meta data for the element.
     *
     * @var array
     */
    public $meta = [];

    /**
     * Get additional meta information to merge with the element payload.
     *
     * @return array
     */
    public function meta($key = null, $default = null)
    {
        return $key
            ? Arr::get($this->meta, $key, $default)
            : $this->meta;
    }

    /**
     * Set additional meta information for the element.
     *
     * @param  array  $meta
     * @return $this
     */
    public function withMeta(array $meta)
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }
}
