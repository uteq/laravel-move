<?php

namespace Uteq\Move\Concerns;

use Closure;

trait HasRequired
{
    protected $requiredCallback;

    /**
     * Set the callback used to determine if the field is required.
     *
     * @param  Closure|bool  $callback
     * @return $this
     */
    public function required($callback = true)
    {
        $this->rules(['required']);
        $this->requiredCallback = $callback;

        return $this;
    }

    public function requiredOnCreateOnly()
    {
        $this->requiredCallback = function ($request, $model) {
            return ! ($model->id ?? false);
        };

        return $this;
    }

    /**
     * Determine if the field is required.
     */
    public function isRequired(): bool
    {
        return with($this->requiredCallback, function ($callback) {
            if ($callback === true || (is_callable($callback) && call_user_func($callback, request(), $this->resource))) {
                return true;
            }

            if (! empty($this->attribute) && is_null($callback)) {
                if (! isset($this->type)) {
                    return false;
                }

                if ($this->type === 'create') {
                    return in_array('required', $this->getCreationRules(request())[$this->attribute] ?? []);
                }

                if ($this->type === 'update') {
                    return in_array('required', $this->getUpdateRules(request())[$this->attribute] ?? []);
                }
            }

            return false;
        });
    }
}
