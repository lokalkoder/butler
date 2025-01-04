<?php

namespace Lokal\Butler\Repositories\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class FilteringColumnsCriteria implements CriteriaInterface
{
    public function __construct(public array $query) {}

    /**
     * Apply criteria in query repository
     *
     * @param  string  $model
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
{
    return $model->where(function (Builder $query) {
        foreach ($this->query as $search) {
            $query->where(current($search), next($search), end($search));
        }

        return $query;
    });
}
}
