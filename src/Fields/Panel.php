<?php

namespace Uteq\Move\Fields;

use Closure;
use Illuminate\Http\Request;
use Uteq\Move\Concerns\AuthorizedToSee;
use Uteq\Move\Concerns\HasDependencies;
use Uteq\Move\Concerns\IsStacked;
use Uteq\Move\Concerns\Makeable;
use Uteq\Move\Concerns\Metable;
use Uteq\Move\Concerns\WithClosures;
use Uteq\Move\Concerns\WithRedirects;
use Uteq\Move\Contracts\ElementInterface;
use Uteq\Move\Contracts\PanelInterface;
use Uteq\Move\Fields\Concerns\ShowsConditionally;
use Uteq\Move\Fields\Concerns\WithHelpText;
use Uteq\Move\Fields\Concerns\WithStackableFields;

class Panel implements PanelInterface, ElementInterface
{
    use AuthorizedToSee;
    use HasDependencies;
    use Makeable;
    use Metable;
    use ShowsConditionally;
    use WithRedirects;
    use WithStackableFields;
    use WithHelpText;
    use IsStacked;
    use WithClosures;

    public string $id;
    public ?string $name = null;
    public array $fields;
    public $panels;
    public string $nameOnCreate;
    public string $nameOnUpdate;
    public array $alert = [];
    public bool $isPlaceholder = false;
    public bool $sortable = false;
    public bool $withoutCard = false;
    public string $attributeName;

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
    public $class = null;
    public $description = null;

    protected $resourceForm;

    public bool $withoutTitle;
    public bool $overflowHidden = true;
    public string $flow;
    public Closure|string|null $afterTitle = null;
    public Closure|string|null $classes = null;
    protected string $unique;

    public int $order = 0;

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

    public static function id(): string
    {
        return encrypt(static::class);
    }

    public function nameOnCreate(string $nameOnCreate): static
    {
        $this->nameOnCreate = $nameOnCreate;

        return $this;
    }

    public function nameOnUpdate(string $nameOnUpdate): static
    {
        $this->nameOnUpdate = $nameOnUpdate;

        return $this;
    }

    public function resolveFields($resource): static
    {
        if ($this->name) {
            $this->name = isset($resource['id']) ? $this->nameOnUpdate : $this->nameOnCreate;
        }

        collect($this->fields)
            ->each(fn (ElementInterface $element) => $element->addDependencies($this->dependencies)
                ->applyResourceData($resource));

        return $this;
    }

    public function allFields(): array
    {
        return array_merge($this->fields, $this->flatPanelsFields());
    }

    public function flatPanelsFields($panels = null): array
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

    /**
     * @return bool
     */
    public function empty()
    {
        return ! count($this->fields) && ! count($this->panels);
    }

    public function alert($type, $description): static
    {
        $this->alert[$type] ??= [];
        $this->alert[$type][] = $description;

        return $this;
    }

    /**
     * @param $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
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
    public function setComponent(string $component): void
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

    public function applyResourceData($model, $resourceForm = null, $resource = null): static
    {
        $this->resourceForm = $resourceForm;
        $this->resource = $resource;
        $this->model = $model;

        return $this;
    }

    public function isVisible(array $resource, ?string $displayType = null)
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

    public function panels(): array
    {
        return $this->panels;
    }

    public function titleClass($class): static
    {
        $this->class = $class;

        return $this;
    }

    public function description($description): static
    {
        $this->description = $description;

        return $this;
    }

    public function afterTitle(Closure $afterTitle): static
    {
        $this->afterTitle = $afterTitle;

        return $this;
    }

    public function withoutCard(bool $withoutCard = true): static
    {
        $this->withoutCard = $withoutCard;

        return $this;
    }

    public function withoutTitle(bool $withoutTitle = true): static
    {
        $this->withoutTitle = $withoutTitle;

        return $this;
    }

    public function isPlaceholder(): static
    {
        $this->withoutCard();
        $this->withoutTitle();

        return $this;
    }

    public function classes($classes): static
    {
        $this->classes = $classes;

        return $this;
    }

    public function flow($flow): static
    {
        $this->flow = $flow;

        return $this;
    }

    public function getUnique(): string
    {
        return $this->unique;
    }

    public function inline(): static
    {
        $this->flow = 'row';

        $this->withoutTitle();
        $this->withoutCard();
        $this->stacked('bg-white w-full last:border-b-0 border-gray-100 mb-4');
        $this->stackFields();

        $this->withMeta([
            'help_text_location' => 'hidden',
        ]);

        return $this;
    }

    public function setFolder($folder): static
    {
        $this->folder = $folder;

        return $this;
    }

    public function overflowHidden($overflowHidden = true): static
    {
        $this->overflowHidden = $overflowHidden;

        return $this;
    }

    public function order(int $order): static
    {
        $this->order = $order;

        return $this;
    }
}
