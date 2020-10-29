<?php

namespace Uteq\Move;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Uteq\Move\Concerns\PerformsQueries;
use Uteq\Move\DomainActions\DeleteResource;
use Uteq\Move\DomainActions\StoreResource;
use Uteq\Move\Facades\Move;
use Uteq\Move\Fields\Field;

abstract class Resource
{
    use PerformsQueries;

    // TODO add all needed traits

    /** The single value that should be used to represent the resource when being displayed. */
    public static string $title = 'id';

    /** The columns that should be searched. */
    public static array $search = [];

    /** The relationships that should be eager loaded when performing an index query.. */
    public static array $with = [];

    /** The cached soft deleting statuses for various resources.*/
    public static array $softDeletes = [];

    /** The per-page options used at the resource index. */
    public static array $perPageOptions = [10, 25, 50, 100];

    /** The per-page options used at the resource index. */
    public static ?int $defaultPerPage = null;

    /** This enables that clicking the title will redirect to the show page. */
    public static bool $fastEdit = true;

    /** Indicates if the resource should be globally searchable. */
    public static bool $globallySearchable = true;

    /** The number of results to display in the global search. */
    public static int $globalSearchResults = 5;

    /** Where should the global search link to? */
    public static string $globalSearchLink = 'detail';

    /** All endpoints that will be satisfied */
    public static array $redirectEndpoints = [
        'create' => 'index',
        'update' => 'index',
        'cancel' => 'index',
    ];

    public static array $defaultActionHandlers = [
        'update' => StoreResource::class,
        'create' => StoreResource::class,
        'delete' => DeleteResource::class,
    ];

    /** The underlying model resource instance. */
    public Model $resource;

    /** @var string|null  */
    public static string $group = 'Resources';

    /**
     * Creates a new resource instance.
     */
    public function __construct(Model $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the searchable columns for the resource.
     */
    public static function searchableColumns(): array
    {
        return empty(static::$search)
            ? [static::newModel()->getKeyName()]
            : static::$search;
    }

    /**
     * Get a fresh instance of the model represented by the resource.
     */
    public static function newModel(): ?Model
    {
        if (! isset(static::$model)) {
            return null;
        }

        $model = static::$model;

        return new $model;
    }

    /**
     * Get the displayable singular label of the resource.
     */
    public static function singularLabel(): string
    {
        return Str::singular(static::label());
    }

    /**
     * Get the displayable label of the resource.
     */
    public static function label(): string
    {
        return Str::plural(Str::title(Str::snake(class_basename(get_called_class()), ' ')));
    }

    /**
     * Get meta information about this resource for client side comsumption.
     */
    public static function additionalInformation(Request $request): array
    {
        return [];
    }

    public static function defaultPerPage()
    {
        return static::$defaultPerPage ?: collect(static::$perPageOptions)->first();
    }

    /**
     * The pagination per-page options configured for this resource.
     */
    public static function perPageOptions(): array
    {
        return static::$perPageOptions;
    }

    /**
     * Get the URI key for the resource.
     */
    public static function uriKey(): string
    {
        return Str::plural(Str::kebab(class_basename(get_called_class())));
    }

    /**
     * Return a fresh resource instance.
     */
    protected static function newResource(): self
    {
        return new static(static::newModel());
    }

    /**
     * Determine if the resource is soft deleted.
     *
     * @return bool
     */
    public function isSoftDeleted()
    {
        return static::softDeletes()
            && ! is_null($this->resource->{$this->resource->getDeletedAtColumn()});
    }

    /**
     * Determine if this resource uses soft deletes.
     *
     * @return bool
     */
    public static function softDeletes()
    {
        if (! isset(static::$model)) {
            return false;
        }

        if (isset(static::$softDeletes[static::$model])) {
            return static::$softDeletes[static::$model];
        }

        return static::$softDeletes[static::$model] = in_array(
            SoftDeletes::class,
            class_uses_recursive(static::newModel())
        );
    }

    /**
     * Get the underlying model instance for the resource.
     */
    public function model(): Model
    {
        return $this->resource;
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title($model = null)
    {
        return ($model ?? $this->resource)->{static::$title};
    }

    public function getForIndex($requestQuery): array
    {
        if (! isset(static::$model)) {
            throw new \Exception(sprintf(
                '%s: The parameter public static $model should be defined on resource %s',
                __METHOD__,
                static::class,
            ));
        }

        $filter = $requestQuery['filter'] ?? [];

        $query = $this->buildIndexQuery(
            request(),
            $filter,
            static::$model::query(),
            $filter['search'] ?? '',
            $this->filters() ?? [],
            $requestQuery['order'] ?? [],
        );

        return [
            'resource' => $this,
            'header' => $this->visibleFields('index'),
            'collection' => $query,
        ];
    }

    public function getForDetail()
    {
    }

    public function handler($key)
    {
        return isset($this->actionHandlers[$key])
            ? new $this->actionHandlers[$key]
            : (
                isset(static::$defaultActionHandlers[$key])
                    ? new static::$defaultActionHandlers[$key]
                    : null
            );
    }

    public function handleAction(string $key, Model $model, array $fields, string $from)
    {
        $handler = $this->handler($key);

        $dtoMethod = 'from' . ucfirst(strtolower($from));

        if (method_exists($this, 'handle' . ucfirst($key))) {
            return app()->call(
                [$this, 'handle' . ucfirst($key)],
                ['handler' => $handler, 'model' => $model, 'fields' => $fields, 'dtoMethod' => $dtoMethod]
            );
        }

        return null;
    }

    public function handleCreate($handler, $model, $fields, $dtoMethod)
    {
        $data = collect($fields)
            ->mapWithKeys(fn ($value, $field) => [$field => $model->{$field}])
            ->toArray();

        return $handler($model, $this->toDataTransferObject($data, $dtoMethod), $this);
    }

    public function handleUpdate($handler, $model, $fields, $dtoMethod)
    {
        $data = collect($fields)
            ->mapWithKeys(fn ($value, $field) => [$field => $model->{$field}])
            ->toArray();

        return $handler($model, $this->toDataTransferObject($data, $dtoMethod), $this);
    }

    public function toDataTransferObject($data, $dtoMethod)
    {
        return isset($this->dataTransferObject)
            ? $this->dataTransferObject::$dtoMethod($data)
            : $data;
    }

    public function handleDelete($handler, $model)
    {
        return $handler($model);
    }

    public function resolveFields(Model $model = null, $type = null)
    {
        /** @var Field $field */
        $fields = [];
        foreach ($this->visibleFields($type, $model) as $field) {
            $field->resolveForDisplay($model ?: static::newModel());

            $fields[] = $field;
        }

        return $fields;
    }

    public function visibleFields($type = null, $model = null)
    {
        $model = $model ?: $this->resource;

        $type = $type ?: (isset($model->id) ? 'edit' : 'create');

        return collect($this->fields())
            ->filter(fn ($field) => $field->isShownOn($type, $model, request()))
            ->toArray();
    }

    public function fill(Model $model, array $data)
    {
        $model->fill($data);

        $actions = method_exists($this, 'beforeStore') ? $this->beforeStore() : [];

        collect($actions)->each->__invoke($this, $model, $data);

        return $model;
    }

    public function route()
    {
        return Move::resourceRoute(get_class($this));
    }

    public function icon()
    {
        return 'heroicon-o-home';
    }

    abstract public function fields();

    abstract public function filters();

    abstract public function actions();
}
