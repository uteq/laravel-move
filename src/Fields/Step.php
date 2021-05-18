<?php

namespace Uteq\Move\Fields;

class Step extends Panel
{
    public string $component = 'form.step';

    public string $next;

    public string $attribute;

    public bool $hideTitle = true;

    public bool $isActive = false;

    public bool $disabled = true;

    public bool $showNextOnEdit = false;
    public ?string $nextText = null;

    public bool $hideCancel = false;

    public ?string $cancelRoute = null;

    public ?string $doneRoute = null;
    public ?string $doneText = null;
    public ?\Closure $doneAction = null;

    public function __construct(string $name, string $attribute, array $fields)
    {
        $this->attribute = $attribute;

        parent::__construct($name, $fields);
    }

    public function disabled()
    {
        return ! $this->active()
            && ! $this->isComplete()
            && ! $this->isAvailable();
    }

    public function active()
    {
        if (! isset($this->resourceForm)) {
            return false;
        }

        return $this->attribute === $this->resourceForm->activeStep();
    }

    public function isComplete()
    {
        return in_array($this->attribute, $this->resourceForm->completedSteps);
    }

    public function isAvailable()
    {
        return in_array($this->attribute, $this->resourceForm->availableSteps);
    }

    public function showTitle($show = true)
    {
        $this->hideTitle = ! $show;

        return $this;
    }

    public function next(string $next, ?string $text = null, $showOnEdit = false)
    {
        $this->next = $next;
        $this->showNextOnEdit = $showOnEdit;
        $this->nextText = $text;

        return $this;
    }

    public function cancelRoute($cancelRoute)
    {
        $this->cancelRoute = $cancelRoute;

        return $this;
    }

    public function hideCancel($hideCancel = true)
    {
        $this->hideCancel = $hideCancel;

        return $this;
    }

    public function done($route, $text, \Closure $action = null)
    {
        $this->doneRoute = $route;
        $this->doneText = $text;
        $this->doneAction = $action;

        return $this;
    }

    public function handleDone($component)
    {
        if ($this->doneAction) {
            return app()->call([$this->doneAction, '__invoke'], [
              'field' => $this,
              'component' => $component,
            ]);
        }

        return redirect($this->doneRoute);
    }
}
