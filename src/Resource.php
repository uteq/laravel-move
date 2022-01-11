<?php

namespace Uteq\Move;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Uteq\Move\Concerns\GloballySearchable;
use Uteq\Move\Concerns\Metable;
use Uteq\Move\Concerns\QueryBuilder;
use Uteq\Move\Concerns\WithAuthorization;
use Uteq\Move\Contracts\ElementInterface;
use Uteq\Move\Contracts\PanelInterface;
use Uteq\Move\DomainActions\DeleteResource;
use Uteq\Move\DomainActions\StoreResource;
use Uteq\Move\Facades\Move;
use Uteq\Move\Fields\Field;
use Uteq\Move\Fields\Panel;
use Uteq\Move\Fields\Step;

abstract class Resource
{
    use QueryBuilder;
    use WithAuthorization;
    use Metable;
    use GloballySearchable;

    public static string $title = 'id';

    public static array $search = [];

    public static array $with = [];

    public static array $softDeletes = [];

    public static array $perPageOptions = [10, 25, 50, 100];

    public static ?int $defaultPerPage = null;

    public static bool $sortable = false;

    public static ?string $layout = null;

    public static bool $keepRequestQuery = false;

    /** All endpoints that will be satisfied */
    public static array $redirectEndpoints = [
        'create' => 'index',
        'update' => 'index',
        'cancel' => 'index',
    ];

    public static array $disabledTableActions = [];

    /**
     * Overwrite this to use your own action handlers
     * This can be useful for event sourcing.
     *
     * @var array|string[]
     */
    public static array $defaultActionHandlers = [
        'update' => StoreResource::class,
        'create' => StoreResource::class,
        'delete' => DeleteResource::class,
    ];

    public static string $group = 'Resources';

    public Model $resource;

    public array $fields;

    protected static $flatFields;
    protected static $allFields;

    public function __construct(Model $resource)
    {
        $this->resource = $resource;

        if (method_exists($this, 'initialize')) {
            app()->call([$this, 'initialize']);
        }
    }

    public static function newModel(): ?Model
    {
        $model = isset(static::$model) ? static::$model : null;

        return $model ? new $model : null;
    }

    public static function singularLabel(): string
    {
        return Str::singular(static::label());
    }

    public static function label(): string
    {
        return move_class_to_label(get_called_class());
    }

    public static function searchableColumns(): array
    {
        return empty(static::$search)
            ? [static::newModel()->getKeyName()]
            : static::$search;
    }

    public static function additionalInformation(Request $request): array
    {
        return [];
    }

    public static function defaultPerPage()
    {
        return static::$defaultPerPage ?: collect(static::$perPageOptions)->first();
    }

    public static function perPageOptions(): array
    {
        return static::$perPageOptions;
    }

    public static function uriKey(): string
    {
        return Str::plural(Str::kebab(class_basename(get_called_class())));
    }

    public static function newResource(): self
    {
        return new static(static::newModel());
    }

    public static function relationQuery(): Builder
    {
        /** @psalm-suppress UndefinedPropertyFetch */
        return static::$model::query();
    }

    public function isSoftDeleted(): bool
    {
        return static::usesSoftDeletes()
            && ! is_null($this->resource->{$this->resource->getDeletedAtColumn()});
    }

    public static function usesSoftDeletes()
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

    public function id()
    {
        return optional($this->resource)->getPrimaryKey();
    }

    public function model(): Model
    {
        return $this->resource;
    }

    public static function title($model)
    {
        return $model->{static::$title};
    }

    public function getForIndex($requestQuery, Request $request = null): array
    {
        if (! isset(static::$model)) {
            throw new \Exception(sprintf(
                '%s: The parameter public static $model should be defined on resource %s',
                __METHOD__,
                static::class,
            ));
        }

        $filter = $requestQuery['filter'] ?? [];

        return [
            'resource' => $this,
            'header' => $this->visibleFields('index'),
            'collection' => $this->buildIndexQuery(
                $request ?: request(),
                $filter,
                static::$model::query(),
                $filter['search'] ?? '',
                $this->filters() ?? [],
                $requestQuery['order'] ?? [],
                '',
                $this
            ),
        ];
    }

    public function getForDetail(): array
    {
        if (! isset(static::$model)) {
            throw new \Exception(sprintf(
                '%s: The parameter public static $model should be defined on resource %s',
                __METHOD__,
                static::class,
            ));
        }

        $filter = $requestQuery['filter'] ?? [];

        return [
            'resource' => $this,
            'header' => $this->visibleFields('detail'),
            'collection' => $this->buildIndexQuery(
                request(),
                $filter,
                static::$model::query(),
                $filter['search'] ?? '',
                $this->filters() ?? [],
                $requestQuery['order'] ?? [],
                '',
                $this,
            ),
        ];
    }

    public function handler($key, array $args = [])
    {
        return app()->make($this->getActionHandler($key)
            ?? (static::$defaultActionHandlers[$key] ?? null)
        , $args);
    }

    public function handleAction(string $key, Model $model, array $fields, string $from, array $args = [])
    {
        $handler = $this->handler($key, $args);

        $dtoMethod = 'from' . ucfirst(strtolower($from));

        if (method_exists($this, 'handle' . ucfirst($key))) {
            $fields = $this->handleFieldsBeforeStore($model, $fields, $this);

            $result = app()->call(
                [$this, 'handle' . ucfirst($key)],
                ['handler' => $handler, 'model' => $model, 'fields' => $fields, 'dtoMethod' => $dtoMethod]
            );

            $this->handleFieldsAfterStore($model, $fields, $this);

            return $result;
        }

        return null;
    }

    public function handleCreate($handler, $model, $fields, $dtoMethod)
    {
        return $handler($model, $this->toDataTransferObject($fields, $dtoMethod), $this);
    }

    public function handleUpdate($handler, $model, $fields, $dtoMethod)
    {
        return $handler($model, $this->toDataTransferObject($fields, $dtoMethod), $this);
    }

    public function handleFieldsBeforeStore(Model $model, array $data, Resource $resource)
    {
        $beforeStoreFields = collect($resource->getFields())
            ->filter(fn ($item) => isset($item->beforeStore));

        foreach ($beforeStoreFields as $beforeStoreField) {
            $value = $data[$beforeStoreField->attribute] ?? null;

            $data = $beforeStoreField->handleBeforeStore($value, $beforeStoreField->attribute, $model, $data);
        }

        return $data;
    }

    public function handleFieldsAfterStore(Model $model, array $data, Resource $resource)
    {
        $afterStoreFields = collect($resource->getFields())
            ->filter(fn ($item) => isset($item->afterStore));

        foreach ($data as $field => $value) {
            $afterStoreField = $afterStoreFields
                ->filter(fn (Field $item) => $item->attribute === $field)
                ->first();

            if (! $afterStoreField) {
                continue;
            }

            $data = $afterStoreField->handleAfterStore($value, $field, $model, $data);
        }

        return $data;
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

    public function resolveFields(Model $model = null, $type = null, $keepPlaceholder = false, array $fields = null)
    {
        if ($model && ! $model->exists) {
            $model = $this->resource && $this->resource->exists ? $this->resource : $model;
        } elseif (! $model) {
            $model = $this->resource;
        }

        $type = $type ?: (isset($model->id) ? 'edit' : 'create');

        $visibleFields = collect($fields ?: $this->getFields($model))
            ->filter(function (ElementInterface $field) use ($type, $model, $keepPlaceholder) {
                // Whenever the field is a placeholder, it should always be added whenever resolving the fields
                //  This way it can be added to the model data.
                if ($keepPlaceholder && $field->isPlaceholder) {
                    return true;
                }

                return $field->isShownOn($type, $model, request());
            })
            ->toArray();

        /** @var Field $field */
        $fields = [];
        foreach ($visibleFields as $field) {
            $field->applyResourceData($model);

            $fields[] = $field;
        }

        return $fields;
    }

    public function visibleFields($type = null, $model = null)
    {
        $model = $model ?: $this->resource;

        $type = $type ?: (isset($model->id) ? 'edit' : 'create');

        return collect($this->getFields())
            ->filter(fn (ElementInterface $element) => $element->isShownOn($type, $model, request()))
            ->filter(fn (ElementInterface $element) => ! ($element->isPlaceholder ?? false))
            ->toArray();
    }

    public function fieldsFromRecursive($fields)
    {
        $panelFields = [];

        foreach ($fields ?? [] as $field) {
            if ($field instanceof Panel) {
                $panelFields = array_merge(
                    $panelFields,
                    $this->fieldsFromRecursive($field->resolveFields($this->resource)->fields)
                );
            }

            if ($field instanceof Field) {
                $panelFields[] = $field;
            }
        }

        return $panelFields;
    }

    public function getFields($model = null)
    {
        // Ensures the resource has a valid model
        $this->resource = $model && ! $this->resource->exists ? $model : $this->resource;

        return $this->fieldsFromRecursive($this->allFields($model));
    }

    public function allFields($model = null)
    {
        return $this->fields($model);
    }

    public function steps()
    {
        return collect($this->allFields())
            ->filter(fn ($field) => $field instanceof Step);
    }

    /**
     * Overwrite this method to use your own panel
     *
     * @return Panel
     */
    public function mainPanel(): Panel
    {
        return new Panel();
    }

    public function panels($resourceForm, $resource, string $displayType)
    {
        $panels = collect($this->allFields())
            ->filter(fn ($field) => $field instanceof PanelInterface);

        $fields = collect($this->allFields())
            ->filter(fn ($field) => $field instanceof Field)
            ->toArray();

        if (count($fields)) {
            $panels->prepend(
                $this->mainPanel()
                    ->setFields($fields)
            );
        }

        $panels = $this->recursivePanels($panels, $resourceForm, $resource, $displayType);

        return $panels;
    }

    public function recursivePanels($panels, $resourceForm, $resource, $displayType)
    {
        foreach ($panels as &$panel) {
            if (! $panel instanceof PanelInterface) {
                continue;
            }

            $elements = $panel->fields;

            $panel->applyResourceData($this->resource, $resourceForm, $this);
            $panel->resolveFields($resource);

            $store = array_replace($resource->toArray(), $resource->store ?? []);

            $panel->fields = collect($elements)
                ->filter(fn ($field) => $field instanceof Field)
                ->filter(fn (Field $field) => $field->isVisible($store, $displayType))
                ->toArray();

            $panel->panels = collect($elements)
                ->filter(fn ($field) => $field instanceof PanelInterface)
                ->filter(fn (Panel $panel) => $panel->isVisible($store, $displayType))
                ->toArray();

            if (count($panel->panels)) {
                $panel->panels = $this->recursivePanels($panel->panels, $resourceForm, $resource, $displayType);
            }
        }

        return $panels;
    }

    public function fill(Model $model, array $data, $store)
    {
        $model->fill($data);

        $actions = method_exists($this, 'beforeStore') ? $this->beforeStore() : [];

        collect($actions)->each->__invoke($this, $model, $data, $store);

        return $model;
    }

    public function route()
    {
        return Move::resourceRoute(get_class($this));
    }

    public function fullRoute($action)
    {

    }

    public function icon()
    {
        return null;
    }

    public function name()
    {
        return (string) Str::of(static::class)
            ->afterLast('\\')
            ->lower()
            ->kebab()
            ->plural();
    }

    abstract public function fields();

    abstract public function filters();

    abstract public function actions();

    /**
     * Return a test store for the resource form.
     * This can be used for testing.
     * This adds the given values to the supplied fields
     *
     * ```
     * <?php
     *
     * $faker = new \Faker\Factory();
     *
     * [
     *     'name' => $faker->sentence(6),
     * ];
     *
     * @return array
     */
    public function testStore()
    {
        return [];
    }

    public function actionEnabled($type): bool
    {
        return ! in_array($type, static::$disabledTableActions);
    }

    public function headerSlots($resourceTable): array
    {
        return [];
    }

    public function redirects(): array
    {
        return static::$redirectEndpoints;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    public function getActionHandler($key)
    {
        return $this->getActionHandlers()[$key];
    }

    public function getActionHandlers()
    {
        return $this->actionHandlers ?? static::$defaultActionHandlers;
    }
}
