<?php

namespace Uteq\Move\Concerns;

trait WithChunkCount
{
    public static int $chunkCount = 0;

    /**
     * @param int $chunkCount
     *
     * @return $this
     */
    public function withChunkCount(int $chunkCount)
    {
        static::$chunkCount = $chunkCount;

        return $this;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return static::$chunkCount;
    }
}
