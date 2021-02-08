<?php

namespace Uteq\Move\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Uteq\Move\Concerns\HasFiles;
use Uteq\Move\Concerns\HasMountActions;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Concerns\WithActionableFields;
use Uteq\Move\Concerns\WithSteps;
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
    use WithActionableFields;
    use WithSteps;

    protected static $viewType = 'edit';

    public $showModal = null;

    public $showingAddResource = [];
    public $baseRoute = 'move';
    public $showForm = false;
    public array $meta = [];

    public bool $hideStepsMenu = false;

    public $queryString = ['activeStep'];

    protected $property = 'model';
    protected $label;

    protected $listeners = [
        'showAddResource' => 'showAddResource',
        'saved' => 'handleAfterSaveActions',
        'changedActiveStep' => 'changedActiveStep',
    ];

    /**
     * @var array
     */
    public array $store;

    public function mount()
    {
        if (method_exists($this, 'setup')) {
            app()->call([$this, 'setup']);
        }

        $this->handleBeforeMount();

        $this->baseRoute = move()::getPrefix();

        $this->store = $this->fields()
            ->mapWithKeys(fn ($field) => [$field->attribute => $field->value])
            ->toArray();

        if (Move::usesTestStore()) {
            $testStore = $this->resource()->testStore() ?? [];

            // This will add the test store data to the resource form
            $this->store = collect($this->store)->map(function ($value, $field) use ($testStore) {
                if ($value !== null) {
                    return $value;
                }

                return $testStore[$field] ?? null;
            })->toArray();
        }

        $this->meta = $this->resource()->meta();

        $this->model->store = $this->store;

        if ((! $this->activeStep || ! $this->model->id) && $step = $this->steps()->first()) {
            $this->activeStep = $step->attribute;
            $this->availableSteps[] = $this->activeStep;
        }

        if ($this->model->id) {
            $this->availableSteps = $this->steps()
                ->map(fn ($step) => $step->attribute)
                ->toArray();
        }

        $this->resource()->authorizeTo('update');

        $this->handleAfterMount();

        if (method_exists($this, 'init')) {
            app()->call([$this, 'init']);
        }
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

    public function updatedStore($key, $value)
    {
        $this->model->store = $this->store;

        $this->emit(static::class . '.updatedStore', $this->store);
    }

    public function getPanelsProperty(): Collection
    {
        return $this->resource()
            ->panels($this, $this->model, isset($model->id) ? 'update' : 'create')
            ->each(fn ($panel) => $panel->id ??= Str::random(20));
    }

    public function panels()
    {
        return collect($this->panels);
    }

    public function set($attribute, $value)
    {
        $attribute = Str::before($attribute, '.');
        $attributePath = Str::after($attribute, '.');

        Arr::set($this->{$attribute}, $attributePath, $value);
    }

    public function panelAction(string $panelId, string $method, ...$args)
    {
        $this->panels()
            ->filter(fn ($panel) => get_class($panel) === decrypt($panelId))
            ->each(fn ($panel) => $panel->{$method}($this, $panel, ...$args));
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
            ? $value($this->store[$field->attribute], $this)
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
