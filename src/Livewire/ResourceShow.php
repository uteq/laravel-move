<?php

namespace Uteq\Move\Livewire;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;
use Uteq\Move\Concerns\FilesModal;
use Uteq\Move\Concerns\HasFiles;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Concerns\LoadableFiles;
use Uteq\Move\Concerns\Metable;
use Uteq\Move\Facades\Move;
use Uteq\Move\Support\Livewire\Concerns\HasCrud;

class ResourceShow extends Component
{
    use HasResource;
    use HasCrud;
    use LoadableFiles;
    use FilesModal;
    use HasFiles;
    use Metable;

    protected static $viewType = 'detail';

    public $confirmingDestroy = null;

    public $hideActions = false;
    public $hideCard = false;
    public $class = null;

    public string $action;

    public $store;

    protected $crudBaseRoute = 'move';

    public function mount($resource)
    {
        $this->resolveResourceModel();

        $this->resource()->authorizeTo('view');

        $this->action ??= $this->model->id ? 'update' : 'create';
    }

    public function panels()
    {
        return collect($this->panels);
    }

    public function getPanelsProperty(): Collection
    {
        return $this->resource()
            ->panels($this, $this->model, 'show')
            ->each(fn ($panel) => $panel->id ??= Str::random(20))
            ->each(fn ($panel) => $panel->component = 'show.panel');
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
