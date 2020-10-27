<?php

namespace Uteq\Move\Concerns;

trait WithFilename
{
    /**
     * @var string|null
     */
    protected $filename;

    /**
     * @param string|null $filename
     *
     * @return $this
     */
    public function withFilename(string $filename = null)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string|null
     */
    protected function filename(): ?string
    {
        return $this->filename;
    }
}
