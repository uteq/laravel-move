<?php

namespace Uteq\Move\Livewire;

use Livewire\Component;
use Uteq\Move\Concerns\FilesModal;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Concerns\LoadableFiles;
use Uteq\Move\Facades\Move;
use Uteq\Move\Support\Livewire\Concerns\HasCrud;

class ResourceShow extends Component
{
    use HasResource;
    use HasCrud;
    use LoadableFiles;
    use FilesModal;

    public $confirmingDestroy = null;

    protected $crudBaseRoute = 'move';

    public function mount()
    {
        $this->resource = request()->route()->parameter('resource');

        $this->resource()->authorizeTo('view');
    }

    public function render()
    {
        /** @psalm-suppress UndefinedInterfaceMethod */
        return view('move::livewire.resource-show', [
            'resource' => $this->resource,
            'model' => $this->model,
            'fields' => $this->resolveFields($this->model),
        ])->layout($this->resource()::$layout ?? Move::layout(), [
            'header' => $this->resource()->singularLabel() .' details',
        ]);
    }
}
