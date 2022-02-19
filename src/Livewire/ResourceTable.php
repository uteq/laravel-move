<?php

namespace Uteq\Move\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\WithPagination;
use Opis\Closure\SerializableClosure;
use Symfony\Component\HttpFoundation\Response;
use Uteq\Move\Concerns\HasParent;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Concerns\HasSelected;
use Uteq\Move\Concerns\WithClosures;
use Uteq\Move\Exceptions\ResourcesException;
use Uteq\Move\Facades\Move;
use Uteq\Move\Requests\ResourceIndexRequest;
use Uteq\Move\Support\Livewire\TableComponent;

class ResourceTable extends TableComponent
{
    use WithPagination {
        WithPagination::setPage as paginationSetPage;
        WithPagination::resolvePage as paginationResolvePage;
    }

    use HasResource;
    use HasSelected;
    use HasParent;
    use WithClosures;

    protected static $viewType = 'index';
    protected bool $keepRequestQuery = false;
    protected ?string $table;
    protected string $limit;
    protected $actionResult;
    protected $queryString = ['search', 'filter', 'order'];
    protected $crudBaseRoute = null;

    public string $view = 'move::livewire.resource-table';

    public $search;
    public $action = '-';
    public $showingAction = false;
    public $showingActionResult = false;
    public $showingDelete = [];
    public $sortable = false;
    public $error = null;
    public $hasError = false;
    public array $actionFields = [];
    public array $store = [];
    public array $meta = [];
    public array $route = [];
    public array $showFields = [];
    public array $hideFields = [];
    public array $customCollection = [];
    public string|null $showModal = null;
    public $disableDeleteFor;
    public $redirects;

    public $listeners = [
        'move::table:updated' => 'render',
        'closeModal' => 'closeModal',
    ];

    public $closures = ['disableDeleteFor', 'redirects'];

    public function mount(string $resource)
    {
        $this->resource = $resource;
        $this->crudBaseRoute ??= move()::getPrefix();

        if ($this->limit ?? null) {
            $this->filter['limit'] = $this->limit;
        }

        $this->initHasFilter();
        $this->initHasParent();
        $this->hydrate();
        $this->initializeWithPagination();
        $this->computeHasSelected();
        $this->serializeClosures();
        $this->sortable = $this->resource()::$sortable;
        $this->route = [
            'resource' => request()->route()->parameter('resource'),
            'model' => request()->route()->parameter('model'),
        ];

        $this->resource()->authorizeTo('viewAny');

        Move::registerTable($this);

        if (method_exists($this, 'init')) {
            app()->call([$this, 'init']);
        }
    }

    public function closeModal()
    {
        $this->confirmingDestroy = null;
        $this->showModal = null;
    }

    public function resolvePage()
    {
        return $this->resource && $this->keepRequestQuery
            ? session(static::class . '.' . $this->resource . '.page')
            : $this->paginationResolvePage();
    }

    public function setPage($page)
    {
        $this->paginationSetPage($page);

        if ($this->resource && $this->keepRequestQuery) {
            session()->put(static::class . '.' . $this->resource . '.page', $page);
        }
    }

    public function resetPage()
    {
        $this->setSelected();
    }

    public function hydrate()
    {
        $this->table = get_class($this->resource()->resource);
        $this->keepRequestQuery = $this->resource()::$keepRequestQuery;
    }

    public function showDelete()
    {
        $this->showingDelete = true;
    }

    public function handleDelete()
    {
        $handler = $this
            ->resource()
            ->handler('delete')
            ?: fn ($item) => $item->delete();

        $this->selectedCollection()->each($handler);

        $this->showingDelete = false;
        $this->selected = [];
        $this->has_selected = false;
        $this->resetFilter();

        app()->call([$this, 'render']);
    }

    public function handleAction()
    {
        try {
            /** @psalm-suppress InvalidArgument */
            $result = app()->call([$this->action(), 'handleLivewireRequest'], [
                'resource' => $this->resource(),
                'collection' => $this->selectedCollection(),
                'actionFields' => $this->store,
            ]);
        } catch (ResourcesException $e) {
            $this->hasError = true;
            $this->error = $e->getMessage();
        }

        $this->showAction(false);

        if ($result instanceof Response || is_string($result)) {
            $this->showingActionResult = true;
            $this->actionResult = $result;

            return null;
        }

        // This enables the action to perform its own logic after
        //  it was successful as if we were in a Livewire Component
        if (isset($result['handle']) && is_callable($result['handle'])) {
            $response = $result['handle']($this);

            if ($response instanceof Response) {
                return $response;
            }
        }

        if ($result instanceof View) {
            $this->showingActionResult = true;
            $this->actionResult = $result;
        }

        /** @psalm-suppress InvalidArgument */
        app()->call([$this, 'render']);
    }

    public function action()
    {
        return collect($this->actions())
            ->filter(fn ($action) => Str::slug($action->name) === $this->action)
            ->first();
    }

    public function showAction($showingAction = true)
    {
        $this->showingAction = $showingAction;
    }

    public function render(ResourceIndexRequest $request)
    {
        $request->route()->setParameter('resource', $this->route['resource']);
        $request->route()->setParameter('model', optional($this->route['model'])['id']);

        /** @psalm-suppress UndefinedInterfaceMethod */
        return view($this->view, array_merge($this->getParamsForIndex($request), [
            'collection' => $this->collection(),
            'rows' => $this->rows(),
            'actionResult' => $this->actionResult,
            'headerSlots' => $this->headerSlots(),
            'table' => $this,
        ]))->layout($this->resource()::$layout ?? Move::layout(), [
            'header' => $this->resource()->label(),
        ]);
    }

    public function updateOrder($order)
    {
        app()->call([$this->resource(), 'tableSort'], [
            'order' => $order,
        ]);
    }

    protected function rows()
    {
        $resourceClass = get_class($this->resource());

        if ($this->customCollection) {
            $data = Arr::get(
                $this->parent()->model()->toArray(),
                $this->customCollection['key'],
                []
            );

            $model = $this->resource()->model()->setRows($data);

            ray($data);

            $collection = $model::query()->get();

        }

        $rows = [];
        $collection ??= $this->collection();

        foreach ($collection as $item) {
            $resource = new $resourceClass($item);

            $rows[] = [
                'model' => $resource->model(),
                'fields' => $this->filterFields(
                    $this->resource()->resolveFields($resource->model(), 'index')
                ),
            ];
        }

        return $rows;
    }

    protected function headerSlots()
    {
        return $this->resource()->headerSlots($this);
    }

    public function redirects()
    {
        return $this->unserializeClosure('redirects');
    }

    /**
     * @return mixed
     */
    private function getParamsForIndex($request)
    {
        $paramsForIndex = $this->resource()->getForIndex($this->requestQuery(), $request);
        $paramsForIndex['header'] = $this->filterFields($paramsForIndex['header']);
        return $paramsForIndex;
    }

    public function filterFields($fields)
    {
        return collect($fields)
            ->when(count($this->showFields), fn($collection) => $collection->filter(fn($field) => in_array($field->attribute, $this->showFields, true)))
            ->when(count($this->hideFields), fn($collection) => $collection->filter(fn($field) => !in_array($field->attribute, $this->hideFields, true)))
            ->toArray();
    }
}
