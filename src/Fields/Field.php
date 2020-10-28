<?php

namespace Uteq\Move\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
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
     * Field constructor.
     */
    public function __construct(string $name, string $attribute = null, callable $callableValue = null)
    {
        $this->name = $name;
        $this->attribute = $attribute;
        $this->callableValue = $callableValue;

        if (method_exists($this, 'init')) {
            /** @psalm-suppress InvalidArgument */
            app()->call([$this, 'init']);
        }
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

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return mixed
     */
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

        return route('move.edit', [
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

    public function view(array $data = [])
    {
        $defaultType = $this->type ?? Str::afterLast(request()->route()->getName(), '.');
        $type = [
            'update' => 'form',
            'create' => 'form',
            'form' => 'form',
            'index' => 'index',
            'show' => 'show',
        ][$defaultType] ?? null;

        if (! $this->areDependenciesSatisfied($this->resource)) {
            return null;
        }

        if (! $this->isShownOn($defaultType, $this->resource, request())) {
            return null;
        }

        return view('move::'. $type .'.' . $this->component, array_replace_recursive([
            'field' => $this,
        ], $data));
    }

    public function render()
    {
        return $this->view(...func_get_args());
    }
}
