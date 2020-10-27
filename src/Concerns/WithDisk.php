<?php

namespace Uteq\Move\Concerns;

trait WithDisk
{
    /**
     * @var string|null
     */
    protected $disk;

    /**
     * @param string|null $disk
     *
     * @return $this
     */
    public function withDisk(string $disk = null)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @return string|null
     */
    protected function disk(): ?string
    {
        return $this->disk;
    }
}
