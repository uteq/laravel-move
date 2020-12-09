<?php

namespace Uteq\Move\Support\Livewire\Concerns;

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

        return $this->redirectRoute('move.index', ['resource' => $this->resource]);
    }
}
