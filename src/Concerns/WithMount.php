<?php

namespace Uteq\Move\Concerns;

use Illuminate\Support\Arr;
use Uteq\Move\Facades\Move;

trait WithMount
{
    public function mount(): void
    {
        if (method_exists($this, 'setup')) {
            /** @psalm-suppress InvalidArgument */
            app()->call([$this, 'setup']);
        }

        $this->handleBeforeMount();

        $this->mountRoutes();

        $this->mountStore();

        $this->mountSteps();

        $this->mountModel();

        $this->handleAfterMount();

        if (method_exists($this, 'init')) {
            /** @psalm-suppress InvalidArgument */
            app()->call([$this, 'init']);
        }
    }

    private function mountRoutes(): void
    {
        $this->baseRoute = move()::getPrefix();
    }

    private function mountStore(): void
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        $this->store ??= [];

        foreach ($this->fields() as $field) {
            Arr::set($this->store, $field->attribute, $field->value);
        }

        $this->mountTestStore();

        $this->meta = $this->resource()->meta();

        $undotedStore = [];
        foreach ($this->store as $key => $value) {
            Arr::set($undotedStore, $key, $value);
        }

        $this->model->store = $undotedStore;
    }

    private function mountTestStore(): void
    {
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
    }

    private function mountSteps(): void
    {
        if ((!$this->activeStep || !$this->model->id) && $step = $this->steps()->first()) {
            $this->activeStep = $step->attribute;
            $this->availableSteps[] = $this->activeStep;
        }

        if ($this->model->id) {
            $this->availableSteps = $this->steps()
                ->map(fn ($step) => $step->attribute)
                ->toArray();
        }
    }

    private function mountModel(): void
    {
        $this->model->id
            ? $this->resource()->authorizeTo('update', $this->model)
            : $this->resource()->authorizeTo('create');
    }
}
