<?php

namespace Uteq\Move\Support\Livewire\Concerns;

use Uteq\Move\Concerns\HasSelected;

trait HasFilter
{
    use HasSelected;

    public $filter;
    public $has_filters = false;
    public $requestQuery = [];
    public $order = [];

    protected bool $filterResetPagination = true;

    public function initHasFilter()
    {
        $this->requestQuery = $this->requestQuery();
        $this->filter['limit'] = $this->filter('limit', $this->resource()->defaultPerPage());
        $this->filter = array_replace_recursive(($this->requestQuery['filter'] ?? []), request()->query('filter', $this->filter));
        $this->queryString = array_replace(session(static::class .'.queryString', []), $this->queryString ?? [], ['filter']);
        $this->has_filters = $this->activeFilters();
        $this->order = request()->query('order', $this->order);
    }

    private function requestQueryKey()
    {
        return static::class . '.'. get_class($this->resource()) . '.requestQuery';
    }

    public function requestQuery()
    {
        $query = request()->query();

        return array_replace(session($this->requestQueryKey(), $query), $query);
    }

    public function sort($field)
    {
        $order = $this->order[$field] ?? null;

        if (! $order) {
            $order = 'asc';
        } elseif ($order === 'asc') {
            $order = 'desc';
        } elseif ($order === 'desc') {
            $order = null;
        }

        $this->order = [$field => $order];

        $this->requestQuery['order'] = $this->order;
    }

    public function getSort($field)
    {
        return $this->order[$field] ?? null;
    }

    public function filter(string $key, $default = null)
    {
        return $this->filter[$key] ?? $default;
    }

    public function resetFilter()
    {
        $this->filter = [];
        $this->selected = [];
        $this->select_type = [];
        $this->has_selected = false;

        $this->requestQuery = request()->query();

        $this->keepRequestQuery ? session()->put($this->requestQueryKey(), $this->requestQuery) : null;
    }

    public function updatedFilter(string $filter, ?string $key): void
    {
        if ($this->filter[$key] === '') {
            unset($this->filter[$key]);
        }

        request()->query->set('filter', $this->filter);

        $this->has_filters = $this->activeFilters();

        if (method_exists($this, 'resetPage') && $this->filterResetPagination === true) {
            $this->resetPage();
        }

        $this->requestQuery = request()->query();

        $this->maybeKeepRequestQuery();

        $this->setPage(1);
    }

    public function updatedSearch()
    {
        $this->requestQuery = request()->query();

        $this->maybeKeepRequestQuery();
    }

    public function activeFilters()
    {
        return collect($this->filter)->filter(fn ($value) => '' !== $value)->count();
    }

    public function maybeKeepRequestQuery()
    {
        $this->keepRequestQuery ? session()->put($this->requestQueryKey(), $this->requestQuery) : null;
    }
}
