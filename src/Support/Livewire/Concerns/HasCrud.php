<?php

namespace Uteq\Move\Support\Livewire\Concerns;

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

    protected static $crudUsesSoftDelete = null;

    public function initializeHasCrud()
    {
        $this->crudBaseRoute ??= move()::getPrefix();
    }

    private function modelById($id)
    {
        $resourceModel = $this->resource()->model();
        if ($this->crudUsesSoftDelete($resourceModel)) {
            $resourceModel = $resourceModel->withTrashed();
        }

        return $resourceModel->find($id);
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

    public function show($id)
    {
        $this->redirect($this->showRoute($id));
    }

    public function showRoute($id)
    {
        $model = $this->modelById($id);

        if (! $model) {
            return null;
        }

        return route($this->crudBaseRoute . '.show', [
            'resource' => str_replace('.', '/', $this->resource),
            'model' => $model,
        ]);
    }

    public function edit($id)
    {
        $this->redirect($this->editRoute($id));
    }

    public function editRoute($id, $resourceRoute = null)
    {
        $model = $this->modelById($id);

        if (! $model) {
            return null;
        }

        return route($this->crudBaseRoute . '.edit', [
            'resource' => str_replace('.', '/', $resourceRoute ?: $this->resource),
            'model' => $this->modelById($id),
        ]);
    }

    public function add()
    {
        $this->redirect($this->addRoute());
    }

    public function addRoute()
    {
        $this->crudBaseRoute ??= move()::getPrefix();

        $parent = $this->parent();
        $attributes = [];
        if ($parent && $parent instanceof Resource) {
            $attributes['parent_model'] = $parent::$model;
            $attributes['parent_id'] = isset($parent->resource) ? $parent->resource->id : null;
        }

        return route($this->crudBaseRoute . '.create', array_replace_recursive([
            'resource' => str_replace('.', '/', $this->resource),
        ], $attributes));
    }

    public function confirmDestroy($id)
    {
        $this->dispatchBrowserEvent(static::class . '.confirming-destroy');

        $this->confirmingDestroy = [$id];
    }

    public function hideConfirmDestroy()
    {
        $this->dispatchBrowserEvent(static::class . '.hide-confirming-destroy');

        return $this->confirmingDestroy = false;
    }

    public function destroy($id)
    {
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

        return $this->redirectRoute(move()::getPrefix() . '.index', ['resource' => str_replace('.', '/', $this->resource)]);
    }
}
