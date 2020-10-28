<?php

namespace Uteq\Move\Livewire;

use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Uteq\Move\Facades\Move;
use Uteq\Move\Resource;

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
            ->map(function ($resource, $key) {
                $resourceModel = $resource::$model;

                $exists = Schema::hasTable((new $resourceModel)->getTable());

                if (! $exists) {
                    return null;
                }

                $query = $resource::buildIndexQuery(
                    request(),
                    ['search'],
                    $resourceModel::query(),
                    $this->search
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
