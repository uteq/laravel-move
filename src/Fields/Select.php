<?php

namespace Uteq\Move\Fields;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Uteq\Move\Actions\LivewireCloseModal;
use Uteq\Move\Concerns\WithClosures;
use Uteq\Move\Concerns\WithListeners;
use Uteq\Move\Concerns\WithModal;
use Uteq\Move\Concerns\WithRedirects;
use Uteq\Move\Facades\Move;
use Uteq\Move\Resource;

class Select extends Field
{
    use WithModal;
    use WithListeners;
    use WithClosures;
    use WithRedirects;

    public string $component = 'select-field';

    protected $version = 1;

    protected $options;

    public $resourceName = null;

    public ?Closure $customIndexName = null;

    public array $settings = [];

    public bool $multiple = false;

    public $query = null;

    public $customQuery = null;

    public ?string $ajaxUrl = null;

    public bool $addResourceEnabled = false;

    public bool $shouldMapTags = true;

    public string $createResource;

    public string $createForm;

    public bool $tags = false;

    protected static $resourceCache = [];

    protected array $closures = [
        'version',
        'options',
    ];

    public function init()
    {
        $this->withMeta([
            'with_add_button' => false,
        ]);

        $this->show(fn ($field) => (string) ($this->getOptions()[$field->value] ?? null));
    }

    public function modalClosed($modal)
    {
        $this->version($this->version + 1);
    }

    public function settings(array $settings): static
    {
        $this->settings = array_replace($this->settings, $settings);

        return $this;
    }

    public function indexName(Closure $indexName): static
    {
        $this->customIndexName = $indexName;

        return $this;
    }

    public function showResourceUrl(): ?string
    {
        if (! Move::resolveResource($this->resourceName)->can('view')) {
            return null;
        }

        if (! $this->value) {
            return null;
        }

        if (! $this->clickable) {
            return null;
        }

        return route(move()::getPrefix() . '.show', [
            'resource' => $this->resourceRouteName(),
            'model' => $this->value,
        ]);
    }

    public function resourceRouteName(): string
    {
        return str_replace('.', '/', Move::getByClass($this->resourceName ?? null) ?? '');
    }

    public function resourceName(Model $model = null): ?string
    {
        $model ??= $this->resourceName
            ? $this->cachedResource($this->resourceName, $this->value, $model)
            : null;

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

    public function cachedResource($name, $value, $model): ?Model
    {
        if ($value instanceof Collection || is_array($value)) {
            return null;
        }

        if (! isset(static::$resourceCache[$name][$value]) || empty(static::$resourceCache[$name][$value])) {
            static::$resourceCache[$name] ??= [];
            static::$resourceCache[$name][$value] = $name::$model::find($value);
        }

        return static::$resourceCache[$name][$value];
    }

    public function resource($resource): static
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

    /**
     * @param (array|null|string)[]|\Closure $options
     *
     * @psalm-param Closure():mixed|\Closure(mixed):mixed|array<string, array|null|string> $options
     */
    public function options(array|Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function query($query): static
    {
        $this->query = $query;

        return $this;
    }

    protected function queryHandler($resourceName): Builder
    {
        $handler = $this->query;

        return $handler
            ? $handler($resourceName::relationQuery(), $this)
            : $resourceName::relationQuery();
    }

    public function getOptions(): array
    {
        $options = $this->options;

        $options = is_callable($options)
            ? $options($this ?? null)
            : $options;

        if (count($options ??= [])) {
            return $options;
        }

        if ($resourceName = $this->resourceName) {
            $customQuery = $this->customQuery ?? fn ($builder) => $builder;

            return $customQuery($this->queryHandler($resourceName))
                ->get()
                ->mapWithKeys(fn ($item) => [$item->getKey() => $this->resourceName($item)])
                ->toArray();
        }

        return [];
    }

    public function customQuery($customQuery): static
    {
        $this->customQuery = $customQuery;

        return $this;
    }

    public function ajax(string $url, $defaultOption = null, array $settings = []): static
    {
        is_callable($defaultOption)
            ? $this->resolveDefaultOption($defaultOption)
            : $this->options(fn () => $defaultOption);

        $minInputLength = $settings['minimumInputLength'] ?? 2;
        $noResults = $settings['noResults'] ?? 'Geen resultaten gevonden';

        $this->settings = array_replace_recursive([
            'ajax' => [
                'url' => $url,
                'dateType' => 'json',
                'delay' => 250,
            ],
            'minimumInputLength' => $minInputLength,
            'language' => [
                'inputTooShort' => <<<JS
                    function() {
                        return 'Minimaal {$minInputLength} karakters vereist';
                    }
                    JS,
                'noResults' => <<<JS
                    function() {
                        return '$noResults';
                    }
                    JS
            ],
        ], $settings);

        return $this;
    }

    public function resolveDefaultOption(callable $option): static
    {
        $this->options(function ($field) use ($option) {
            $store = $field->resource->store
                ?? $field->resource->getAttributes();

            return $option($store[$this->attribute] ?? null);
        });

        return $this;
    }

    public function multiple($multiple = true): static
    {
        $this->multiple = $multiple;

        if ($this->multiple && $this->shouldMapTags) {
            $this->mapTags();
        } else {
            unset($this->beforeStore['multiple']);
        }

        return $this;
    }

    public function shouldMapTags(bool $shouldMapTags = true): static
    {
        $this->shouldMapTags = $shouldMapTags;

        return $this;
    }

    /**
     * This makes sure that when using the 'multiple' implementation the tags will be mapped to the correct value.
     */
    protected function mapTags(): void
    {
        $this->beforeStore(function ($value, $field, $model) {
            $model = (clone $model)->refresh();

            return collect($value)
                ->map(fn ($value) => $model->{$field}[$value] ?? $value)
                ->toArray();
        }, 'multiple');
    }

    public function values($form)
    {
        $fieldStore = $this->fieldStore();

        $store = $this->store();

        if (! $store && ! Arr::get($form->store, $this->attribute)) {
            $value = $this->valueCallback ? ($this->valueCallback)(null, $this->resource, $this->attribute) : null;
            $values = $value ? [$value] : null;

            $form->store[$this->attribute] = $value;
        } else {
            $values = $store ?: Arr::get($form->store, $this->attribute);
        }

        $values = $store
            ? ($this->multiple ? $fieldStore : array_keys($fieldStore))
            : $values;

        return is_string($values) ? [$values] : $values;
    }

    public function fieldStore()
    {
        return is_array($this->store()) ? $this->store() : [$this->store() => true];
    }

    public function version($version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion()
    {
        $version = $this->version;

        return is_callable($version)
            ? $version($this)
            : $version;
    }

    public function createResource(string $resource, string $form): static
    {
        $this->createResource = $resource;
        $this->createForm = $form;
        $this->addResourceEnabled = true;

        return $this;
    }

    public function withAddButton($withAddButton = true, $redirectsCloseModal = true): static
    {
        $this->meta['with_add_button'] = $withAddButton;

        $this->version($this->resourceName::$model::count());

        if ($redirectsCloseModal) {
            $this->redirects(is_array($redirectsCloseModal) ? $redirectsCloseModal : [
                'create' => LivewireCloseModal::make(),
                'update' => LivewireCloseModal::make(),
                'delete' => LivewireCloseModal::make(),
            ]);
        }

        return $this;
    }

    public function tags($tags = true): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function optionsBy(string $key, string $value, string $resource = null)
    {
        $resource ??= $this->resource::class;

        return $this
            ->shouldMapTags(false)
            ->index(fn ($field) => implode(
                ', ',
                $resource::query()
                ->whereIn($key, $field->value)
                ->pluck($value)
                ->toArray()
            ))
            ->options(
                $resource::all()
                    ->mapWithKeys(fn ($item) => [$item->{$key} => $item->{$value}])
                    ->toArray()
            );
    }
}
