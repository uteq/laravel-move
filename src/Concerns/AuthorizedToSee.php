<?php

namespace Uteq\Move\Concerns;

use Closure;
use Illuminate\Http\Request;

trait AuthorizedToSee
{
    protected ?\Closure $seeCallback = null;

    public function authorizedToSee(Request $request)
    {
        return $this->seeCallback
            ? $this->callSeeCallback($request)
            : true;
    }

    public function canSee(Closure $callback): static
    {
        $this->seeCallback = $callback;

        return $this;
    }

    protected function callSeeCallback(...$args)
    {
        $seeable = $this->seeCallback;

        return $seeable(...$args);
    }
}
