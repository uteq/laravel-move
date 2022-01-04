<?php

namespace Uteq\Move\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\View;
use Uteq\Move\Actions\UnsetField;
use Uteq\Move\Concerns\HasDependencies;
use Uteq\Move\Concerns\HasHelpText;
use Uteq\Move\Concerns\HasRequired;
use Uteq\Move\Concerns\HasRules;
use Uteq\Move\Concerns\IsStacked;
use Uteq\Move\Concerns\Sortable;
use Uteq\Move\Facades\Move;

abstract class Field extends FieldElement
{
    use Macroable;
    use HasHelpText;
    use HasRules;
    use Sortable;
    use IsStacked;
    use HasDependencies;
    use HasRequired;

    public string $name;
    public ?string $attribute;
    public ?string $type;
    public ?string $placeholder = null;
    public bool $clickable = false;
    public ?bool $wrapContent = null;
    public ?string $storePrefix = null;
    public ?string $defaultStorePrefix = null;

    public mixed $value;

    protected ?Closure $valueCallback;

    protected array $listeners = [];

    /**
     * The callback to be used to resolve the field's display value.
     */
    protected ?Closure $resourceDataCallback = null;

    /**
     * Indicates if the field is nullable.
     */
    public bool $nullable = false;

    /**
     * Indicates if the fields display is flexable
     */
    public bool $flex = false;

    /**
     * Values which will be replaced to null.
     */
    public array $nullValues = [''];

    /**
     * The model associated with the field.
     */
    public Model $resource;

    /**
     * The attribute used to keep the data in to
     * submit with the form.
     */
    public string $formAttribute = 'model';

    /**
     * Define your own field filler here
     */
    protected ?Closure $fillCallback = null;

    /**
     * @var Closure[]
     */
    protected array $beforeStore = [];

    /**
     * @var Closure[]
     */
    protected array $afterStore = [];

    public string $store;

    protected ?Closure $before = null;

    public bool $isPlaceholder = false;

    public array $displayTypes = [
        'edit' => 'form',
        'update' => 'form',
        'create' => 'form',
        'form' => 'form',
        'index' => 'index',
        'show' => 'show',
    ];

    public string $folder = 'move::';

    public string $unique;

    protected $index = null;
    protected $show = null;
    protected $form = null;

    public bool $disabled = false;

    protected ?Closure $afterUpdatedStore = null;

    public bool $dirty = false;

    protected array $sortableCallbacks = [];

    /**
     * Field constructor.
     */
    public function __construct(string $name, string $attribute = null, Closure $valueCallback = null)
    {
        $this->name = $name;
        $this->attribute = $attribute ?? Str::snake(Str::singular($name));
        $this->valueCallback($valueCallback);
        $this->store = $this->storePrefix() . '.' . $attribute;
        $this->unique = Str::random(20);

        if (method_exists($this, 'init')) {
            /** @psalm-suppress InvalidArgument */
            app()->call([$this, 'init']);
        }
    }

    public function valueCallback(Closure $valueCallback = null): static
    {
        $this->valueCallback = $valueCallback;

        return $this;
    }

    public function isPlaceholder(bool $value = true): static
    {
        $this->isPlaceholder = $value;

        return $this;
    }

    public function storePrefix(): string
    {
        return 'store';
    }

    public function formAttribute($formAttribute): static
    {
        $this->formAttribute = $formAttribute;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function attribute(): string|null
    {
        return $this->attribute;
    }

    public function type($type): static
    {
        $this->type = $type;

        return $this;
    }

    public function model(): string
    {
        return $this->formAttribute . '.' . $this->attribute;
    }

    /** Applies the resource data to the current field */
    public function applyResourceData(
        $model,
        $resourceForm = null,
        $resource = null,
    ): static {
        $this->resource = $model;

        $this->resourceDataCallback
            ? tap(
                $this->value ?? $this->getResourceAttributeValue($model, $this->attribute),
                fn ($value) => $this->value = call_user_func(
                    $this->resourceDataCallback,
                    $value,
                    $model,
                    $this->attribute,
                )
            )
            : $this->fillFromResource($model, $this->attribute);

        if (! $this->value && $this->valueCallback) {
            $this->value = ($this->valueCallback)($this->value, $model, $this->attribute);
        }

        return $this;
    }

    /**
     * Fills the field values from resource
     *
     * @param  mixed $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function fillFromResource($resource, $attribute = null): void
    {
        $this->resource = $resource;

        $value = $this->getResourceAttributeValue($resource, $this->attribute);

        $this->value = $this->valueCallback
            ? tap($value, fn ($value) => call_user_func(
                $this->valueCallback,
                $value,
                $resource,
                $this->attribute
            ))
            : $value;
    }

    /**
     * @param $resource
     * @param null|string $attribute
     *
     * @return array|mixed
     */
    protected function getResourceAttributeValue($resource, string|null $attribute)
    {
        return Arr::get($resource, str_replace('->', '.', $attribute));
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     */
    public function fillInto(
        Request $request,
        object $model,
        string $attribute,
        ?string $requestAttribute = null
    ): void {
        $this->fillAttribute($request, $requestAttribute ?? $this->attribute, $model, $attribute);
    }

    protected function fillAttribute(
        Request $request,
        string|null $requestAttribute,
        object $model,
        string $attribute
    ): void {
        $filler = $this->fillCallback;

        if (is_callable($filler)) {
            $filler($request, $model, $attribute, $requestAttribute);

            return;
        }

        $this->fillAttributeFromRequest($request, $requestAttribute, $model, $attribute);
    }

    /**
     * @param Request $request
     * @param $requestAttribute
     * @param $model
     * @param $attribute
     */
    protected function fillAttributeFromRequest(
        Request $request,
        $requestAttribute,
        $model,
        $attribute
    ): void {
        if (! $request->exists($requestAttribute)) {
            return;
        }

        $value = $request[$requestAttribute];

        $model->{$attribute} = $this->isNullValue($value)
            ? null
            : $value;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isNullValue($value): bool
    {
        $nullValues = $this->nullValues;

        return $this->nullable
            ? (is_callable($nullValues) ? $nullValues($value) : in_array($value, $nullValues))
            : false;
    }

    /**
     * @param Closure $fillCallback
     * @return $this
     */
    public function fillUsing(Closure $fillCallback): static
    {
        $this->fillCallback = $fillCallback;

        return $this;
    }

    public function resourceUrl($resource): string
    {
        $resource = Move::getByClass(is_string($resource) ? $resource : get_class($resource));

        $resource = str_replace('.', '/', $resource);

        // Fixes a possible double prefix
        $resource = Str::startsWith($resource, move()::getPrefix() . '/')
            ? str_replace(move()::getPrefix() . '/', '', $resource)
            : $resource;

        return route(move()::getPrefix() . '.edit', [
            'resource' => $resource,
            'model' => $this->resource,
        ]);
    }

    public function clickable($clickable = true): static
    {
        $this->clickable = is_callable($clickable) ? $clickable($this) : $clickable;

        return $this;
    }

    public function cleanModel(Model $model): Model
    {
        return $model;
    }

    /**
     * Return the validation key for the field.
     */
    public function validationKey(): ?string
    {
        return $this->attribute;
    }

    public function key(): string
    {
        return strtolower(Str::afterLast(static::class, '\\'));
    }

    public function view(string $displayTypeKey, array $data = [])
    {
        $this->type = $displayTypeKey;

        $displayType = $this->displayTypes[$displayTypeKey] ?? 'index';

        $data = array_replace_recursive([
            'field' => $this,
        ], $data);

        if (isset($this->{$displayType}) && null !== $this->{$displayType}) {
            $handler = $this->{$displayType};

            if ($handler instanceof View) {
                return $handler->with($data);
            }

            return is_callable($handler) ? $handler($this, $data) : $handler;
        }

        if (! $this->isVisible($this->resourceStore(), $this->type)) {
            return null;
        }

        return view($this->folder . $displayType .'.' . $this->component, array_replace_recursive([
            'field' => $this,
        ], $data));
    }

    public function resourceStore(): array
    {
        return array_replace($this->resource->toArray(), $this->resource->store ?? []);
    }

    public function isVisible(array $resource, ?string $displayType = null): bool
    {
        if (! $this->areDependenciesSatisfied($resource)) {
            return false;
        }

        $type = [
            'create' => 'create',
            'edit' => 'update',
            'index' => 'index',
            'show' => 'show',
        ][$displayType] ?? $displayType;

        return $this->isShownOn($type, $resource, request());
    }

    public function defaultDisplayType(): string
    {
        return $this->type ?? Str::afterLast(request()->route()->getName(), '.');
    }

    public function render()
    {
        return $this->view(...func_get_args());
    }

    /**
     * @param null|string $key
     *
     * @psalm-param 'multiple'|null $key
     */
    public function beforeStore(Closure $beforeStore, string|null $key = null): static
    {
        $key
            ? $this->beforeStore[$key] = $beforeStore
            : $this->beforeStore[] = $beforeStore;

        return $this;
    }

    public function hasBeforeStore(): bool
    {
        return isset($this->beforeStore);
    }

    public function afterStore(Closure $afterStore, $key = null): static
    {
        $key ? $this->afterStore[$key] = $afterStore
             : $this->afterStore[] = $afterStore;

        return $this;
    }

    public function hasAfterStore(): bool
    {
        return isset($this->afterStore);
    }

    public function handleBeforeStore($value, $field, $model, $data): array
    {
        $handlers = $this->beforeStore;

        if (method_exists($this, 'initBeforeStore')) {
            $handlers[] = $this->initBeforeStore($value, $field, $model, $data);
        }

        if (! count($handlers)) {
            return $data;
        }

        foreach ($handlers as $handler) {
            $data[$field] = $handler($value, $field, $model, $data);
        }

        return collect($data)
            ->filter(fn ($value) => $value !== UnsetField::class)
            ->toArray();
    }

    public function handleAfterStore($value, $field, $model, $data): array
    {
        $handlers = $this->afterStore;

        if (method_exists($this, 'initAfterStore')) {
            $handlers[] = $this->initAfterStore($value, $field, $model, $data);
        }

        if (! count($handlers)) {
            return $data;
        }

        foreach ($handlers as $handler) {
            $data[$field] = $handler($value, $field, $model, $data);
        }

        return collect($data)
            ->filter(fn ($_field, $value) => $value !== UnsetField::class)
            ->toArray();
    }

    public function removeFromModel(\Closure $conditions = null): static
    {
        $this->beforeStore[] = function ($value, $field, $model, $data) use ($conditions) {
            if ($conditions
                && ! ($conditions($value, $field, $model, $data))
            ) {
                return $value;
            }

            unset($model->{$field});

            return UnsetField::class;
        };

        return $this;
    }

    public function onlyForValidation(\Closure $conditions = null): static
    {
        $this->removeFromModel($conditions);

        return $this;
    }

    public function index(string|Closure|View $index): static
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @psalm-param Closure(mixed):string $show
     */
    public function show(string|Closure|View $show): static
    {
        $this->show = $show;

        return $this;
    }

    public function form(string|Closure|View $form): static
    {
        $this->form = $form;

        return $this;
    }

    public function store($key = null, $default = null)
    {
        $store = move_arr_expand($this->resource->store ?: $this->resource->getAttributes() ?? []);

        if (empty($store)) {
            return $default;
        }

        $attribute = $this->storePrefix
            ? Str::after($this->storePrefix . '.' . $this->attribute, 'store.')
            : $this->attribute;

        return $key
            ? Arr::get($store[$attribute] ?? $default, $key, $default)
            : Arr::get($store, $attribute, $default);
    }

    public function before(Closure $before): static
    {
        $this->before = $before;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder($resource): ?string
    {
        return $this->placeholder
            ?? (string) __('Add a :label', [
                'label' => lcfirst($resource->singularLabel()) . ' ' . lcfirst($this->name),
            ]);
    }

    public function forgetComponentMeta($component, $key): void
    {
        Arr::forget($component->meta, static::class . '.' . $key);
    }

    public function hasComponentMeta($component, $key): bool
    {
        return Arr::has($component->meta, static::class . '.' . $key);
    }

    public function setComponentMeta($component, $key, $value): void
    {
        Arr::set($component->meta, static::class . '.' . $key, $value);
    }

    public function getComponentMeta($component, $key, $default = null)
    {
        return Arr::get($component->meta, static::class . '.' . $key, $default);
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function enabled(bool $enabled = true): static
    {
        $this->disabled = ! $enabled;

        return $this;
    }

    public function afterValueCallback(Closure $afterCallback): void
    {
        $callback = $this->valueCallback;

        $this->valueCallback = $callback
            ? fn ($value, ...$args) => $afterCallback($callback($value, ...$args))
            : $afterCallback;
    }

    public function applyAfterUpdatedStore($store, $key, $value, $form)
    {
        $this->dirty = true;

        if (! is_callable($this->afterUpdatedStore)) {
            return $store;
        }

        return ($this->afterUpdatedStore)($store, $key, $value, $form, $this);
    }

    public function storeValue($key, $default = null)
    {
        $prefix = $this->storePrefix;
        $prefix .= $prefix ? '.' : '';

        $store = move_arr_expand($this->resource->store);

        $storeKey = Str::after($this->storePrefix . '.' . $key, $this->defaultStorePrefix . '.');

        return $this->dirty
            ? Arr::get($store, $storeKey, $default)
            : Arr::get($this->resource, $prefix . $key, $default);
    }

    /**
     * This is only used for array data.
     * For example if the attribute is store.meta.items.0.key
     * This will be return the next supposed storePrefix: store.meta.items.1
     *
     * @return \Illuminate\Support\Stringable
     */
    public function nextItemStorePrefix()
    {
        $number = (int) ((string) Str::of($this->storePrefix)->afterLast('.') ?? null);

        return Str::of($this->storePrefix)
            ->before((string) $number)
            ->append($number + 1);
    }

    public function hasBefore(): bool
    {
        return (bool) $this->before;
    }

    public function getBefore(...$args)
    {
        return is_callable($this->before)
            ? ($this->before)($this, ...$args)
            : $this->before;
    }

    public function sortableCallback(\Closure $sortableCallback): static
    {
        $this->sortableCallbacks[] = $sortableCallback;

        return $this;
    }

    public function getSortableCallbacks(): array
    {
        return $this->sortableCallbacks;
    }
}
