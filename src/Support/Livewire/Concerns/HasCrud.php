<?php

namespace Uteq\Move\Support\Livewire\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;

trait HasCrud
{
    public $confirmingDestroy = null;

    protected $crudActions = [
        'show' => 'show',
        'edit' => 'edit',
        'create' => 'create',
    ];

    protected static $crudUsesSoftDelete = null;

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

        $this->crudBaseRoute ??= move()::getPrefix();

        return route($this->crudBaseRoute . '.show', [
            'resource' => $this->resource,
            'model' => $model,
        ]);
    }

    public function edit($id)
    {
        $this->redirect($this->editRoute($id));
    }

    public function editRoute($id)
    {
        $model = $this->modelById($id);

        if (! $model) {
            return null;
        }

        $this->crudBaseRoute ??= move()::getPrefix();

        return route($this->crudBaseRoute . '.edit', [
            'resource' => $this->resource,
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

        return route($this->crudBaseRoute . '.create', [
            'resource' => $this->resource,
        ]);
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

        return $this->redirectRoute(move()::getPrefix() . '.index', ['resource' => $this->resource]);
    }
}
