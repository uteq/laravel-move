<?php

namespace Uteq\Move\TableFilters;

use Illuminate\Database\Eloquent\Builder;
use Uteq\Move\Filters\Filter;

class PaginationFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public string $component = 'select-filter';

    /** @var string */
    public string $name = 'Per pagina';

    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     */
    public function apply($query, $value, $request): Builder
    {
        return $query;
    }

    public function default()
    {
        return '10';
    }

    /**
     * Get the filter's available options.
     *
     * @return array
     */
    public function options()
    {
        return [
            '10' => '10',
            '25' => '25',
            '50' => '50',
            '100' => '100',
        ];
    }
}
