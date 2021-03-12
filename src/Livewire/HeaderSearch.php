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

    public function startSearch()
    {
        $this->showSearchResult = true;
    }

    public function stopSearch()
    {
        $this->search = null;
        $this->showSearchResult = false;
    }

    public function render()
    {
        return view(Move::headerSearch(), [
            'searchResult' => $this->searchResult(),
        ]);
    }

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

                return Schema::hasTable((new $resourceModel)->getTable());
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

    public function updatedShowSearchResult()
    {
        if (! $this->showSearchResult) {
            $this->search = null;
        }
    }
}
