<?php

namespace Uteq\Move\Livewire;

use Livewire\Component;
use Uteq\Move\Concerns\FilesModal;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Concerns\LoadableFiles;
use Uteq\Move\Support\Livewire\Concerns\HasCrud;

class ResourceShow extends Component
{
    use HasResource;
    use HasCrud;
    use LoadableFiles;
    use FilesModal;

    public $confirmingDestroy = false;

    protected $crudBaseRoute = 'move';

    public function mount()
    {
        $this->resource = request()->route()->parameter('resource');
    }

    public function render()
    {
        return view('move::livewire.resource-show', [
            'resource' => $this->resource,
            'model' => $this->model,
            'fields' => $this->resolveFields($this->model),
        ])->layout('layouts.app', [
            'header' => $this->resource()->singularLabel() .' details',
        ]);
    }
}
