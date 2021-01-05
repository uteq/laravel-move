<?php

namespace Uteq\Move\Livewire;

use Illuminate\Support\Collection;
use Livewire\WithFileUploads;
use Uteq\Move\Concerns\HasFiles;
use Uteq\Move\Concerns\HasMountActions;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Facades\Move;
use Uteq\Move\Support\Livewire\Concerns\HasStore;
use Uteq\Move\Support\Livewire\FormComponent;

/**
 * Class ResourceForm
 * @package Uteq\Move\Livewire
 */
class ResourceForm extends FormComponent
{
    use HasMountActions;
    use HasResource;
    use HasStore;
    use HasFiles;
    use WithFileUploads;

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
        $this->handleBeforeMount();

        $this->baseRoute = move()::getPrefix();

        $testStore = $this->resource()->testStore() ?? [];

        $this->store = $this->fields()
            ->mapWithKeys(fn ($field) => [$field->attribute => $field->value])

            // This will add the test store data to the resource form
            ->map(function ($value, $field) use ($testStore) {

                if (config('app.debug') !== true) return $value;

                if ($value !== null) return $value;

                return $testStore[$field] ?? null;
            })
            ->toArray();

        $this->model->store = $this->store;

        $this->resource()->authorizeTo('update');

        $this->handleAfterMount();
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

    public function updatedStore()
    {
        $this->model->store = $this->store;
    }

    public function panels()
    {
        return $this->resource()->panels($this->model, isset($model->id) ? 'update' : 'create');
    }

    public function action($store, $method, ...$args)
    {
        $matchingFields = $this->fields
            ->filter(fn ($field) => $field->store === $store);

        foreach ($matchingFields as $key => $field) {
            $field->{$method}($this, $field, $args);
        }

        return $this;
    }

    /**
     * Updates the current fields value/store. This makes
     * it easy to change the components field
     * data from outside the component
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function updateFieldValue($field, $value): self
    {
        $this->store[$field->attribute] = is_callable($value)
            ? $value($this->store[$field->attribute])
            : $value;

        return $this;
    }

    public function render()
    {
        /** @psalm-suppress UndefinedInterfaceMethod */
        return view('move::livewire.resource-form')
            ->layout($this->resource()::$layout ?? Move::layout(), [
                'header' => $this->resource()->singularLabel() .' details',
            ]);
    }

    public function rules($model = null): array
    {
        return array_replace_recursive(
            $this->resolveFieldRules($this->store),
            (
                // TODO model never exists whenever the rules are loaded
                (optional($model)->id || $this->modelId)
                    ? $this->resolveFieldUpdateRules($this->store)
                    : $this->resolveFieldCreateRules($this->store)
            )
        );
    }
}
