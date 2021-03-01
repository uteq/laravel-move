<?php

namespace Uteq\Move\Concerns;

use Uteq\Move\Fields\Step;

trait WithSteps
{
    public $activeStep;
    public array $stepsData = [];
    public array $completedSteps = [];
    public array $availableSteps = [];

    public function changedActiveStep($activeStep)
    {
        if ($this->isCurrentStepDisabled($activeStep)) {
            return $this;
        }

        $this->activeStep = $activeStep;
    }

    public function setActiveStep($stepName = null)
    {
        if ($this->isCurrentStepDisabled($stepName)) {
            return $this;
        }

        $this->validateStep(null, false);

        $this->activeStep = $stepName;

        $this->emit('changedActiveStep', $this->activeStep);

        return $this;
    }

    public function isCurrentStepDisabled($stepName = null)
    {
        $step = $this->steps()->firstWhere('attribute', $stepName ?: $this->activeStep);

        return $step ? $step->disabled() : true;
    }

    public function activeStep()
    {
        return $this->activeStep;
    }

    public function step($step = null)
    {
        return $this->steps()
            ->firstWhere('attribute', $step ?: $this->activeStep);
    }

    public function steps()
    {
        return $this->panels()
            ->filter(fn ($panel) => $panel instanceof Step)
            ->reject(fn ($panel) => $panel->empty());
    }

    public function notSteps()
    {
        return $this->panels()
            ->reject(fn ($panel) => $panel instanceof Step)
            ->reject(fn ($panel) => $panel->empty());
    }

    public function validateStep($step = null, $setNext = true)
    {
        $resolvedFields = $this->resolvedStepFields($step);

        $rules = collect($resolvedFields)
            ->filter(fn ($field) => $field->isVisible($this->store, $this->model->id ? 'update' : 'create'))
            ->mapWithKeys(fn ($field) => [
                $field->attribute => $field->rules,
            ])
            ->filter()
            ->toArray();

        $data = $this->customValidate($this->mapFields($resolvedFields, $this->store), $rules);

        if (! $this->model->id) {
            $this->completedSteps[] = optional($step)->name;
        }

        $step = $this->step($step);

        if ($setNext && isset($step->next)) {
            $this->availableSteps[] = $step->next;
            $this->activeStep = $step->next;

            $this->emit('changedActiveStep', $this->activeStep);
        }
    }

    public function resolvedAndMappedStepFields($step = null, $keepPlaceholder = true)
    {
        return $this->resolvedStepFields($step, $keepPlaceholder);
    }

    public function resolvedStepFields($step = null, $keepPlaceholder = true)
    {
        $step = $this->step($step);
        $fields = $step->allFields();

        return $this->resolveFields($this->model, $keepPlaceholder, $fields);
    }

    public function allStepsAvailable()
    {
        $steps = $this->steps();

        $count = $steps->filter(fn ($step) => in_array($step->attribute, $this->availableSteps))->count();

        return $count == $steps->count();
    }
}
