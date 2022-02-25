<?php

namespace Uteq\Move\Fields;

use App\Admin\Move\Fields\Paragraph;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
use Uteq\Move\Concerns\WithClosures;
use Uteq\Move\Concerns\WithModal;
use Uteq\Move\Concerns\WithRedirects;
use Uteq\Move\Facades\Move;
use Livewire\CreateBladeView;

abstract class Field extends FieldElement
{
    use Macroable;
    use HasHelpText;
    use HasRules;
    use Sortable;
    use IsStacked;
    use HasDependencies;
    use HasRequired;
    use WithModal;
    use WithRedirects;

    public string $name;
    public ?string $indexName = null;
    public ?string $attribute;
    public ?string $type;
    public ?string $placeholder = null;
    public bool $clickable = false;
    public ?bool $wrapContent = null;

    /** @var mixed */
    public $value;

    /** @var ?Closure */
    protected $valueCallback;

    /** @var ?Closure */
    protected $defaultValueCallback;

    protected static array $cachedValue = [];

    /**
     * The callback to be used to resolve the field's display value.
     */
    protected ?Closure $resourceDataCallback = null;

    /**
     * Indicates if the field is nullable.
     */
    public bool $nullable = false;

    /**
     * Values which will be replaced to null.
     */
    public array $nullValues = [''];

    /**
     * The model associated with the field.
     */
    public Model $resource;

    /**
     * The validation rules for creation and updates.
     */
    public array $rules = [];

    /**
     * The validation rules for creation.
     */
    public array $creationRules = [];

    /**
     * The validation rules for updates.
     */
    public array $updateRules = [];

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

    protected ?Closure $afterUpdatedStore = null;

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

    public string $storePrefix;
    public string $defaultStorePrefix = 'store';

    protected $index = null;
    protected $show = null;
    protected $form = null;

    public bool $disabled = false;
    public bool $hideName = false;
    public bool $dirty = false;
    public bool $flex = true;

    /**
     * Field constructor.
     */
    public function __construct(string $name, string $attribute = null, callable $valueCallback = null)
    {
        $this->name = $name;
        $this->attribute = $attribute ?? Str::snake(Str::singular($name));
        $this->unique = Str::random(20);

        $this->withMeta([
            'display' => 'normal',
        ]);

        $this->valueCallback($valueCallback);
        $this->generateStoreAttribute();

        if (method_exists($this, 'init')) {
            /** @psalm-suppress InvalidArgument */
            app()->call([$this, 'init']);
        }
    }

    public function valueCallback(callable $valueCallback = null)
    {
        $this->valueCallback = $valueCallback;

        return $this;
    }

    public function generateStoreAttribute()
    {
        $this->store = $this->storePrefix() . '.' . $this->attribute;
    }

    public function default($value)
    {
        $this->defaultValueCallback = $value instanceof Closure
            ? $value
            : fn ($currentValue, $model, $attribute, $field) => $currentValue ?: $value;

        return $this;
    }

    public function isPlaceholder(bool $value = true): self
    {
        $this->isPlaceholder = $value;

        return $this;
    }

    public function setStorePrefix(string $storePrefix)
    {
        $this->storePrefix = $storePrefix;

        return $this;
    }

    public function updateStorePrefix(string $storePrefix)
    {
        $this->setStorePrefix($storePrefix);

        $this->generateStoreAttribute();
    }

    public function storePrefix(): string
    {
        return $this->storePrefix ?? $this->defaultStorePrefix;
    }

    public function formAttribute($formAttribute): self
    {
        $this->formAttribute = $formAttribute;

        return $this;
    }

    public function name(): string
    {
        if ($this->indexName) {
            return $this->indexName;
        }

        return $this->name;
    }

    public function attribute(): string
    {
        return $this->attribute;
    }

    public function type($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function model(): string
    {
        return $this->formAttribute . '.' . $this->attribute;
    }

    /**
     * Applies the resource data to the current field
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     */
    public function applyResourceData(
        $resource,
        $attribute = null
    ): self {
        $index = $this->attribute . '.' . md5($resource);

        $this->resource = $resource;

        // Makes sure the value is only checked once.
        //  Because this is a very expensive check.
        if (isset(static::$cachedValue[$index])) {

            $this->value = static::$cachedValue[$index];

            return $this;
        }

        $value = (function () {
            // Value is already set
            if ($this->value) {
                return $this->value;
            }

            $data = $this->resource->toArray();

            // From store
            if ($value = $this->getResourceStoreValue($data, $this->attribute)) {
                return $value;
            }

            // From resource
            if ($value = $this->getResourceAttributeValue($data, $this->attribute)) {
                return $value;
            }

            if (! $this->defaultValueCallback) {
                return null;
            }

            // Default value
            return ($this->defaultValueCallback)(
                null,
                $this->resource,
                $this->attribute,
                $this
            );
        })();

        $this->value = $this->valueCallback
             ? ($this->valueCallback)($value, $resource, $attribute, $this)
             : $value;

        static::$cachedValue[$index] = $this->value;

        return $this;

        $this->fillFromResource($resource, $defaultValue ?? null);

        return $this;

        dd($resource);


        if (! $this->value
            && ! Arr::has($resource->toArray(), $this->attribute)
        ) {
            $this->value = $this->applyValueCallback($resource);

            return $this;
        }

        $defaultValue = $this->applyValueCallback($resource);

        // TODO fix that when version switches the description switches too
        //  http://nathan.digipz.test/company/onboarding?filter[limit]=10
//        if ($defaultValue) {
//            $this->value = $defaultValue;
//
//            return $this;
//        }

        if (! empty($this->value)) {
            $this->fillFromResource($resource, $defaultValue ?? null);

            return $this;
        }

        $resourceValue = $this->getResourceAttributeValue($resource, $this->attribute);

        $value = $this->value = $resourceValue ?: $defaultValue ?? null;

        $this->resourceDataCallback
            ? tap(
            $this->value ?? $this->getResourceAttributeValue($resource, $this->attribute),
            fn ($value) => $this->value = call_user_func(
                $this->resourceDataCallback,
                $value,
                $resource,
                $this->attribute,
            )
        ) : $this->fillFromResource($resource, $defaultValue ?? null);

        if ($this->attribute === 'description') {
            dd($this->value);
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
    public function fillFromResource($resource, $defaultValue = null): void
    {
        $this->resource = $resource;

        $value = $this->getResourceAttributeValue($resource, $this->attribute) ?: $defaultValue;

        if ($this->attribute === 'description') {
            dd($value);
        }

        $this->value = $this->valueCallback
            ? tap($value, fn ($value) => call_user_func(
                $this->valueCallback,
                $value,
                $resource,
                $this->attribute,
                $this,
            ))
            : $value;
    }

    protected function applyValueCallback($resource, $value = null)
    {
        $this->value = $this->valueCallback
            ? ($this->valueCallback)($value, $resource, $this->attribute, $this)
            : $value;

        if (! $this->value) {
            $this->value = $this->valueCallback ? tap($value, fn ($value) => call_user_func(
                $this->valueCallback,
                $value,
                $resource,
                $this->attribute,
                $this,
            )) : $value;
        }

        return $this->value;
    }

    /**
     * @param $resource
     * @param $attribute
     * @return array|mixed
     */
    protected function getResourceStoreValue($resource, $attribute)
    {
        $attribute = $this->storePrefix() . '.' . $attribute;

        return $this->getResourceAttributeValue($resource, $attribute);
    }

    /**
     * @param $resource
     * @param $attribute
     * @return array|mixed
     */
    protected function getResourceAttributeValue($resource, $attribute)
    {
        $resource = is_array($resource) ? $resource : $resource->toArray();

        $attribute = str_replace('->', '.', $attribute);

        if ($value = data_get($resource, $attribute)) {
            return $value;
        }

        return Arr::get($resource, $attribute);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  Request  $request
     * @param  object  $model
     * @param  string  $attribute
     * @param  string|null  $requestAttribute
     * @return void
     */
    public function fillInto(Request $request, $model, $attribute, $requestAttribute = null): void
    {
        $this->fillAttribute($request, $requestAttribute ?? $this->attribute, $model, $attribute);
    }

    /**
     * @param Request $request
     * @param $requestAttribute
     * @param $model
     * @param $attribute
     */
    protected function fillAttribute(
        Request $request,
        $requestAttribute,
        $model,
        $attribute
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
    protected function fillAttributeFromRequest(Request $request, $requestAttribute, $model, $attribute): void
    {
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
    public function fillUsing(Closure $fillCallback): self
    {
        $this->fillCallback = $fillCallback;

        return $this;
    }

    public function resourceUrl($resource)
    {
        $resource = Move::getByClass(get_class($resource));

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

    public function clickable($clickable = true): self
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

    public function identification(): string
    {
        return static::class . '.' . $this->attribute;
    }

    public function view(string $displayTypeKey, array $data = [])
    {
        if ($this->isPlaceholder) {
            return null;
        }

        $this->type = $displayTypeKey;

        $displayType = $this->displayTypes[$displayTypeKey] ?? 'index';

        $data = array_replace_recursive([
            'field' => $this,
            'store' => $this->resource['store'] ?? null,
            'meta' => $this->meta,
            'showModal' => $this->showModal,
        ], $data);

        if (isset($this->{$displayType}) && null !== $this->{$displayType}) {
            $handler = $this->{$displayType};

            if ($handler instanceof View) {
                return $handler->with($data);
            }

            $view = is_callable($handler) ? $handler($this, $data) : $handler;

            if (is_string($view)) {
                $view = app('view')->make(CreateBladeView::fromString($view));
            }

            if (! $view) {
                return $handler($this, $data);
            }

            throw_unless($view instanceof View,
                new \Exception('"view" method on [' . get_class($this) . '] must return instance of [' . View::class . ']'));

            return $view->with($data);
        }

        if (! $this->isVisible($this->resourceStore(), $this->type)) {
            return null;
        }

        return view($this->folder . $displayType .'.' . $this->component, array_replace_recursive([
            'field' => $this,
        ], $data));
    }

    public function resourceStore()
    {
        if (! ($this->resource ?? null)) {
            return [];
        }

        return array_replace(
            $this->resource->toArray(),
            $this->resource->store ?? []
        );
    }

    public function isVisible($resource, ?string $displayType = null): bool
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

    public function beforeStore(Closure $beforeStore, $key = null): self
    {
        $key ? $this->beforeStore[$key] = $beforeStore
            : $this->beforeStore[] = $beforeStore;

        return $this;
    }

    public function afterStore(Closure $afterStore, $key = null): self
    {
        $key ? $this->afterStore[$key] = $afterStore
             : $this->afterStore[] = $afterStore;

        return $this;
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
            /** @psalm-suppress UnusedClosureParam */
            ->filter(fn ($value, $field) => $value !== UnsetField::class)
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
            /** @psalm-suppress UnusedClosureParam */
            ->filter(fn ($field, $value) => $value !== UnsetField::class)
            ->toArray();
    }

    public function removeFromModel(\Closure $conditions = null)
    {
        $this->beforeStore[] = function ($value, $field, $model, $data) use ($conditions) {
            if ($conditions && ! ($conditions($value, $field, $model, $data))) {
                return $value;
            }

            unset($model->{$field});

            return UnsetField::class;
        };

        return $this;
    }

    public function onlyForValidation(\Closure $conditions = null): self
    {
        $this->removeFromModel($conditions);

        return $this;
    }

    public function index($index): self
    {
        $this->index = $index;

        return $this;
    }

    public function customIndexName($index): self
    {
        $this->indexName = $index;

        return $this;
    }

    public function show($show): self
    {
        $this->show = $show;

        return $this;
    }

    public function form($form): self
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

        $attribute = ($this->storePrefix ?? false)
            ? Str::after($this->storePrefix . '.' . $this->attribute, 'store.')
            : $this->attribute;

        return $key
            ? Arr::get($store[$attribute] ?? $default, $key, $default)
            : Arr::get($store, $attribute, $default);
    }

    public function before(Closure $before): self
    {
        $this->before = $before;

        return $this;
    }

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder($resource): string
    {
        return $this->placeholder ?? __('Add a :label', [
            'label' => lcfirst($resource->singularLabel()) . ' ' . lcfirst($this->name),
        ]);
    }

    public function forgetComponentMeta($component, $key)
    {
        Arr::forget($component->meta, static::class . '.' . $key);
    }

    public function hasComponentMeta($component, $key)
    {
        return Arr::has($component->meta, static::class . '.' . $key);
    }

    public function setComponentMeta($component, $key, $value)
    {
        Arr::set($component->meta, static::class . '.' . $key, $value);
    }

    public function getComponentMeta($component, $key, $default = null)
    {
        return Arr::get($component->meta, static::class . '.' . $key, $default);
    }

    public function disabled(bool $disabled = true)
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function enabled(bool $enabled = true)
    {
        $this->disabled = ! $enabled;

        return $this;
    }

    public function afterUpdatedStore(Closure $closure)
    {
        $this->afterUpdatedStore = $closure;

        return $this;
    }

    public function applyAfterUpdatedStore($store, $key, $value, $form)
    {
        $this->dirty = true;

        if (! is_callable($this->afterUpdatedStore)) {
            return $store;
        }

        return ($this->afterUpdatedStore)($store, $key, $value, $form, $this);
    }

    public function hideName(bool $hideName = true)
    {
        $this->hideName = $hideName;

        return $this;
    }

    public function getName()
    {
        if ($this->hideName) {
            return null;
        }

//        if ($this->indexName) {
//            return $this->indexName;
//        }

        return $this->name;
    }

    public function storeValue($key, $default = null)
    {
        $storeKey = $this->storePrefix ?? $this->defaultStorePrefix;

        $prefix = $storeKey ? $storeKey . '.' : '';

        $store = $this->resource->toArray();

        return Arr::get($store, $prefix . $key);
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
            ->before($number)
            ->append($number + 1);
    }

    public function wrap($wrap = true): static
    {
        $this->wrapContent = $wrap;

        return $this;
    }

    public function shouldWrap(): bool
    {
        if ($this->wrapContent) {
            return true;
        }

        if (Move::getWrapTableContent()) {
            return true;
        }

        return false;
    }
}
