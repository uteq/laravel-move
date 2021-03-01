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
        switch ($trashedStatus) {
            case TrashedStatus::WITH:
                return $query->withTrashed();

            case TrashedStatus::ONLY:
                return $query->onlyTrashed();

            default:
                return $query;
        }
    }
}
