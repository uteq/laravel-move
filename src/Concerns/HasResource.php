<?php

namespace Uteq\Move\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Uteq\Move\Facades\Move;

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
            $this->fields = collect($this->getFieldsProperty());
        });

        $this->model ??= $this->resolveResourceModel();

        if (! $this->model) {
            dd(static::class);
        }

        $this->modelId = optional($this->model)->id;
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

    public function resolveFields(Model $model = null, $keepPlaceholder = false)
    {
        $type = ! $model ? 'create' : ($model->id ? 'update' : 'create');

        return $this->resource()->resolveFields($model, $type, $keepPlaceholder);
    }

    public function resolveAndMapFields(Model $model, array $store, array $fields = null)
    {
        $model->fill($store);

        $fields ??= $this->resolveFields($model, true);

        return collect($fields)
            ->filter(fn ($field) => isset($store[$field->attribute]))
            ->mapWithKeys(fn ($field) => [$field->attribute => $store[$field->attribute]])
            ->toArray();
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

    public function handleResourceAction($type, $fields)
    {
        $this->resource()->handleAction(
            $type,
            $this->{$this->property},
            $fields,
            'livewire',
        );

        $this->{$this->property}->refresh();

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
        return $this->resource()->{'getFor' . ucfirst(static::$viewType)}($this->requestQuery)['collection'];
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
