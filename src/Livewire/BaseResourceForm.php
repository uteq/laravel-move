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
use Uteq\Move\Concerns\WithSortable;
use Uteq\Move\Concerns\WithSteps;
use Uteq\Move\Facades\Move;
use Uteq\Move\Support\Livewire\Concerns\HasStore;
use Uteq\Move\Support\Livewire\FormComponent;

abstract class BaseResourceForm extends FormComponent
{
    use HasMountActions;
    use HasResource;
    use HasStore;
    use HasFiles;
    use WithFileUploads;
    use WithActionableFields;
    use WithSteps;
    use WithSortable;

    protected static $viewType = 'edit';

    public $name = null;
    public $showModal = null;
    public $showingAddResource = [];
    public $baseRoute = 'move';
    public $showForm = false;
    public $fields;
    public array $meta = [];
    public array $dirtyFields = [];

    public bool $hideStepsMenu = false;
    public bool $hideActions = false;

    public $queryString = ['activeStep'];

    public ?string $buttonSaveText = null;
    public ?string $buttonCancelText = null;

    protected $property = 'model';
    protected $label;

    protected $listeners = [
        'changedActiveStep' => 'changedActiveStep',
        'fields.$refresh' => 'refreshFields',
        'closeModal' => 'closeModal',
        'showAddResource' => 'showAddResource',
        'saved' => 'handleAfterSaveActions',
    ];

    /**
     * @var array
     */
    public array $store;

    public function addListener($key, $method): void
    {
        if (isset($this->listeners[$key])) {
            throw new \Exception(sprintf(
                '%s: The given listener `%s` already exists',
                __METHOD__,
                $key,
            ));
        }

        $this->listeners = array_replace([$key => $method], $this->listeners ?? []);
    }

    public function addQueryString($key): void
    {
        if (isset($this->queryString[$key])) {
            throw new \Exception(sprintf(
                '%s: The given queryString `%s` already exists',
                __METHOD__,
                $key,
            ));
        }

        array_push($this->queryString, $key);
    }

    public function refreshFields(): void
    {
        $this->model->refresh();

        $this->store = array_replace_recursive(
            $this->store,
            $this->fields()
                ->mapWithKeys(fn ($field) => [$field->attribute => $field->value])
                ->toArray()
        );
    }

    public function closeModal(): void
    {
        $this->showModal = null;
    }

    /**
     * @return void
     */
    public function handleAfterSaveActions()
    {
        if ($this->inModal) {
            $this->emit('closeModal');
            $this->emit('afterSave' . Str::slug(static::class));

            return;
        }

        if (count($this->showingAddResource)) {
            foreach (array_keys($this->showingAddResource) as $key) {
                $this->showingAddResource[$key] = false;
            }
        }

        $this->emit('afterSave' . Str::slug(static::class));
        $this->render();
    }

    public function showAddResource($id): void
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

    public function title(): string
    {
        return $this->resource()->singularLabel() . ' ' . (
            $this->model ? 'edit' : 'create'
        );
    }

    public function updatedStore($defaultKey, $defaultValue): void
    {
        $defaultKey = is_array($defaultKey)
            ? ($defaultKey[0] ?? null)
            : $defaultKey;

        $store = $this->storeAsArray();

        foreach ($this->fields() as $field) {
            $store = $field->applyAfterUpdatedStore($store, $defaultValue, $defaultKey, $this);
        }

        $this->model->store = $store;

        $this->emit(static::class . '.updatedStore', $store);

        $this->store = $store;

        $this->dirtyFields[$defaultKey] = true;
    }

    protected function storeAsArray(): array
    {
        $store = [];
        foreach ($this->store as $key => $value) {
            if (str_contains($key, '.')) {
                if (isset($this->store[Str::before($key, '.')])) {
                    continue;
                }
            }

            Arr::set($store, $key, $value);
        }

        return $store;
    }

    public function getPanelsProperty(): Collection
    {
        $this->model->store = $this->store;

        return $this->resource()
            ->panels($this, $this->model, isset($model->id) ? 'update' : 'create')
            ->each(fn ($panel) => $panel->id ??= Str::random(20));
    }

    public function panels(): Collection
    {
        return collect($this->panels);
    }

    public function set($attribute, $value): void
    {
        $attribute = Str::before($attribute, '.');
        $attributePath = Str::after($attribute, '.');

        Arr::set($this->{$attribute}, $attributePath, $value);
    }

    public function panelAction(string $panelId, string $method, ...$args): void
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
    public function updateFieldValue($field, $value): static
    {
        $this->store[$field->attribute] = is_callable($value)
            ? $value($this->store[$field->attribute], $this)
            : $value;

        return $this;
    }

    public function getCurrentAction()
    {
        return $this->model->exists()
            ? 'update'
            : 'create';
    }

    public function isCurrentActionEnabled()
    {
        return $this
            ->resource()
            ->actionEnabled($this->getCurrentAction());
    }

    public function booted()
    {
        if (! $this->isCurrentActionEnabled()) {
            if ($this->model->exists()) {
                return redirect(route(move()::getPrefix() . '.show', [
                    'resource' => $this->resource,
                    'model' => $this->model,
                ]));
            }

            return redirect(route(move()::getPrefix() . '.index', $this->resource));
        }
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
