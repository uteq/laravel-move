<?php

namespace Uteq\Move\Livewire;

use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Uteq\Move\Facades\Move;

class HeaderSearch extends Component
{
    public $showSearchResult = false;

    public $search = null;
    public $searchResult = [];

    protected $listeners = ['startSearch', 'stopSearch'];

    public function startSearch(): void
    {
        $this->showSearchResult = true;
    }

    public function stopSearch(): void
    {
        $this->search = null;
        $this->showSearchResult = false;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view(Move::headerSearch(), [
            'searchResult' => $this->searchResult(),
        ]);
    }

    /**
     * @return array|null
     *
     * @psalm-return array<empty, empty>|null
     */
    public function searchResult()
    {
        if (! $this->search) {
            return [];
        }

        $resources = collect(Move::all())
            // Only globally Searchable
            ->filter(fn ($resource) => $resource::$globallySearchable === true)
            ->filter(fn ($resource) => Move::resolveResource($resource)->can('viewAny'))
            ->filter(function ($resource) {
                $resourceModel = $resource::$model;

                return Schema::hasTable((new $resourceModel())->getTable());
            })
            ->map(function ($resource, $key) {
                $resourceModel = $resource::$model;

                $query = $resource::buildIndexQuery(
                    request(),
                    ['search'],
                    $resourceModel::query(),
                    $this->search,
                    [],
                    [],
                    '',
                    $resource
                )
                ->limit($resource::$globalSearchResults);

                if (! $query->count()) {
                    return null;
                }

                return [
                    'route' => str_replace('.', '/', $key),
                    'resource' => $resource,
                    'result' => $query->get(),
                ];
            })
            ->filter(fn ($resource) => $resource);

        $this->searchResult = $resources->toArray();

        $this->showSearchResult = (strlen($this->search) > 0);
    }

    public function updatedShowSearchResult(): void
    {
        if (! $this->showSearchResult) {
            $this->search = null;
        }
    }
}
