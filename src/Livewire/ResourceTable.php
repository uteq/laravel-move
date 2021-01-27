<?php

namespace Uteq\Move\Livewire;

use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;
use Uteq\Move\Concerns\HasResource;
use Uteq\Move\Concerns\HasSelected;
use Uteq\Move\Exceptions\ResourcesException;
use Uteq\Move\Facades\Move;
use Uteq\Move\Requests\ResourceIndexRequest;
use Uteq\Move\Support\Livewire\TableComponent;

class ResourceTable extends TableComponent
{
    use WithPagination;
    use HasResource;
    use HasSelected;

    protected static $viewType = 'index';

    public $action = '-';
    public $showingAction = false;
    public $showingActionResult = false;
    protected $actionResult;
    public $showingDelete = [];
    public $sortable = false;
    public $error = null;
    public $hasError = false;
    public $requestQuery;
    protected ?string $table;
    protected string $limit;
    public array $actionFields = [];
    public array $store = [];
    public array $meta = [];

    protected $queryString = ['search', 'filter', 'order'];

    protected $crudBaseRoute = null;

    public function mount(string $resource)
    {
        $this->crudBaseRoute ??= move()::getPrefix();

        $this->resource = $resource;
        $this->filter['limit'] = $this->filter('limit', $this->resource()->defaultPerPage());
        $this->hydrate();
        $this->computeHasSelected();
        $this->requestQuery = request()->query();
        $this->sortable = $this->resource()::$sortable;

        $this->resource()->authorizeTo('viewAny');
    }

    public function updateTaskOrder($order)
    {
        // TODO needs implementation
    }

    public function resetPage()
    {
        $this->setSelected();
    }

    public function hydrate()
    {
        $this->table = get_class($this->resource()->resource);
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
        /** @psalm-suppress UndefinedInterfaceMethod */
        return view('move::livewire.resource-table', array_merge($this->resource()->getForIndex($this->requestQuery), [
            'collection' => $this->collection(),
            'rows' => $this->rows(),
            'actionResult' => $this->actionResult,
        ]))->layout($this->resource()::$layout ?? Move::layout(), [
            'header' => $this->resource()->label(),
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
