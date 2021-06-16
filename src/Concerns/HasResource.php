<?php

namespace Uteq\Move\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Uteq\Move\Facades\Move;

/**
 * Trait HasResource
 * @package Uteq\Move\Concerns
 * @property array fields
 */
trait HasResource
{
    use HasMountActions;

    public $resource;
    public $model = null;
    public $modelId = null;

    public function initializeHasResource()
    {
        $this->initializeHasMountActions();

        $this->beforeMount(function () {
            $this->model ??= $this->resolveResourceModel();
            $this->modelId = optional($this->model)->{$this->model->getKey()};

            $this->fields = collect($this->getFieldsProperty());
        });
    }

    public function resolveResourceModel()
    {
        if ($this->model !== null) {
            return $this->model;
        }

        $resource = Move::resolveResource(request()->route()->parameter('resource') ?? null ?: $this->resource);

        if (! $resource) {
            return null;
        }

        return $resource->model();
    }

    public function mountHasResource()
    {
        if ($this->modelId) {
            $this->resource()->resource = $this->model;
        }
    }

    public function resource()
    {
        return $this->resolvedResource;
    }

    public function getResolvedResourceProperty()
    {
        return Move::resolveResource($this->resource);
    }

    public function getResourceProperty()
    {
        return $this->resource();
    }

    public function resolveModel($id)
    {
        return $this->resource()->newModel()->newQuery()->find($id);
    }

    public function resolveFields(Model $model = null, $keepPlaceholder = false, array $fields = null)
    {
        $type = ! $model ? 'create' : ($model->id ? 'update' : 'create');

        return $this->resource()->resolveFields($model, $type, $keepPlaceholder, $fields);
    }

    public function resolveAndMapFields(Model $model, array $store, array $fields = null)
    {
        $model->fill($store);

        $fields ??= $this->resolveFields($model, true);

        return $this->mapFields($fields, $store);
    }

    public function mapFields(array $resolvedFields, $store)
    {
        $store = collect($store)
            ->filter(fn ($value, $key) => ! str_contains($key, '.'))
            ->toArray();

        $fields = collect($resolvedFields)
            ->mapWithKeys(fn ($field) => [$field->attribute => Arr::get($store, $field->attribute)])
            ->toArray();

        $undotFields = [];
        foreach ($fields as $key => $value) {
            Arr::set($undotFields, $key, $value);
        }

        return $undotFields;
    }

    public function resolveFieldRules($model)
    {
        return $this->fields
            ->filter(fn ($field) => $field->isVisible($model, 'update'))
            ->flatMap(fn ($field) => $field->getRules(request()))
            ->toArray();
    }

    public function resolveFieldCreateRules($model)
    {
        return $this->fields
            ->filter(fn ($field) => $field->isVisible($model, 'create'))
            ->flatMap(fn ($field) => $field->getCreationRules(request()))
            ->toArray();
    }

    public function resolveFieldUpdateRules($model)
    {
        return $this->fields
            ->filter(fn ($field) => $field->isVisible($model, 'update'))
            ->flatMap(fn ($field) => $field->getUpdateRules(request()))
            ->toArray();
    }

    public function resolveAndMapFieldToFields($key): array
    {
        $fields = $this->fields()
            ->filter(fn ($field) => $field->attribute === $key)
            ->toArray();

        $store = Arr::dot($this->store);

        return $this->mapFields($this->resolveFields(null, null, $fields), $store);
    }

    public function getFieldRule($key): array
    {
        return collect($this->rules($this->{$this->property}))
            ->filter(fn ($rules, $field) => $field === $key)
            ->toArray();
    }

    public function handleResourceAction($type, $fields, $args = [])
    {
        $this->resource()->handleAction(
            $type,
            $this->{$this->property},
            $fields,
            'livewire',
            $args
        );

        if ($this->{$this->property}->id) {
            $this->{$this->property}->refresh();
        }

        $this->emit('saved', $this->{$this->property});
    }

    public function getFieldsProperty()
    {
        return collect(
            $this->model
                ? $this->resolveFields($this->model)
                : $this->resource()->resolveFields($this->resource()->model())
        );
    }

    public function fields()
    {
        return $this->fields;
    }

    public function filters()
    {
        return $this->resource()->filters();
    }

    public function actions()
    {
        return $this->resource()->actions();
    }

    public function query(): Builder
    {
        return $this->resource()->{'getFor' . ucfirst(static::$viewType)}($this->requestQuery())['collection'];
    }

    public function collection()
    {
        return $this->cachedCollection;
    }

    public function getCachedCollectionProperty()
    {
        return $this->query()
            ->paginate($this->filter('limit', $this->resource()->defaultPerPage()));
    }
}
