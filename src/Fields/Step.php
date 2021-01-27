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

    public function next(string $next)
    {
        $this->next = $next;

        return $this;
    }
}
