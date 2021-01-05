<?php

namespace Uteq\Move\Concerns;

use Illuminate\Support\Collection;

trait HasMountActions
{
    protected Collection $beforeMount;
    protected Collection $afterMount;

    public function initializeHasMountActions()
    {
        $this->beforeMount = collect();
        $this->afterMount = collect();
    }

    protected function handleBeforeMount()
    {
        $this->beforeMount
            ->each(fn (\Closure $handler) => $handler());
    }

    protected function handleAfterMount()
    {
        $this->afterMount
            ->each(fn (\Closure $handler) => $handler());
    }

    public function beforeMount(\Closure $closure)
    {
        $this->beforeMount->add($closure);
    }

    public function afterMount(\Closure $closure)
    {
        $this->afterMount->add($closure);
    }
}
