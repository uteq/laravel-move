<?php

namespace Uteq\Move\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
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
    use AuthorizesRequests;

    public $action = '-';
    public $showingAction = false;
    public $showingDelete = false;
    public $sortable = false;
    public $error = null;
    public $hasError = false;
    public $requestQuery;
    protected ?string $table;
    protected string $limit;
    public array $actionFields = [];

    protected $queryString = ['search', 'filter', 'order'];

    protected $crudBaseRoute = 'move';

    public function mount(string $resource)
    {
        $this->resource = $resource;
        $this->filter['limit'] = $this->filter('limit', $this->resource()->defaultPerPage());
        $this->hydrate();
        $this->computeHasSelected();
        $this->requestQuery = request()->query();
        $this->sortable = $this->resource()::$sortable;
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
                'actionFields' => $this->actionFields,
            ]);
        } catch (ResourcesException $e) {
            $this->hasError = true;
            $this->error = $e->getMessage();
        }

        $this->showAction(false);

        // This enables the action to perform its own logic after
        //  it was successful as if we were in a Livewire Component
        if (isset($result['handle']) && is_callable($result['handle'])) {
            $response = $result['handle']($this);

            if ($response instanceof Response) {
                return $response;
            }
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
        ]))->layout(Move::layout(), [
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
