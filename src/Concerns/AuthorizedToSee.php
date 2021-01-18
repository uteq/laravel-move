<?php

namespace Uteq\Move\Concerns;

use Closure;
use Illuminate\Http\Request;

trait AuthorizedToSee
{
    public \Closure $seeCallback;

    public function authorizedToSee(Request $request)
    {
        return $this->seeCallback
            ? $this->callSeeCallback($request)
            : true;
    }

    public function canSee(Closure $callback)
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
