<?php

namespace Uteq\Move\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Uteq\Move\Facades\Move;
use Uteq\Move\Resource;

class Select extends Field
{
    public string $component = 'select-field';

    public array $options = [];

    public $resourceName = null;

    public string $placeholder;

    public ?\Closure $customIndexName = null;

    public array $settings = [];

    public $query = null;

    public $customQuery = null;

    public function settings(array $settings)
    {
        $this->settings = $settings;

        return $this;
    }

    public function indexName(\Closure $indexName)
    {
        $this->customIndexName = $indexName;

        return $this;
    }

    public function placeholder(string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function showResourceUrl()
    {
        if (! Move::resolveResource($this->resourceName)->can('view')) {
            return null;
        }

        if (! $this->value) {
            return null;
        }

        return route('move.show', [
            'resource' => $this->resourceRouteName(),
            'model' => $this->value,
        ]);
    }

    public function resourceRouteName()
    {
        return str_replace('.', '/', Move::getByClass($this->resourceName ?? null) ?? '');
    }

    public function resourceName(Model $model = null)
    {
        $model ??= $this->resourceName ? $this->resourceName::$model::find($this->value) : null;

        if (! $model) {
            return null;
        }

        if (! $this->resourceName) {
            return null;
        }

        if ($this->customIndexName) {
            $callback = $this->customIndexName;

            return $callback($model, $this);
        }

        $resourceName = $this->resourceName;

        return $resourceName::title($model);
    }

    public function resource($resource)
    {
        if (! class_exists($resource)) {
            throw new \Exception(sprintf(
                '%s: the given resource %s does not exist',
                __METHOD__,
                $resource
            ));
        }

        if (! is_subclass_of($resource, Resource::class)) {
            throw new \Exception(sprintf(
                '%s: the given resource %s is not a subclass of %s',
                __METHOD__,
                $resource,
                Resource::class
            ));
        }

        $this->resourceName = $resource;

        return $this;
    }

    public function options($options): self
    {
        $this->options = is_callable($options)
            ? $options($this->resourceName ?? null)
            : $options;

        return $this;
    }

    public function query($query)
    {
        $this->query = $query;

        return $this;
    }

    protected function queryHandler($resourceName)
    {
        $handler = $this->query;

        return $handler
            ? $handler($resourceName::relationQuery(), $this)
            : $resourceName::relationQuery();
    }

    public function getOptions(): array
    {
        if (count($this->options)) {
            return $this->options;
        }

        if ($resourceName = $this->resourceName) {

            $customQuery = $this->customQuery ?? fn($builder) => $builder;

            return $customQuery($this->queryHandler($resourceName))
                ->get()
                ->mapWithKeys(fn ($item) => [$item->getKey() => $this->resourceName($item)])
                ->toArray();
        }

        return [];
    }

    public function customQuery($customQuery)
    {
        $this->customQuery = $customQuery;

        return $this;
    }
}
