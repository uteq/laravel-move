<?php

namespace Uteq\Move\Support\Livewire\Concerns;

use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uteq\Move\Resource;

trait HasCrud
{
    public $confirmingDestroy = null;

    protected $crudActions = [
        'show' => 'show',
        'edit' => 'edit',
        'create' => 'create',
    ];

    protected array $hasCrudModelByIdCache = [];

    protected static $crudUsesSoftDelete = null;

    public function initializeHasCrud(): void
    {
        /** @psalm-suppress TypeDoesNotContainType */
        if (! property_exists($this, 'crudBaseRoute')) {
            throw new Exception('Class: '. static::class .' is missing property crudBaseRoute.');
        }

        /** @psalm-suppress UndefinedThisPropertyAssignment */
        $this->crudBaseRoute = $this->crudBaseRoute
            ?: move()::getPrefix();
    }

    private function modelById($id)
    {
        $resourceModel = $this->resource()->model();

        if ($this->crudUsesSoftDelete($resourceModel)) {
            $resourceModel = $resourceModel->withTrashed();
        }

        if ($this->rows ?? null) {
            return collect($this->rows)
                ->filter(fn ($row) => (int)  $row['model']['id'] === (int) $id)
                ->filter(fn ($row) => $row['model']::class === $resourceModel::class)
                ->map(fn ($row) => $row['model'])
                ->first();
        }

        return $this->hasCrudModelByIdCache[$resourceModel::class][$id] ??= $resourceModel->find($id);
    }

    private function crudUsesSoftDelete($resourceModel)
    {
        if (null !== static::$crudUsesSoftDelete) {
            return static::$crudUsesSoftDelete;
        }

        return static::$crudUsesSoftDelete = in_array(
            SoftDeletes::class,
            class_uses_recursive($resourceModel)
        );
    }

    public function show($id): void
    {
        $this->redirect($this->showRoute($id));
    }

    public function showRoute($id): string|null
    {
        /** @psalm-suppress TypeDoesNotContainType */
        if (! property_exists($this, 'crudBaseRoute')) {
            throw new Exception('Class: '. static::class .' is missing property crudBaseRoute.');
        }

        $model = $this->modelById($id);

        if (! $model) {
            return null;
        }

        return route($this->crudBaseRoute . '.show', [
            'resource' => str_replace('.', '/', $this->resource),
            'model' => $model,
        ]);
    }

    public function edit($id): void
    {
        $this->redirect($this->editRoute($id));
    }

    public function editRoute($id, $resourceRoute = null): string|null
    {
        /** @psalm-suppress TypeDoesNotContainType */
        if (! property_exists($this, 'crudBaseRoute')) {
            throw new Exception('Class: '. static::class .' is missing property crudBaseRoute.');
        }

        $model = $this->modelById($id);

        if (! $model) {
            return null;
        }

        return route($this->crudBaseRoute . '.edit', [
            'resource' => str_replace('.', '/', $resourceRoute ?: $this->resource),
            'model' => $this->modelById($id),
        ]);
    }

    public function add(): void
    {
        $this->redirect($this->addRoute());
    }

    public function addRoute(): string
    {
        /** @psalm-suppress TypeDoesNotContainType */
        if (! property_exists($this, 'crudBaseRoute')) {
            throw new Exception('Class: '. static::class .' is missing property crudBaseRoute.');
        }

        /** @psalm-suppress UndefinedThisPropertyAssignment */
        $this->crudBaseRoute ??= move()::getPrefix();

        /** @var Resource $parent */
        $parent = $this->parent();
        $attributes = [];
        if ($parent && $parent instanceof Resource) {
            /** @psalm-suppress UndefinedPropertyFetch */
            $attributes['parent_model'] = $parent::$model;

            /**
             * @psalm-suppress RedundantCondition
             * @psalm-suppress TypeDoesNotContainType
             */
            $attributes['parent_id'] = isset($parent->resource) ? $parent->resource->id : null;
        }

        return route($this->crudBaseRoute . '.create', array_replace_recursive([
            'resource' => str_replace('.', '/', $this->resource),
        ], $attributes));
    }

    public function confirmDestroy($id): void
    {
        $this->dispatchBrowserEvent(static::class . '.confirming-destroy');

        $this->confirmingDestroy = [$id];
    }

    /**
     * @return false
     */
    public function hideConfirmDestroy(): bool
    {
        $this->dispatchBrowserEvent(static::class . '.hide-confirming-destroy');

        return $this->confirmingDestroy = false;
    }

    public function destroy($id)
    {
        /** @psalm-suppress TypeDoesNotContainType */
        if (! property_exists($this, 'crudBaseRoute')) {
            throw new Exception('Class: '. static::class .' is missing property crudBaseRoute.');
        }

        $model = $this->resolveModel($id);

        $destroyer = $this->resource()->handler('delete') ?: fn ($item) => $item->delete();
        $destroyer($model);

        if (method_exists($this, 'parent')) {
            if ($this->parent() && isset($this->parent()->resource->id)) {
                return $this->redirectTo = $this->editRoute(
                    $this->parent()->resource->id,
                    $this->parentRoute($this->crudBaseRoute)
                );
            }
        }

        $this->emit('move::table:updated');

        return $this->redirectRoute(move()::getPrefix() . '.index', ['resource' => str_replace('.', '/', $this->resource)]);
    }
}
