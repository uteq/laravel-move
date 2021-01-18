<?php

namespace Uteq\Move\Fields;

use Illuminate\Http\Request;
use Uteq\Move\Concerns\AuthorizedToSee;
use Uteq\Move\Concerns\HasDependencies;
use Uteq\Move\Concerns\Makeable;
use Uteq\Move\Concerns\Metable;
use Uteq\Move\Contracts\ElementInterface;
use Uteq\Move\Contracts\PanelInterface;
use Uteq\Move\Fields\Concerns\ShowsConditionally;

class Panel implements PanelInterface, ElementInterface
{
    use AuthorizedToSee;
    use HasDependencies;
    use Makeable;
    use Metable;
    use ShowsConditionally;

    public string $id;
    public ?string $name = null;
    public array $fields;
    public array $panels;
    public string $nameOnCreate;
    public string $nameOnUpdate;
    public array $alert = [];
    public bool $isPlaceholder = false;
    public bool $sortable = false;

    // Related models
    public $resouceForm;
    public $resource;
    public $model;

    /**
     * The assigned panel, if available.
     */
    public string $panel;

    public string $component = 'form.panel';
    public string $folder = 'move::';

    public function __construct(?string $name = null, array $fields = [])
    {
        $this->name = $name;
        $this->fields = $fields;

        if ($name) {
            $this->nameOnCreate = $name;
            $this->nameOnUpdate = $name;
        }

        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    public static function id()
    {
        return encrypt(static::class);
    }

    public function nameOnCreate(string $nameOnCreate)
    {
        $this->nameOnCreate = $nameOnCreate;

        return $this;
    }

    public function nameOnUpdate(string $nameOnUpdate)
    {
        $this->nameOnUpdate = $nameOnUpdate;

        return $this;
    }

    public function resolveFields($resource)
    {
        if ($this->name) {
            $this->name = isset($resource['id']) ? $this->nameOnUpdate : $this->nameOnCreate;
        }

        collect($this->fields)
            ->each(fn (ElementInterface $element) => $element->addDependencies($this->dependencies)
                ->resolveForDisplay($resource));

        return $this;
    }

    public function allFields(): array
    {
        return array_merge($this->fields, $this->flatPanelsFields());
    }

    public function flatPanelsFields($panels = null)
    {
        $panels ??= $this->panels;
        $fields = [];

        foreach ($panels as $panel) {
            if (count($panel->panels)) {
                $fields = array_merge($fields, $this->flatPanelsFields($panel->panels));
            }

            $fields = array_merge($fields, $panel->fields);
        }

        return $fields;
    }

    public function empty()
    {
        return ! count($this->fields) && ! count($this->panels);
    }

    public function alert($type, $description)
    {
        $this->alert[$type] ??= [];
        $this->alert[$type][] = $description;

        return $this;
    }

    public function render($model)
    {
        return view($this->folder . $this->component, [
            'panel' => $this,
            'model' => $model,
        ]);
    }

    /**
     * Determine if the element should be displayed for the given request.
     *
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        return $this->authorizedToSee($request);
    }

    /**
     * Sets the component name for the element
     *
     * @param string $component
     */
    public function setComponent(string $component)
    {
        $this->component = $component;
    }

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return $this->component;
    }

    public function resolveForDisplay($model, $resourceForm = null, $resource = null)
    {
        $this->resourceForm = $resourceForm;
        $this->resource = $resource;
        $this->model = $model;
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

    public function panels()
    {
        return $this->panels;
    }
}
