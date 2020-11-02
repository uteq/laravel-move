<?php

namespace Uteq\Move\Livewire;

use Livewire\WithFileUploads;
use Uteq\Move\Concerns\HasFiles;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Facades\Move;
use Uteq\Move\Support\Livewire\Concerns\HasStore;
use Uteq\Move\Support\Livewire\FormComponent;

class ResourceForm extends FormComponent
{
    use HasResource;
    use HasStore;
    use WithFileUploads;
    use HasFiles;

    public $showingAddResource = [];
    public $baseRoute = 'move';
    public $showForm = false;
    public array $modelData = [];

    protected $property = 'model';
    protected $label;

    protected $listeners = [
        'showAddResource' => 'showAddResource',
        'saved' => 'handleAfterSaveActions',
    ];

    /**
     * @var array
     */
    public array $store;

    public function mount()
    {
        $this->baseRoute = Move::getPrefix();
        $this->store = collect($this->fields())
            ->mapWithKeys(fn ($field) => [$field->attribute => $field->value])
            ->toArray();
    }

    public function handleAfterSaveActions()
    {
        if ($this->inModal) {
            $this->emit('closeModal');

            return;
        }

        if (count($this->showingAddResource)) {
            foreach (array_keys($this->showingAddResource) as $key) {
                $this->showingAddResource[$key] = false;
            }
        }
    }

    public function showAddResource($id)
    {
        $this->showForm = true;

        $this->showingAddResource[$id] = true;

        $this->emit('showingAddResource');
    }

    public function redirects(): array
    {
        return $this->resource()::$redirectEndpoints;
    }

    public function label()
    {
        return $this->resource()->singularLabel();
    }

    public function title()
    {
        return $this->resource()->singularLabel() . ' ' . (
            $this->model ? 'edit' : 'create'
        );
    }

    public function panels()
    {
        return $this->resource()->panels($this->model, isset($model->id) ? 'update' : 'create');
    }

    public function render()
    {
        /** @psalm-suppress UndefinedInterfaceMethod */
        return view('move::livewire.resource-form')->layout(Move::layout(), [
            'header' => $this->resource()->singularLabel() .' details',
        ]);
    }

    public function rules($model = null): array
    {
        return array_replace_recursive(
            $this->resolveFieldRules(),
            (
                // TODO model never exists whenever the rules are loaded
                (optional($model)->id || $this->modelId)
                    ? $this->resolveFieldUpdateRules()
                    : $this->resolveFieldCreateRules()
            )
        );
    }
}
