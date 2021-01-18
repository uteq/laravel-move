<?php

namespace Uteq\Move\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Uteq\Move\Query\ApplySoftDeleteConstraint;

trait PerformsQueries
{
    public static function buildIndexQuery(
        $request,
        $requestQuery,
        Builder $query,
        $search = '',
        array $filters = [],
        array $orderings = [],
        $withTrashed = ''
    ) {
        $query = static::initializeQuery($query, $search, $withTrashed);
        $query = static::applyFilters($requestQuery, $query, $filters);
        $query = static::applyOrderings($query, $orderings);
        $query = $query->tap(function ($query) use ($request) {
            static::indexQuery($request, $query->with(static::$with));
        });

        return $query;
    }

    /**
     * Builds an initial index query
     */
    protected static function initializeQuery(
        Builder $query,
        string $search,
        string $withTrashed
    ) {
        $softDeleteConstraintQuery = static::applySoftDeleteConstraint($query, $withTrashed);

        return empty(trim($search))
            ? $softDeleteConstraintQuery
            : static::applySearch($softDeleteConstraintQuery, $search);
    }

    /**
     * Adds a conditional with or without trashed constraint to the query
     *
     * @param $query
     * @param $withTrashed
     * @return Builder|mixed
     */
    protected static function applySoftDeleteConstraint($query, $withTrashed)
    {
        return static::softDeletes()
            ? (new ApplySoftDeleteConstraint)->__invoke($query, $withTrashed)
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

    public static function applyOrderings($query, array $orderings)
    {
        $orderings = array_filter($orderings);

        if (empty($orderings)) {
            return empty($query->getQuery()->orders)
                ? $query->latest($query->getModel()->getQualifiedKeyName())
                : $query;
        }

        foreach ($orderings as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query;
    }

    /**
     * Build an "index" query for the given resource.
     */
    public static function indexQuery(Request $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     */
    public static function detailQuery(Request $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     */
    public static function relatableQuery(Request $request, Builder $query): Builder
    {
        return $query;
    }
}
