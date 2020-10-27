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

    public function mount()
    {
        $this->baseRoute = Move::getPrefix();

        if (isset($this->model->password)) {
            collect($this->fields())
                ->each(fn ($field) => $this->model = $field->cleanModel($this->model));
        }
    }

    public function handleAfterSaveActions()
    {
        if ($this->inModal) {
            $this->emit('closeModal');

            return;
        }

        if (count($this->showingAddResource)) {
            foreach ($this->showingAddResource as $key => $value) {
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

    public function updatedModel($value, $field)
    {
        $this->resolveFields($this->model);

        $this->model->{$field} = $value;

        // FIX This is somehow needed to keep track of the model data
        //  because whenever save is hit the model seems to reset.
        //  still not to figure out why
        $this->modelData = $this->model->toArray();
    }

    public function label()
    {
        return $this->resource()->singularLabel();
    }

    public function title()
    {
        return $this->resource()->singularLabel() . ' ' . (
            $this->model ? 'bewerken' : 'aanmaken'
        );
    }

    public function render()
    {
        return view('move::livewire.resource-form', [
            'model' => $this->model,
            'fields' => $this->resolveFields($this->model),
        ])->layout(Move::layout(), [
            'header' => $this->resource()->singularLabel() .' details',
        ]);
    }

    public function rules(): array
    {
        return array_replace_recursive($this->resolveFieldRules(), (
            // TODO model never exists whenever the rules are loaded
        ($this->modelId ?? null)
            ? $this->resolveFieldUpdateRules()
            : $this->resolveFieldCreateRules()
        ));
    }
}
