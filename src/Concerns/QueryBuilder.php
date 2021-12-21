<?php

namespace Uteq\Move\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Uteq\Move\Query\ApplyTrashedConstraint;

trait QueryBuilder
{
    public static function buildIndexQuery(
        $request,
        $requestQuery,
        Builder $query,
        ?string $search = '',
        array $filters = [],
        array $orderBy = [],
        $trashedStatus = '',
        $resource = null
    ) {
        $query = static::defaultQuery($query, $search, $trashedStatus);
        $query = static::applyFilters($requestQuery, $query, $filters);
        $query = static::applyOrderBy($query, $orderBy);
        $query = $query->tap(fn ($query) => static::indexQuery($request, $query->with(static::$with), $resource));

        return $query;
    }

    protected static function defaultQuery(
        Builder $query,
        string $search,
        string $trashedStatus
    ) {
        $softDeleteConstraintQuery = static::applySoftTrashedConstraint($query, $trashedStatus);

        return empty(trim($search))
            ? $softDeleteConstraintQuery
            : static::applySearch($softDeleteConstraintQuery, $search);
    }

    /**
     * @param Builder $query
     * @param $trashedStatus
     *
     * @return Builder|mixed
     */
    protected static function applySoftTrashedConstraint(Builder $query, $trashedStatus)
    {
        return static::usesSoftDeletes()
            ? (new ApplyTrashedConstraint())->__invoke($query, $trashedStatus)
            : $query;
    }

    public static function applySearch($query, $search)
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->orWhere($query->getModel()->getQualifiedKeyName(), $search);

            foreach (static::searchableColumns() as $column) {
                $query->orWhere($query->getModel()->qualifyColumn($column), 'like', '%' . $search . '%');
            }
        });
    }

    public static function applyFilters($requestQuery, $query, array $filters)
    {
        collect($filters)->each->__invoke($requestQuery, $query);

        return $query;
    }

    public static function applyOrderBy($query, array $orderBy)
    {
        $orderBy = array_filter($orderBy);

        empty($orderBy)
            ? (
                $query = empty($query->getQuery()->orders)
                    ? $query->latest($query->getModel()->getQualifiedKeyName())
                    : $query
            )
            : collect($orderBy)
                ->each(fn ($order, $column) => $query->orderBy($column, $order));

        return $query;
    }

    public static function indexQuery(Request $request, Builder $query): Builder
    {
        return $query;
    }

    public static function detailQuery(Request $request, Builder $query): Builder
    {
        return $query;
    }

    public static function relatableQuery(Request $request, Builder $query): Builder
    {
        return $query;
    }
}
