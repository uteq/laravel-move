<?php

namespace Uteq\Move\Support\Livewire\Concerns;

use Uteq\Move\Facades\Move;

trait HasCrud
{
    public $confirmingDestroy = false;

    protected $crudActions = [
        'show' => 'show',
        'edit' => 'edit',
        'create' => 'create',
    ];

    private function modelById(int $id)
    {
        return $this->resource()->newModel()->find($id);
    }

    public function show(int $id)
    {
        $this->redirect($this->showRoute($id));
    }

    public function showRoute(int $id)
    {
        $this->crudBaseRoute ??= move()::getPrefix();

        return route($this->crudBaseRoute . '.show', [
            'resource' => $this->resource,
            'model' => $this->modelById($id),
        ]);
    }

    public function edit(int $id)
    {
        $this->redirect($this->editRoute($id));
    }

    public function editRoute(int $id)
    {
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

    public function confirmDestroy()
    {
        $this->dispatchBrowserEvent(static::class . '.confirming-destroy');

        $this->confirmingDestroy = true;
    }

    public function hideConfirmDestroy()
    {
        $this->dispatchBrowserEvent(static::class . '.hide-confirming-destroy');

        return $this->confirmingDestroy = false;
    }

    public function destroy(int $id)
    {
        $model = $this->resolveModel($id);

        $destroyer = $this->resource()->handler('delete') ?: fn ($item) => $item->delete();
        $destroyer($model);

        return $this->redirectRoute(move()::getPrefix() . '.index', ['resource' => $this->resource]);
    }
}
