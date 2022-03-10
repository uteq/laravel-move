<?php

namespace Uteq\Move\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Uteq\Move\Facades\Move;
use Uteq\Move\Livewire\ResourceForm;

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

    protected $resourceFields = [];

    public function initializeHasResource()
    {
        $this->initializeHasMountActions();

        $this->beforeMount(function () {

            $this->model = $this->resolveResourceModel();

            $this->modelId ??= $this->model->getKey();

            $this->fields = collect($this->getFieldsProperty());

        });
    }

    public function resolveResourceModel()
    {
        if (is_string($this->model)) {
            return $this->model::find($this->modelId)
                ?: new ($this->model)();
        }

        if ($this->model !== null) {
            return $this->model;
        }

        $model = $this->doResolveResourceModel();

        if ($this->action === 'create') {
            return $model ? new ($model::class)() : null;
        }

        return $model;
    }

    protected function doResolveResourceModel()
    {
        $resource = Move::resolveResource(
            request()->route()->parameter('resource')
            ?? null
                ?: $this->resource
        );

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

        if ($this->action === 'create') {
            $this->resource()->resource = $this->model;
        }
    }

    public function resource()
    {
        $resource = $this->resolvedResource ?? $this->getResolvedResourceProperty();
        $action = isset($this->action) && $this->action ?? null;

        if ($resource->resource?->id && $action === 'create') {
            $resource->resource = new ($resource->resource::class)();
            $this->model = $resource->resource;
            $this->store = [];
        }

        return $resource;
    }

    public function getResolvedResourceProperty()
    {
        $resource = class_exists($this->resource)
            ? new $this->resource($this->model)
            : Move::resolveResource($this->resource);

        if (method_exists($this, 'resourceFields')) {
            $this->resourceFields = $this->resourceFields();
        }

        if ($this->resourceFields) {
            $resource->setFields($this->resourceFields);
        }

        if (isset($this->meta)) {
            $resource->withMeta($this->meta ?? []);
        }

        return $resource;
    }

    public function getResourceClassProperty()
    {
        return get_class($this->resource());
    }

    public function getResourceProperty()
    {
        return $this->resource();
    }

    public function resolveModel($id)
    {
        return $this->resource()->newModel()->newQuery()->find($id)
            ?: $this->collection()->firstWhere($this->resource()->model()->getKeyName(), $id);
    }

    public function resolvedFields($model = null)
    {
        $model = is_array($model) ? $this->model : $model;

        return collect($this->resolveFields($model));
    }

    public function resolveFields(Model $model = null, $keepPlaceholder = false, array $fields = null)
    {
        $type = ! $model
            ? 'create'
            : ($model->id ? 'update' : 'create');

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
        $store = move_arr_expand($store);

        $store = collect($store)
            ->filter(fn ($value, $key) => ! str_contains($key, '.'))
            ->toArray();

        $fields = collect($resolvedFields)
            ->mapWithKeys(fn ($field) => [$field->attribute => Arr::get($store, $field->attribute)])
            ->toArray();

        return move_arr_expand($fields);
    }

    public function resolveFieldRules($model)
    {
        return $this->resolvedFields($model)
            ->filter(fn ($field) => $field->isVisible($model, 'update'))
            ->flatMap(fn ($field) => $field->getRules(request()))
            ->toArray();
    }

    public function resolveFieldCreateRules($model)
    {
        return $this->resolvedFields()
            ->filter(fn ($field) => $field->isVisible($model, 'create'))
            ->flatMap(fn ($field) => $field->getCreationRules(request()))
            ->toArray();
    }

    public function resolveFieldUpdateRules($model)
    {
        return $this->resolvedFields($model)
            ->filter(fn ($field) => $field->isVisible($model, 'update'))
            ->flatMap(fn ($field) => $field->getUpdateRules(request()))
            ->toArray();
    }

    public function resolveAndMapFieldToFields($key): array
    {
        $attribute = $field->attribute ?? $field['attribute'] ?? null;

        $fields = $this->fields()
            ->filter(fn ($field) => str_starts_with($key, $attribute))
            ->toArray();

        return $this->mapFields(
            $this->resolveFields(null, null, $fields),
            $this->store
        );
    }

    public function getFieldRule($key): array
    {
        return collect($this->rules($this->{$this->property}))
            ->filter(fn ($rules, $field) => str_starts_with($key, $field))
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
            $this->action === 'create'
                ? $this->resource()->resolveFields($this->resource()->model())
                : $this->resolveFields($this->model)
//            $this->model
//                ? $this->resolveFields($this->model)
//                : $this->resource()->resolveFields($this->resource()->model())
        );
    }

    public function fields()
    {
        return collect(
            is_array($this->fields[0] ?? null)
                ? $this->getFieldsProperty()
                : $this->fields ?? []
        );
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
        if ($this->customCollection) {
            return $this->getCustomCollection();
        }

        return $this->query()
            ->paginate($this->filter('limit', $this->resource()->defaultPerPage()));
    }

    public function getCustomCollection()
    {
        $data = Arr::get(
            $this->parent()->model()->toArray(),
            $this->customCollection['key'],
            []
        );

        $model = $this->resource()->model()->setRows($data);

        return $model::query()->get();
    }
}
