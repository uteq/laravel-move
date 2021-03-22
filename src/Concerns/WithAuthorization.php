<?php

namespace Uteq\Move\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

trait WithAuthorization
{
    public static ?string $gate = null;

    public static function policy()
    {
        return Gate::getPolicyFor(static::newModel());
    }

    public static function policyMethodExists($ability, $model = null)
    {
        return method_exists($model ?? static::policy(), $ability);
    }

    /**
     * Determines whether the Resource has authorization
     *
     * @return bool
     */
    public static function canAuthorize()
    {
        return null !== static::policy();
    }

    public function authorizeTo($ability, $model = null, $arguments = [])
    {
        throw_unless($this->can($ability, $model, $arguments), AuthorizationException::class);
    }

    public function can($ability, $model = null, $arguments = [])
    {
        if (! static::canAuthorize()) {
            return true;
        }

        return Gate::check($ability, array_merge($arguments, [$model ?: $this->resource, static::$gate ?: $this->name()]));
    }
}
