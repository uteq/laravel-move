<?php

namespace Uteq\Move\Fields\Concerns;

use Closure;
use Illuminate\Http\Request;

trait ShowsConditionally
{
    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var Closure|bool
     */
    protected $showOnIndex = true;

    /**
     * Indicates if the element should be shown on the detail view.
     *
     * @var Closure|bool
     */
    protected $showOnDetail = true;

    /**
     * Indicates if the element should be shown on the creation view.
     *
     * @var Closure|bool
     */
    protected $showOnCreation = true;

    /**
     * Indicates if the element should be shown on the update view.
     *
     * @var Closure|bool
     */
    protected $showOnUpdate = true;

    /**
     * Indicates if the element is only shown on the detail screen.
     *
     * @var bool
     */
    protected bool $onlyOnDetail = false;

    /**
     * Specify that the element should be hidden from the index view.
     *
     * @param  Closure|bool  $callback
     * @return $this
     */
    public function hideFromIndex($callback = true)
    {
        $this->showOnIndex = is_callable($callback)
            ? fn () => ! call_user_func_array($callback, func_get_args())
            : ! $callback;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the detail view.
     *
     * @param  Closure|bool  $callback
     * @return $this
     */
    public function hideFromDetail($callback = true)
    {
        $this->showOnDetail = is_callable($callback)
            ? fn () => ! call_user_func_array($callback, func_get_args())
            : ! $callback;

        return $this;
    }

    public function hideFromForm($callback = true)
    {
        $this->showOnCreation = is_callable($callback)
            ? fn () => ! call_user_func_array($callback, func_get_args())
            : ! $callback;

        $this->showOnUpdate = is_callable($callback)
            ? fn () => ! call_user_func_array($callback, func_get_args())
            : ! $callback;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the creation view.
     *
     * @param  Closure|bool  $callback
     * @return $this
     */
    public function hideWhenCreating($callback = true)
    {
        $this->showOnCreation = is_callable($callback)
            ? fn () => ! call_user_func_array($callback, func_get_args())
            : ! $callback;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the update view.
     *
     * @param  Closure|bool  $callback
     * @return $this
     */
    public function hideWhenUpdating($callback = true)
    {
        $this->showOnUpdate = is_callable($callback)
            ? fn () => ! call_user_func_array($callback, func_get_args())
            : ! $callback;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the index view.
     *
     * @param  Closure|bool  $callback
     * @return $this
     */
    public function showOnIndex($callback = true)
    {
        $this->showOnIndex = $callback;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the detail view.
     *
     * @param  Closure|bool  $callback
     * @return $this
     */
    public function showOnDetail($callback = true)
    {
        $this->showOnDetail = $callback;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the creation view.
     *
     * @param  Closure|bool  $callback
     * @return $this
     */
    public function showOnCreating($callback = true)
    {
        $this->showOnCreation = $callback;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the update view.
     *
     * @param  Closure|bool  $callback
     * @return $this
     */
    public function showOnUpdating($callback = true)
    {
        $this->showOnUpdate = $callback;

        return $this;
    }

    public function showOnForm($callback = true)
    {
        $this->showOnUpdate = $callback;
        $this->showOnCreation = $callback;

        return $this;
    }

    /**
     * Check for showing when updating.
     *
     * @param  Request  $request
     * @param  mixed  $resource
     * @return bool
     */
    public function isShownOnUpdate(Request $request, $resource): bool
    {
        if (is_callable($this->showOnUpdate)) {
            $this->showOnUpdate = call_user_func($this->showOnUpdate, $request, $resource);
        }

        return $this->showOnUpdate;
    }

    /**
     * Check showing on index.
     *
     * @param  Request  $request
     * @param  mixed  $resource
     * @return bool
     */
    public function isShownOnIndex(Request $request, $resource): bool
    {
        if (is_callable($this->showOnIndex)) {
            $this->showOnIndex = call_user_func($this->showOnIndex, $request, $resource);
        }

        return $this->showOnIndex;
    }

    /**
     * Check showing on detail.
     *
     * @param  Request  $request
     * @param  mixed  $resource
     * @return bool
     */
    public function isShownOnDetail(Request $request, $resource): bool
    {
        if (is_callable($this->showOnDetail)) {
            $this->showOnDetail = call_user_func($this->showOnDetail, $request, $resource);
        }

        return $this->showOnDetail;
    }

    /**
     * Check for showing when creating.
     *
     * @param  Request  $request
     * @return bool
     */
    public function isShownOnCreation(Request $request): bool
    {
        if (is_callable($this->showOnCreation)) {
            $this->showOnCreation = call_user_func($this->showOnCreation, $request);
        }

        return $this->showOnCreation;
    }

    /**
     * Specify that the element should only be shown on the index view.
     *
     * @return $this
     */
    public function onlyOnIndex()
    {
        $this->showOnIndex = true;
        $this->showOnDetail = false;
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    /**
     * Specify that the element should only be shown on the detail view.
     *
     * @return $this
     */
    public function onlyOnDetail()
    {
        $this->onlyOnDetail = true;

        // Show / hide
        $this->showOnIndex = false;
        $this->showOnDetail = true;
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    /**
     * Specify that the element should only be shown on forms.
     *
     * @return $this
     */
    public function onlyOnForms()
    {
        $this->showOnIndex = false;
        $this->showOnDetail = false;
        $this->showOnCreation = true;
        $this->showOnUpdate = true;

        return $this;
    }

    public function hide(bool $value = true)
    {
        $this->showOnIndex = ! $value;
        $this->showOnDetail = ! $value;
        $this->showOnCreation = ! $value;
        $this->showOnUpdate = ! $value;

        return $this;
    }

    /**
     * Specify that the element should be hidden from forms.
     *
     * @return $this
     */
    public function exceptOnForms()
    {
        $this->showOnIndex = true;
        $this->showOnDetail = true;
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    public function isShownOn($action, $resource = null, $request = null)
    {
        $request = $request ?: request();

        $handler = [
            'create' => fn() => $this->isShownOnCreation($request),
            'update' => fn() => $this->isShownOnUpdate($request, $resource),
            'index' => fn() => $this->isShownOnIndex($request, $resource),
            'show' => fn() => $this->isShownOnDetail($request, $resource),
        ][$action] ?? false;

        return $handler ? $handler() : false;
    }
}
