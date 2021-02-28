<?php

namespace Uteq\Move\Query;

use Illuminate\Database\Eloquent\Builder;
use Uteq\Move\Status\TrashedStatus;

class ApplyTrashedConstraint
{
    /**
     * @param Builder $query
     * @param $trashedStatus
     * @return Builder
     */
    public function __invoke(Builder $query, $trashedStatus): Builder
    {
        return $trashedStatus ? [
            TrashedStatus::WITH => $query->withTrashed(),
            TrashedStatus::ONLY => $query->onlyTrashed(),
        ][$trashedStatus] ?? $query : $query;
    }
}
