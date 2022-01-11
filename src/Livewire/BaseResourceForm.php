<?php

namespace Uteq\Move\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Uteq\Move\Concerns\HasFiles;
use Uteq\Move\Concerns\HasMountActions;
use Uteq\Move\Concerns\HasParent;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Concerns\WithActionableFields;
use Uteq\Move\Concerns\WithClosures;
use Uteq\Move\Concerns\WithListeners;
use Uteq\Move\Concerns\WithSteps;
use Uteq\Move\Facades\Move;
use Uteq\Move\Support\Livewire\Concerns\HasStore;
use Uteq\Move\Support\Livewire\FormComponent;

/**
 * Class BaseResourceForm
 * @package Uteq\Move\Livewire
 * @property Collection $panels
 */
abstract class BaseResourceForm extends FormComponent
{
    use HasMountActions,
        HasResource,
        HasStore,
        HasFiles,
        HasParent,
        WithFileUploads,
        WithActionableFields,
        WithSteps,
        WithClosures,
        WithListeners;

    protected static $viewType = 'edit';

    public $layout = null;
    protected $layoutVariables = [];
    public $name = null;
    public $showModal = null;
    public $showingAddResource = [];
    public $baseRoute = 'move';
    public $showForm = false;
    public ?string $action = null;
    public array $meta = [];
    public array $dirtyFields = [];

    public bool $hideStepsMenu = false;
    public bool $hideActions = false;

    public $queryString = ['activeStep'];

    public ?string $buttonSaveText = null;
    public ?string $buttonCancelText = null;

    public $closeModalClass;
    public $message;

    protected $property = 'model';
    protected $label;

    public $redirects;

    public int $version = 1;

    protected array $closures = ['redirects'];

    protected $listeners = [
        'updatedStore' => 'updatedStore',
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

    public function addQueryString($key)
    {
        if (isset($this->queryString[$key])) {
            throw new \Exception(sprintf(
                '%s: The given queryString `%s` already exists',
                __METHOD__,
                $key,
            ));
        }

        if (in_array($key, $this->queryString)) {
            return;
        }

        $this->queryString[] = $key;
    }

    public function refreshFields()
    {
        $this->model->refresh();

        $this->store = array_replace_recursive(
            $this->store,
            $this->fields()
                ->mapWithKeys(fn ($field) => [
                    $field->attribute => $field->value
                ])
                ->toArray()
        );
    }

    public function closeModal()
    {
        $closedModal = $this->showModal;

        $this->showModal = null;

        collect($this->resource()->getFields())
            ->filter(fn ($field) => method_exists($field, 'modalClosed'))
            ->each(fn ($field) => $this->action($field->store, 'modalClosed', $closedModal));
    }

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

    public function updatedStore($defaultValue, $defaultKey)
    {
        $store = $this->store;

        $this->model->store = $store;

        foreach ($this->fields() as $field) {
            $store = $field->applyAfterUpdatedStore($store, $defaultValue, $defaultKey, $this);
        }

        $this->model->store = $store;

        $this->emit(static::class . '.updatedStore', $store);

        $this->store = $store;

        $this->dirtyFields[$defaultKey] = true;
    }

    public function getPanelsProperty(): Collection
    {
        $this->model->store = $this->store;

        return $this->resource()
            ->panels($this, $this->model, isset($model->id) ? 'update' : 'create')
            ->each(fn ($panel) => $panel->id ??= Str::random(20));
    }

    public function panels()
    {
        return $this->panels;
    }

    public function panelAction(string $panelId, string $method, ...$args)
    {
        $this->panels()
            ->filter(fn ($panel) => $panel->id() === $panelId)
            ->each(fn ($panel) => $panel->{$method}($this, $panel, ...$args));
    }

    public function set($attribute, $value)
    {
        $attribute = Str::before($attribute, '.');
        $attributePath = Str::after($attribute, '.');

        Arr::set($this->{$attribute}, $attributePath, $value);
    }

    /**
     * Updates the current fields value/store. This makes
     * it easy to change the components' field
     * data from outside the component
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
        return view($this->resource()::$formView ?? 'move::livewire.resource-form', [
                'showModal' => $this->showModal,
                'isSidebarEnabled' => $this->isSidebarEnabled,
            ])
            ->layout($this->layout ?? $this->resource()::$layout ?? Move::layout(), array_replace([
                'header' => $this->resource()->singularLabel() .' details',
            ], $this->layoutVariables));
    }

    public function rules($model = null): array
    {
        return array_replace_recursive(
            $this->resolveFieldRules($model),
            (
                // TODO model never exists whenever the rules are loaded
                (optional($model)->id || $this->modelId)
                    ? $this->resolveFieldUpdateRules($this->store)
                    : $this->resolveFieldCreateRules($this->store)
            )
        );
    }

    public function customRedirects()
    {
        return $this->unserializeClosure('redirects');
    }

    public function getIsSidebarEnabledProperty()
    {
        return $this->allStepsAvailable();
    }
}
