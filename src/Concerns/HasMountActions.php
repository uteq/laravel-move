<?php

namespace Uteq\Move\Concerns;

use Illuminate\Support\Collection;

trait HasMountActions
{
    protected Collection $beforeMount;
    protected Collection $afterMount;

    public function initializeHasMountActions(): void
    {
        $this->beforeMount = collect();
        $this->afterMount = collect();
    }

    protected function handleBeforeMount(): void
    {
        $this->beforeMount
            ->each(fn (\Closure $handler) => $handler());
    }

    protected function handleAfterMount(): void
    {
        $this->afterMount
            ->each(fn (\Closure $handler) => $handler());
    }

    public function beforeMount(\Closure $closure): void
    {
        $this->beforeMount->add($closure);
    }

    public function afterMount(\Closure $closure): void
    {
        $this->afterMount->add($closure);
    }
}
