<?php

namespace Uteq\Move\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Uteq\Move\Facades\Move;
use Uteq\Move\Resource;

trait HasResource
{
    public $resource;
    public $model = null;
    public ?int $modelId = null;

    public function initializeHasResource()
    {
        $this->model ??= (Move::resolveResource(request()->route()->parameter('resource'))->model());
        $this->modelId = optional($this->model)->id;
    }

    public function mountHasResource()
    {
        if ($this->modelId) {
            $this->resource()->resource = $this->model;
        }
    }

    public function resource()
    {
        return Move::resolveResource($this->resource);
    }

    public function getResourceProperty()
    {
        return $this->resource();
    }

    public function resolveModel(int $id)
    {
        return $this->resource()->newModel()->newQuery()->find($id);
    }

    public function resolveFields(Model $model = null)
    {
        $type = ! $model ? 'create' : ($model->id ? 'update' : 'create');

        return $this->resource()->resolveFields($model, $type);
    }

    public function resolveAndMapFields(Model $model)
    {
        return collect($this->resolveFields($this->model))
            ->mapWithKeys(fn ($field) => [$field->attribute => $this->store[$field->attribute]])
            ->toArray();
    }

    public function resolveFieldRules()
    {
        return collect($this->fields())
            ->flatMap(fn ($field) => $field->getRules(request()))
            ->toArray();
    }

    public function resolveFieldCreateRules()
    {
        return collect($this->fields())
            ->flatMap(fn ($field) => $field->getCreationRules(request()))
            ->toArray();
    }

    public function resolveFieldUpdateRules()
    {
        return collect($this->fields())
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

    public function fields()
    {
        return $this->model
            ? $this->resolveFields($this->model)
            : $this->resource()->resolveFields($this->resource()->model());
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
        return $this->resource()->getForIndex($this->requestQuery)['collection'];
    }

    public function collection()
    {
        return $this->query()
            ->paginate($this->filter('limit', $this->resource()->defaultPerPage()));
    }
}
