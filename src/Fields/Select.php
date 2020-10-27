<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Facades\Move;
use Uteq\Move\Resource;

class Select extends Field
{
    public string $component = 'select-field';

    public array $options = [];

    public ?string $resourceName = null;

    public string $placeholder;

    public \Closure $customIndexName;

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

    public function resourceName()
    {
        if (! $this->resourceName) {
            return null;
        }

        if ($this->customIndexName ?? false) {
            $callback = $this->customIndexName;

            return $callback($this);
        }

        $key = $this->resourceName::$title;

        return optional($this->resourceName::$model::find($this->value))->$key;
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

    public function getOptions(): array
    {
        if (count($this->options)) {
            return $this->options;
        }

        if ($this->resourceName) {
            return $this->resourceName::$model::all()
                ->mapWithKeys(function ($item) {
                    $key = $this->resourceName::$title;

                    return [$item->getKey() => $item->$key];
                })
                ->toArray();
        }

        return [];
    }
}
