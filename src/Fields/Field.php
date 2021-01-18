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
    public string $placeholder;
    public bool $clickable = false;

    /**
     * The field's resolved value.
     *
     * @var mixed
     */
    public $value;

    /** @var callable|Closure|null */
    protected $callableValue;

    /**
     * The callback to be used to resolve the field's display value.
     */
    public ?Closure $displayCallback = null;

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
    public Closure $fillCallback;

    /**
     * @var Closure[]
     */
    public array $beforeStore = [];

    /**
     * @var Closure[]
     */
    public array $afterStore = [];

    public string $store;

    public ?Closure $before = null;

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

    /**
     * Field constructor.
     */
    public function __construct(string $name, string $attribute = null, callable $callableValue = null)
    {
        $this->name = $name;
        $this->attribute = $attribute ?? Str::snake(Str::singular($name));
        $this->callableValue = $callableValue;
        $this->store = $this->storePrefix() . '.' . $attribute;
        $this->unique = Str::random(20);

        if (method_exists($this, 'init')) {
            /** @psalm-suppress InvalidArgument */
            app()->call([$this, 'init']);
        }
    }

    public function isPlaceholder(bool $value = true)
    {
        $this->isPlaceholder = $value;

        return $this;
    }

    public function storePrefix()
    {
        return 'store';
    }

    public function formAttribute($formAttribute)
    {
        $this->formAttribute = $formAttribute;

        return $this;
    }

    public function name()
    {
        return $this->name;
    }

    public function attribute()
    {
        return $this->attribute;
    }

    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    public function model()
    {
        return $this->formAttribute . '.' . $this->attribute;
    }

    /**
     * Resolve the field's value for display.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        $this->resource = $resource;

        $attribute = $attribute ?? $this->attribute;

        if (! $this->displayCallback) {
            $this->resolve($resource, $attribute);
        } else {
            tap(
                $this->value ?? $this->resolveAttribute($resource, $attribute),
                fn ($value) => $this->value = call_user_func($this->displayCallback, $value, $resource, $attribute)
            );
        }

        return $this;
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $this->resource = $resource;

        $attribute = $attribute ?? $this->attribute;

        if (! $this->callableValue) {
            $this->value = $this->resolveAttribute($resource, $attribute);
        } elseif (is_callable($this->callableValue)) {
            tap(
                $this->resolveAttribute($resource, $attribute),
                fn ($value) => $this->value = call_user_func($this->callableValue, $value, $resource, $attribute)
            );
        }
    }

    protected function resolveAttribute($resource, $attribute)
    {
        return data_get($resource, str_replace('->', '.', $attribute));
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
    public function fillInto(Request $request, $model, $attribute, $requestAttribute = null)
    {
        $this->fillAttribute($request, $requestAttribute ?? $this->attribute, $model, $attribute);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  Request  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttribute(Request $request, $requestAttribute, $model, $attribute)
    {
        if (isset($this->fillCallback)) {
            call_user_func(
                $this->fillCallback,
                $request,
                $model,
                $attribute,
                $requestAttribute
            );

            return;
        }

        $this->fillAttributeFromRequest($request, $requestAttribute, $model, $attribute);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  Request  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(Request $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $value = $request[$requestAttribute];

            $model->{$attribute} = $this->isNullValue($value) ? null : $value;
        }
    }

    /**
     * Check value for null value.
     *
     * @param  mixed $value
     * @return bool
     */
    protected function isNullValue($value)
    {
        if (! $this->nullable) {
            return false;
        }

        return is_callable($this->nullValues)
            ? ($this->nullValues)($value)
            : in_array($value, (array) $this->nullValues);
    }

    /**
     * Specify a callback that should be used to hydrate the model attribute for the field.
     *
     * @param  Closure $fillCallback
     * @return $this
     */
    public function fillUsing($fillCallback)
    {
        $this->fillCallback = $fillCallback;

        return $this;
    }

    public function resourceUrl($resource)
    {
        $resource = Move::getByClass(get_class($resource));

        return route(move()::getPrefix() . '.edit', [
            'resource' => str_replace('.', '/', $resource),
            'model' => $this->value,
        ]);
    }

    public function clickable($clickable = true)
    {
        $this->clickable = is_callable($clickable) ? $clickable($this) : $clickable;

        return $this;
    }

    public function cleanModel(Model $model)
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

    public function key()
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

        if (! $this->isVisible($this->resource->store, $this->type)) {
            return null;
        }

        return view($this->folder . $displayType .'.' . $this->component, array_replace_recursive([
            'field' => $this,
        ], $data));
    }

    public function isVisible($resource, ?string $displayType = null)
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

    public function defaultDisplayType()
    {
        return $this->type ?? Str::afterLast(request()->route()->getName(), '.');
    }

    public function render()
    {
        return $this->view(...func_get_args());
    }

    public function beforeStore(Closure $beforeStore)
    {
        $this->beforeStore[] = $beforeStore;

        return $this;
    }

    public function afterStore(Closure $afterStore)
    {
        $this->afterStore[] = $afterStore;

        return $this;
    }

    public function handleBeforeStore($value, $field, $model, $data)
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
            ->filter(fn ($value, $field) => $value !== UnsetField::class)
            ->toArray();
    }

    public function handleAfterStore($value, $field, $model, $data)
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
            ->filter(fn ($field, $value) => $value !== UnsetField::class)
            ->toArray();
    }

    public function removeFromModel(\Closure $conditions = null)
    {
        $this->beforeStore[] = function ($value, $field, $model, $data) use ($conditions) {
            if ($conditions
                && ! ($conditions($value, $field, $model, $data))
            ) {
                return $value;
            }

            if (! isset($model[$this->attribute])) {
                return;
            }

            unset($model[$this->attribute]);
        };

        return $this;
    }

    public function onlyForValidation(\Closure $conditions = null)
    {
        $this->removeFromModel($conditions);

        return $this;
    }

    public function index($index)
    {
        $this->index = $index;

        return $this;
    }

    public function show($show)
    {
        $this->show = $show;

        return $this;
    }

    public function form($form)
    {
        $this->form = $form;

        return $this;
    }

    public function store($key = null, $default = null)
    {
        $store = $this->resource->store ?: $this->resource->getAttributes();

        if (empty($store)) {
            return $default;
        }

        return $key
            ? Arr::get($store[$this->attribute] ?? $default, $key, $default)
            : $store[$this->attribute] ?? $default;
    }

    public function before(Closure $before)
    {
        $this->before = $before;

        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder($resource)
    {
        return $this->placeholder ?? __('Add a :label', [
            'label' => lcfirst($resource->singularLabel()) . ' ' . lcfirst($this->name),
        ]);
    }
}
