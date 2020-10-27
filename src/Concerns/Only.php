<?php

namespace Uteq\Move\Concerns;

trait Only
{
    /**
     * @var array|null
     */
    protected $only;

    /**
     * @var bool
     */
    protected $onlyIndexFields = true;

    /**
     * @param array|mixed $columns
     *
     * @return $this
     */
    public function only($columns)
    {
        $this->only = \is_array($columns) ? $columns : \func_get_args();

        return $this;
    }

    /**
     * @return $this
     */
    public function onlyIndexFields()
    {
        $this->onlyIndexFields = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function allFields()
    {
        $this->onlyIndexFields = false;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getOnly()
    {
        return $this->only;
    }
}
