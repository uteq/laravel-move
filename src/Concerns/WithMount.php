<?php

namespace Uteq\Move\Concerns;

use Uteq\Move\Facades\Move;

trait WithMount
{
    public function mount()
    {
        if (method_exists($this, 'setup')) {
            app()->call([$this, 'setup']);
        }

        $this->handleBeforeMount();

        $this->baseRoute = move()::getPrefix();

        $this->store = $this->fields()
            ->mapWithKeys(fn ($field) => [$field->attribute => $field->value])
            ->toArray();

        if (Move::usesTestStore()) {
            $testStore = $this->resource()->testStore() ?? [];

            // This will add the test store data to the resource form
            $this->store = collect($this->store)->map(function ($value, $field) use ($testStore) {
                if ($value !== null) {
                    return $value;
                }

                return $testStore[$field] ?? null;
            })->toArray();
        }

        $this->meta = $this->resource()->meta();

        $this->model->store = $this->store;

        if ((! $this->activeStep || ! $this->model->id) && $step = $this->steps()->first()) {
            $this->activeStep = $step->attribute;
            $this->availableSteps[] = $this->activeStep;
        }

        if ($this->model->id) {
            $this->availableSteps = $this->steps()
                ->map(fn ($step) => $step->attribute)
                ->toArray();
        }

        $this->model->id
            ? $this->resource()->authorizeTo('update', $this->model)
            : $this->resource()->authorizeTo('create');

        $this->handleAfterMount();

        if (method_exists($this, 'init')) {
            app()->call([$this, 'init']);
        }
    }
}
