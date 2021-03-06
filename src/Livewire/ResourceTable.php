<?php

namespace Uteq\Move\Livewire;

use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;
use Uteq\Move\Concerns\HasParent;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Concerns\HasSelected;
use Uteq\Move\Exceptions\ResourcesException;
use Uteq\Move\Facades\Move;
use Uteq\Move\Requests\ResourceIndexRequest;
use Uteq\Move\Support\Livewire\TableComponent;

class ResourceTable extends TableComponent
{
    use WithPagination {
        setPage as paginationSetPage;
        resolvePage as paginationResolvePage;
    }
    use HasResource;
    use HasSelected;
    use HasParent;
//    use WithActionableFields;

    protected static $viewType = 'index';
    protected bool $keepRequestQuery = false;
    protected ?string $table;
    protected string $limit;
    protected $actionResult;

    public $search;
    public $action = '-';
    public $showingAction = false;
    public $showingActionResult = false;
    public $showModal = null;
    public $showingDelete = [];
    public $sortable = false;
    public $error = null;
    public $hasError = false;
    public array $actionFields = [];
    public array $store = [];
    public array $meta = [];
    public array $route = [];

    public $listeners = [
        'move::table:updated' => 'render',
        'closeModal' => 'closeModal',
    ];

    protected $queryString = ['search', 'filter', 'order'];

    protected $crudBaseRoute = null;

    public function mount(string $resource)
    {
        $this->crudBaseRoute ??= move()::getPrefix();

        $this->resource = $resource;

        $this->initHasFilter();
        $this->initHasParent();
        $this->hydrate();
        $this->initializeWithPagination();
        $this->computeHasSelected();
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
        $handler = $this->resource()->handler('delete')
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
        return view('move::livewire.resource-table', array_merge($this->resource()->getForIndex($this->requestQuery(), $request), [
            'collection' => $this->collection(),
            'rows' => $this->rows(),
            'actionResult' => $this->actionResult,
            'headerSlots' => $this->resource()->headerSlots($this),
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
        $rows = [];
        foreach ($this->collection() as $item) {
            $resourceClass = get_class($this->resource());
            $resource = new $resourceClass($item);

            $fields = $this->resource()->resolveFields($resource->model(), 'index');

            $rows[] = ['model' => $resource->model(), 'fields' => $fields];
        }

        return $rows;
    }
}
